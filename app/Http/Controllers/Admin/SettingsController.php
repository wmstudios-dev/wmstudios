<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\ImageUploader;
use App\Support\SettingsSchema;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit()
    {
        return view('admin.settings', [
            'groups' => SettingsSchema::groups(),
            'values' => Setting::allValues(),
        ]);
    }

    public function update(Request $request)
    {
        $rules = [];

        foreach (SettingsSchema::keys() as $key) {
            $type = SettingsSchema::field($key)['type'];

            $rules[$key] = match ($type) {
                'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:12288'],
                'url' => ['nullable', 'url', 'max:500'],
                'textarea' => ['nullable', 'string', 'max:2000'],
                default => ['nullable', 'string', 'max:255'],
            };

            if ($type === 'image') {
                $rules[$key . '_remove'] = ['nullable', 'boolean'];
            }
        }

        $data = $request->validate($rules);

        foreach (SettingsSchema::keys() as $key) {
            $type = SettingsSchema::field($key)['type'];

            if ($type === 'image') {
                if ($request->hasFile($key)) {
                    ImageUploader::delete(Setting::get($key));
                    Setting::put($key, ImageUploader::store($request->file($key), 'settings'));
                } elseif ($request->boolean($key . '_remove')) {
                    ImageUploader::delete(Setting::get($key));
                    Setting::put($key, null);
                }

                continue;
            }

            $value = isset($data[$key]) ? trim($data[$key]) : null;

            if ($key === 'whatsapp' && $value) {
                $value = preg_replace('/\D+/', '', $value);
            }

            Setting::put($key, $value === '' ? null : $value);
        }

        return back()->with('success', 'Settings saved.');
    }
}
