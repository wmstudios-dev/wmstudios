<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AdminResources;
use App\Support\ImageUploader;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * One controller for every content type described in App\Support\AdminResources.
 */
class ResourceController extends Controller
{
    public function index(Request $request, string $resource)
    {
        $cfg = AdminResources::get($resource);
        $query = $cfg['model']::query();

        if ($request->filled('q') && ! empty($cfg['search'])) {
            $term = '%' . trim($request->q) . '%';
            $query->where(function ($q) use ($cfg, $term) {
                foreach ($cfg['search'] as $field) {
                    $q->orWhere($field, 'like', $term);
                }
            });
        }

        foreach ($cfg['order'] ?? [['id', 'desc']] as [$column, $direction]) {
            $query->orderBy($column, $direction);
        }

        $items = $query->paginate(20)->withQueryString();

        return view('admin.resource.index', compact('resource', 'cfg', 'items'));
    }

    public function create(string $resource)
    {
        $cfg = AdminResources::get($resource);
        $item = new ($cfg['model'])();

        foreach ($cfg['fields'] as $field) {
            if (array_key_exists('default', $field)) {
                $item->{$field['name']} = $field['default'];
            }
        }

        return view('admin.resource.form', compact('resource', 'cfg', 'item'));
    }

    public function store(Request $request, string $resource)
    {
        $cfg = AdminResources::get($resource);
        $data = $request->validate($this->rules($cfg, null));

        $item = new ($cfg['model'])();
        $this->fill($item, $cfg, $request, $data, creating: true);
        $item->save();
        $this->syncGalleries($item, $cfg, $request);

        return redirect()->route('admin.resource.index', $resource)
            ->with('success', ucfirst($cfg['singular']) . ' added.');
    }

    public function edit(string $resource, int $id)
    {
        $cfg = AdminResources::get($resource);
        $item = $cfg['model']::findOrFail($id);

        return view('admin.resource.form', compact('resource', 'cfg', 'item'));
    }

    public function update(Request $request, string $resource, int $id)
    {
        $cfg = AdminResources::get($resource);
        $item = $cfg['model']::findOrFail($id);
        $data = $request->validate($this->rules($cfg, $item));

        $this->fill($item, $cfg, $request, $data, creating: false);
        $item->save();
        $this->syncGalleries($item, $cfg, $request);

        return redirect()->route('admin.resource.index', $resource)
            ->with('success', ucfirst($cfg['singular']) . ' updated.');
    }

    public function destroy(string $resource, int $id)
    {
        $cfg = AdminResources::get($resource);
        $item = $cfg['model']::findOrFail($id);

        foreach ($cfg['fields'] as $field) {
            if ($field['type'] === 'image') {
                ImageUploader::delete($item->{$field['name']});
            } elseif ($field['type'] === 'gallery') {
                foreach ($item->{$field['relation']} as $photo) {
                    ImageUploader::delete($photo->path);
                }
            }
        }

        $item->delete();

        return redirect()->route('admin.resource.index', $resource)
            ->with('success', ucfirst($cfg['singular']) . ' deleted.');
    }

    /** Every form field -> a validation rule (bilingual fields expand to name_id / name_en). */
    private function rules(array $cfg, ?Model $existing): array
    {
        $rules = [];

        foreach ($cfg['fields'] as $f) {
            $required = ! empty($f['required']);

            foreach ($this->columns($f) as $column => $isPrimary) {
                $req = $required && $isPrimary ? 'required' : 'nullable';

                $rules[$column] = match ($f['type']) {
                    'text' => [$req, 'string', 'max:255'],
                    'textarea', 'lines' => [$req, 'string', 'max:30000'],
                    'number' => ['nullable', 'integer', 'between:-100000,100000'],
                    'select', 'icon' => [$req, Rule::in(array_keys($f['options']))],
                    'checkbox' => ['nullable', 'boolean'],
                    'multicheck' => ['nullable', 'array'],
                    'url' => ['nullable', 'url', 'max:500'],
                    'datetime' => ['nullable', 'date'],
                    'image' => [
                        ($required && ! ($existing && $existing->{$f['name']})) ? 'required' : 'nullable',
                        'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:12288',
                    ],
                    default => ['nullable'],
                };
            }

            if ($f['type'] === 'multicheck') {
                $rules[$f['name'] . '.*'] = [Rule::in(array_keys($f['options']))];
            }

            if ($f['type'] === 'image') {
                $rules[$f['name'] . '_remove'] = ['nullable', 'boolean'];
            }

            if ($f['type'] === 'gallery') {
                $rules[$f['name'] . '_new'] = ['nullable', 'array', 'max:' . ($f['max'] ?? 12)];
                $rules[$f['name'] . '_new.*'] = ['image', 'mimes:jpg,jpeg,png,webp,gif', 'max:12288'];
                $rules[$f['name'] . '_remove'] = ['nullable', 'array'];
                $rules[$f['name'] . '_remove.*'] = ['integer'];

                if (! empty($f['kinds'])) {
                    $kinds = implode(',', array_keys(\App\Models\WorkPhoto::KINDS));
                    $rules[$f['name'] . '_kind'] = ['nullable', 'array'];
                    $rules[$f['name'] . '_kind.*'] = ['in:' . $kinds];
                    $rules[$f['name'] . '_new_kind'] = ['nullable', 'in:' . $kinds];
                }
            }
        }

        return $rules;
    }

