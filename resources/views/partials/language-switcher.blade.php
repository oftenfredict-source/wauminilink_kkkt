@php
    $currentLocale = session('locale', app()->getLocale());
    $locales = [
        'en' => ['name' => 'English', 'flag' => '🇬🇧'],
        'sw' => ['name' => 'Kiswahili', 'flag' => '🇹🇿']
    ];
    // Ensure currentLocale is valid
    if (!isset($locales[$currentLocale])) {
        $currentLocale = config('app.locale', 'en');
    }
@endphp

<li class="nav-item dropdown me-2 me-md-3">
    <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: #ffffff !important;" title="Switch Language">
        <i class="fas fa-language me-1"></i>
        <span class="d-none d-md-inline" id="languageDisplay">{{ $locales[$currentLocale]['flag'] }} {{ strtoupper($currentLocale) }}</span>
        <span class="d-md-none" id="languageDisplayMobile">{{ $locales[$currentLocale]['flag'] }}</span>
    </a>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
        @foreach($locales as $locale => $info)
            <li>
                <a class="dropdown-item language-option {{ $currentLocale === $locale ? 'active' : '' }}" 
                   href="{{ route('language.switch', $locale) }}"
                   data-locale="{{ $locale }}">
                    <span class="me-2">{{ $info['flag'] }}</span>
                    {{ $info['name'] }}
                    @if($currentLocale === $locale)
                        <i class="fas fa-check ms-2 text-success"></i>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
</li>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update language display after page load
    const currentLocale = '{{ $currentLocale }}';
    const locales = {
        'en': { flag: '🇬🇧', name: 'English' },
        'sw': { flag: '🇹🇿', name: 'Kiswahili' }
    };
    
    if (locales[currentLocale]) {
        const displayEl = document.getElementById('languageDisplay');
        const displayMobileEl = document.getElementById('languageDisplayMobile');
        if (displayEl) {
            displayEl.textContent = locales[currentLocale].flag + ' ' + currentLocale.toUpperCase();
        }
        if (displayMobileEl) {
            displayMobileEl.textContent = locales[currentLocale].flag;
        }
        
        // Update active state in dropdown
        document.querySelectorAll('.language-option').forEach(option => {
            option.classList.remove('active');
            if (option.getAttribute('data-locale') === currentLocale) {
                option.classList.add('active');
                // Add checkmark if not present
                if (!option.querySelector('.fa-check')) {
                    const check = document.createElement('i');
                    check.className = 'fas fa-check ms-2 text-success';
                    option.appendChild(check);
                }
            } else {
                // Remove checkmark from other options
                const check = option.querySelector('.fa-check');
                if (check) check.remove();
            }
        });
    }
});
</script>


