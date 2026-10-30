@php
    $currentLocale = app()->getLocale();
@endphp

<div class="erp-language-switcher" role="group" aria-label="{{ __('Language') }}">
    <a href="{{ route('language.switch', 'en') }}"
       class="erp-language-option {{ $currentLocale === 'en' ? 'active' : '' }}"
       lang="en" hreflang="en"
       aria-current="{{ $currentLocale === 'en' ? 'true' : 'false' }}"
       title="English">
        <span class="erp-language-flag" aria-hidden="true">🇺🇸</span>
        <span>EN</span>
    </a>

    <a href="{{ route('language.switch', 'bn') }}"
       class="erp-language-option {{ $currentLocale === 'bn' ? 'active' : '' }}"
       lang="bn" hreflang="bn"
       aria-current="{{ $currentLocale === 'bn' ? 'true' : 'false' }}"
       title="বাংলা">
        <span class="erp-language-flag" aria-hidden="true">🇧🇩</span>
        <span>বাংলা</span>
    </a>
</div>

@once
<style>
.erp-language-switcher{display:inline-flex;align-items:center;gap:4px;padding:4px;border:1px solid #e2e8f0;border-radius:10px;background:#f8fafc;white-space:nowrap}
.erp-language-option{min-height:34px;display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:5px 10px;border-radius:7px;color:#64748b;font-size:12px;font-weight:700;line-height:1;text-decoration:none;transition:background .15s ease,color .15s ease,box-shadow .15s ease}
.erp-language-option:hover{color:#2563eb;background:#fff}
.erp-language-option.active{color:#fff;background:#2563eb;box-shadow:0 3px 8px rgba(37,99,235,.22)}
.erp-language-flag{font-size:15px;line-height:1}
@media(max-width:575px){.erp-language-option{padding-left:7px;padding-right:7px}.erp-language-option>span:last-child{display:none}.erp-language-flag{font-size:17px}}
</style>
@endonce