    /** column name => is it the primary (Indonesian) column; used for "required". */
    private function columns(array $field): array
    {
        if ($field['type'] === 'gallery') {
            return [];
        }

        if (! empty($field['bilingual'])) {
            return [$field['name'] . '_id' => true, $field['name'] . '_en' => false];
        }

        return [$field['name'] => true];
    }

    private function fill(Model $item, array $cfg, Request $request, array $data, bool $creating): void
    {
        foreach ($cfg['fields'] as $f) {
            if ($f['type'] === 'gallery') {
                continue;
            }

            if ($f['type'] === 'image') {
                $this->fillImage($item, $f, $request);
                continue;
            }

            foreach ($this->columns($f) as $column => $primary) {
                $value = $data[$column] ?? null;

                $item->{$column} = match ($f['type']) {
                    'checkbox' => $request->boolean($column),
                    'multicheck' => ($picked = array_values(array_intersect(array_keys($f['options']), (array) $request->input($column, [])))) ? implode(',', $picked) : null,
                    'number' => ($value === null || $value === '') ? ($column === 'sort_order' ? 0 : null) : (int) $value,
                    'datetime' => $value ? \Illuminate\Support\Carbon::parse($value) : null,
                    default => ($value === null || trim($value) === '') ? null : trim($value),
                };
            }

            // The site falls back to the other language, but required columns can not be empty:
            // if only one language was typed, use it for both.
            if (! empty($f['bilingual']) && ! empty($f['required'])) {
                $id = $f['name'] . '_id';
                $en = $f['name'] . '_en';
                $item->{$en} = $item->{$en} ?? $item->{$id};
            }
        }

        if ($creating && ! empty($cfg['slug_from'])) {
            $item->slug = $this->uniqueSlug($cfg['model'], (string) $item->{$cfg['slug_from']});
        }

        if ($cfg['model'] === \App\Models\Thought::class && $item->is_published && ! $item->published_at) {
            $item->published_at = now();
        }
    }

    private function fillImage(Model $item, array $field, Request $request): void
    {
        $name = $field['name'];

        if ($request->hasFile($name)) {
            ImageUploader::delete($item->{$name});
            $item->{$name} = ImageUploader::store($request->file($name), $field['folder'] ?? 'misc');
        } elseif ($request->boolean($name . '_remove')) {
            ImageUploader::delete($item->{$name});
            $item->{$name} = null;
        }
    }

    private function syncGalleries(Model $item, array $cfg, Request $request): void
    {
        foreach ($cfg['fields'] as $f) {
            if ($f['type'] !== 'gallery') {
                continue;
            }

            $relation = $f['relation'];

            foreach ((array) $request->input($f['name'] . '_remove', []) as $photoId) {
                $photo = $item->{$relation}()->whereKey($photoId)->first();

                if ($photo) {
                    ImageUploader::delete($photo->path);
                    $photo->delete();
                }
            }

            // New kind chosen for photos that are already there
            if (! empty($f['kinds'])) {
                foreach ((array) $request->input($f['name'] . '_kind', []) as $photoId => $kind) {
                    $item->{$relation}()->whereKey($photoId)->update(['kind' => $kind]);
                }
            }

            $next = (int) $item->{$relation}()->max('sort_order');
            $newKind = ! empty($f['kinds']) ? ($request->input($f['name'] . '_new_kind') ?: 'other') : null;

            foreach ((array) $request->file($f['name'] . '_new', []) as $file) {
                $item->{$relation}()->create([
                    'path' => ImageUploader::store($file, $f['folder'] ?? 'misc'),
                    'sort_order' => ++$next,
                ] + ($newKind ? ['kind' => $newKind] : []));
            }
        }
    }

    private function uniqueSlug(string $model, string $source): string
    {
        $base = Str::slug($source) ?: Str::random(8);
        $slug = $base;
        $i = 2;

        while ($model::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
