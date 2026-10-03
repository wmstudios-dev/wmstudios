@extends('admin.layout')

@section('title', 'Message from ' . $message->name)

@section('content')
<a href="{{ route('admin.messages.index') }}" class="text-sm font-medium text-muted hover:text-brand-600">&larr; Messages</a>
<h1 class="display mt-2 text-5xl">{{ $message->name }}</h1>
<p class="mt-1 text-sm text-muted">{{ $message->created_at->format('d M Y, H:i') }} · {{ strtoupper($message->locale) }}</p>

<div class="mt-6 grid gap-6 lg:grid-cols-3">
    <section class="rounded-3xl border border-line bg-white p-6 lg:col-span-2">
        <p class="whitespace-pre-line leading-relaxed text-ink/90">{{ $message->message }}</p>

        <div class="mt-8 flex flex-wrap gap-3 border-t border-line pt-6">
            <a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: your message to ' . config('app.name')) }}" class="btn-primary !py-2.5">Reply by email</a>
            @if($whatsappUrl)
                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="btn-ghost !py-2.5"><x-icon name="whatsapp" class="h-4 w-4" /> Reply on WhatsApp</a>
            @endif
        </div>
    </section>

    <aside class="space-y-4">
        <dl class="space-y-4 rounded-3xl border border-line bg-white p-6 text-sm">
            <div><dt class="text-xs font-semibold uppercase tracking-wider text-muted">Email</dt><dd class="mt-1 break-all font-medium"><a href="mailto:{{ $message->email }}" class="text-brand-600 hover:underline">{{ $message->email }}</a></dd></div>
            <div><dt class="text-xs font-semibold uppercase tracking-wider text-muted">Phone</dt><dd class="mt-1 font-medium">{{ $message->phone ?: '—' }}</dd></div>
            <div><dt class="text-xs font-semibold uppercase tracking-wider text-muted">Service</dt><dd class="mt-1 font-medium">{{ $message->service ?: '—' }}</dd></div>
            <div><dt class="text-xs font-semibold uppercase tracking-wider text-muted">Budget</dt><dd class="mt-1 font-medium">{{ $message->budget ? __('site.contact.budgets.' . $message->budget, [], $message->locale) : '—' }}</dd></div>
        </dl>

        <form method="POST" action="{{ route('admin.messages.update', $message) }}" class="rounded-3xl border border-line bg-white p-6">
            @csrf @method('PATCH')
            <label class="text-xs font-semibold uppercase tracking-wider text-muted">Status</label>
            <div class="mt-2 flex gap-2">
                <select name="status" class="field !py-2.5">
                    @foreach(\App\Models\ContactMessage::STATUSES as $s)
                        <option value="{{ $s }}" @selected($message->status === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button class="btn-dark !px-4 !py-2.5">Save</button>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" onsubmit="return confirm('Delete this message?')">
            @csrf @method('DELETE')
            <button class="w-full rounded-2xl border border-red-200 px-4 py-3 text-sm font-semibold text-red-600 transition-colors hover:bg-red-50">Delete message</button>
        </form>
    </aside>
</div>
@endsection
