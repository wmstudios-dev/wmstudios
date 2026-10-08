{{-- Structured data (JSON-LD) that tells search engines who the business is. Built only from what the admin has filled in. --}}
@php
    use App\Models\Setting;
    use App\Support\WhatsApp;

    $schemaPhone = WhatsApp::normalize(Setting::get('whatsapp'));
    $schemaAddress = trim((string) Setting::get('address_id', Setting::get('address', '')));
    $schemaHours = trim((string) Setting::get('hours_id', Setting::get('hours', '')));
    $sameAs = collect(['instagram', 'tiktok', 'youtube', 'linkedin', 'behance', 'facebook'])
        ->map(fn ($k) => Setting::get($k))->filter()->values()->all();

    $business = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'ProfessionalService',
        'name' => $siteName,
        'url' => url('/'),
        'logo' => $logo,
        'image' => asset('images/og-default.png'),
        'description' => strip_tags($tagline),
        'telephone' => $schemaPhone ? '+' . $schemaPhone : null,
        'email' => Setting::get('email') ?: null,
        'address' => $schemaAddress !== '' ? array_filter([
            '@type' => 'PostalAddress',
            'addressLocality' => stripos($schemaAddress, 'magelang') !== false ? 'Magelang' : null,
            'streetAddress' => stripos($schemaAddress, 'magelang') !== false && strlen($schemaAddress) <= 10 ? null : $schemaAddress,
            'addressCountry' => 'ID',
        ]) : null,
        'areaServed' => 'ID',
        'openingHoursSpecification' => preg_match('/24/', $schemaHours) ? [[
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            'opens' => '00:00',
            'closes' => '23:59',
        ]] : null,
        'sameAs' => $sameAs ?: null,
    ]);
@endphp
<script type="application/ld+json">{!! json_encode($business, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
