<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
@php
    $budgets = ['lt5' => 'Under Rp 5 million', '5to15' => 'Rp 5 – 15 million', '15to50' => 'Rp 15 – 50 million', 'gt50' => 'Over Rp 50 million', 'unsure' => 'Not sure yet'];
    $rows = [
        'Name' => $contact->name,
        'Email' => $contact->email,
        'Phone' => $contact->phone ?: '-',
        'Service' => $contact->service ?: '-',
        'Budget' => $budgets[$contact->budget] ?? '-',
        'Language' => strtoupper($contact->locale),
    ];
@endphp
<body style="font-family: Arial, sans-serif; color: #0b1220; max-width: 540px; margin: 0 auto; padding: 24px;">
    <h1 style="font-size: 20px; color: #2e59bf; margin: 0 0 4px;">New message</h1>
    <p style="margin: 0 0 20px; color: #5b6477;">Someone just wrote to you through the website.</p>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        @foreach($rows as $label => $value)
            <tr>
                <td style="padding: 6px 0; width: 90px; color: #5b6477;">{{ $label }}</td>
                <td style="padding: 6px 0; font-weight: bold;">{{ $value }}</td>
            </tr>
        @endforeach
    </table>

    <p style="color: #5b6477; margin: 0 0 4px;">Message</p>
    <p style="margin: 0; white-space: pre-line; background: #f5f7fb; padding: 14px 16px; border-radius: 10px;">{{ $contact->message }}</p>

    <p style="margin-top: 24px;">
        <a href="{{ route('admin.messages.show', $contact) }}" style="color: #2e59bf; font-weight: bold;">Open in the admin inbox</a>
    </p>
    <p style="color: #5b6477; font-size: 13px;">Reply to this email to answer {{ $contact->name }} directly.</p>
</body>
</html>
