<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $siteName }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --vp-teal: #0e8a9c;
            --vp-teal-dark: #0b7285;
            --vp-blue: #1a73e8;
        }
        body { font-family: "Segoe UI", Arial, sans-serif; background: linear-gradient(160deg, #0e8a9c 0%, #0b7285 55%, #095b6b 100%); min-height: 100vh; }

        /* Floating pill header (EduSmart style) */
        .floating-header {
            position: fixed; top: 18px; left: 50%; transform: translateX(-50%);
            width: min(1140px, calc(100% - 32px)); z-index: 1030;
            background: rgba(255,255,255,.96); backdrop-filter: blur(8px);
            border-radius: 50px; box-shadow: 0 8px 30px rgba(9,91,107,.25);
            padding: .5rem 1.25rem;
        }
        .floating-header .brand { font-weight: 700; color: var(--vp-teal-dark); }
        .floating-header .brand img { height: 40px; width: 40px; object-fit: contain; }
        .floating-header .brand .default-emblem {
            height: 40px; width: 40px; border-radius: 50%;
            background: linear-gradient(135deg, var(--vp-teal), var(--vp-blue));
            color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem;
        }
        .btn-login-pill {
            background: var(--vp-blue); color: #fff; border-radius: 50px;
            padding: .5rem 1.75rem; font-weight: 600; border: none;
        }
        .btn-login-pill:hover { background: #1557b0; color: #fff; }
        .lang-switch .dropdown-toggle {
            border-radius: 50px; border: 1px solid #dbe4ea; background: #fff; font-weight: 600; color: #33414e;
        }
        .lang-switch .dropdown-menu { border-radius: 14px; box-shadow: 0 8px 24px rgba(0,0,0,.12); }

        /* Hero (GISP style) */
        .hero { padding: 150px 0 90px; color: #fff; }
        .hero .hero-logo { height: 150px; width: 150px; object-fit: contain; filter: drop-shadow(0 6px 18px rgba(0,0,0,.25)); }
        .hero .hero-emblem {
            height: 150px; width: 150px; border-radius: 50%;
            background: rgba(255,255,255,.14); border: 3px solid rgba(255,255,255,.5);
            display: inline-flex; align-items: center; justify-content: center; font-size: 4rem;
        }
        .hero h1 { font-weight: 800; font-size: clamp(2rem, 4.5vw, 3.4rem); line-height: 1.12; }
        .hero .lead { color: rgba(255,255,255,.85); }

        .feature-card {
            background: #fff; border-radius: 22px; padding: 1.75rem;
            box-shadow: 0 14px 40px rgba(9,91,107,.28); height: 100%;
        }
        .feature-card .icon-wrap { height: 84px; display: flex; align-items: center; justify-content: center; }
        .feature-card .icon-wrap i { font-size: 3rem; color: var(--vp-teal); }
        .feature-card h3 { letter-spacing: .12em; font-size: 1.05rem; font-weight: 700; color: #22303c; }
        .feature-card p { color: #5b6b7a; font-size: .92rem; }
        .feature-card .q-link {
            display: flex; align-items: center; gap: .5rem; padding: .65rem .9rem;
            border: 1px solid #e4ebf1; border-radius: 10px; margin-bottom: .55rem;
            color: #33414e; text-decoration: none; font-size: .9rem; transition: .15s;
        }
        .feature-card .q-link:hover { border-color: var(--vp-teal); color: var(--vp-teal-dark); background: #f2fafb; }
        .feature-card .cta {
            display: inline-block; background: var(--vp-teal); color: #fff; border-radius: 50px;
            padding: .55rem 1.6rem; font-weight: 600; text-decoration: none;
        }
        .feature-card .cta:hover { background: var(--vp-teal-dark); color: #fff; }

        .info-card {
            background: #fff; border-radius: 18px; padding: 1.5rem; height: 100%;
            box-shadow: 0 10px 30px rgba(9,91,107,.22);
        }
        .info-card i { font-size: 1.8rem; color: var(--vp-teal); }
        .info-card h5 { font-weight: 700; color: var(--vp-teal-dark); }
        .info-card p { color: #5b6b7a; font-size: .9rem; margin-bottom: 0; }

        .stats-strip { color: #fff; }
        .stats-strip .num { font-size: 2.2rem; font-weight: 800; }
        .stats-strip .lbl { color: rgba(255,255,255,.75); font-size: .9rem; }

        footer.landing-footer { color: rgba(255,255,255,.7); }
    </style>
</head>
<body>

{{-- Floating header: logo + name, language switch and login only --}}
<header class="floating-header d-flex align-items-center justify-content-between">
    <a href="{{ route('landing') }}" class="brand d-flex align-items-center gap-2 text-decoration-none">
        @if($logoUrl)
            <img src="{{ $logoUrl }}" alt="{{ $siteName }}">
        @else
            <span class="default-emblem"><i class="bi bi-building"></i></span>
        @endif
        <span class="d-none d-sm-inline">{{ $siteName }}</span>
    </a>
    <div class="d-flex align-items-center gap-2">
        <div class="dropdown lang-switch">
            <button class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-globe me-1"></i>{{ app()->getLocale() === 'sw' ? 'Kiswahili' : 'English' }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}" href="{{ route('locale.switch', 'en') }}">English</a></li>
                <li><a class="dropdown-item {{ app()->getLocale() === 'sw' ? 'active' : '' }}" href="{{ route('locale.switch', 'sw') }}">Kiswahili</a></li>
            </ul>
        </div>
        <a href="{{ route('login') }}" class="btn btn-login-pill">{{ __('Login') }}</a>
    </div>
</header>

{{-- Hero --}}
<section class="hero">
    <div class="container" style="max-width:1140px">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 text-center text-lg-start">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="hero-logo mb-4">
                @else
                    <span class="hero-emblem mb-4"><i class="bi bi-building"></i></span>
                @endif
                <h1 class="mb-3">{{ $siteName }}</h1>
                <p class="lead mb-4">{{ $tagline }}</p>
                <div class="stats-strip d-flex gap-5 justify-content-center justify-content-lg-start">
                    <div><div class="num">{{ $stats['institutions'] }}</div><div class="lbl">{{ __('Institutions') }}</div></div>
                    <div><div class="num">{{ $stats['datasets'] }}</div><div class="lbl">{{ __('Datasets') }}</div></div>
                    <div><div class="num">{{ $stats['published'] }}</div><div class="lbl">{{ __('Published reports') }}</div></div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="feature-card text-center">
                            <div class="icon-wrap"><i class="bi bi-inbox"></i></div>
                            <h3>{{ __('DATA SUBMISSION') }}</h3>
                            <p>{{ __('Institutions report datasets to the Ministry through portal forms or CSV file upload.') }}</p>
                            <div class="text-start">
                                <a href="{{ route('login') }}" class="q-link"><i class="bi bi-chevron-right"></i>{{ __('Do you want to submit institution data?') }}</a>
                                <a href="{{ route('login') }}" class="q-link"><i class="bi bi-chevron-right"></i>{{ __('Do you want to track your submission status?') }}</a>
                            </div>
                            <a href="{{ route('login') }}" class="cta mt-2">{{ __('Submit Data') }}</a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-card text-center">
                            <div class="icon-wrap"><i class="bi bi-diagram-3"></i></div>
                            <h3>{{ __('API INTEGRATION') }}</h3>
                            <p>{{ __('Institutions with digital systems submit data directly through the secure REST API.') }}</p>
                            <div class="text-start">
                                <a href="{{ route('login') }}" class="q-link"><i class="bi bi-chevron-right"></i>{{ __('Do you want an API access token?') }}</a>
                                <a href="{{ route('login') }}" class="q-link"><i class="bi bi-chevron-right"></i>{{ __('Do you want the dataset schema for integration?') }}</a>
                            </div>
                            <a href="{{ route('login') }}" class="cta mt-2">{{ __('Get API Access') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Info cards --}}
<section class="pb-5">
    <div class="container" style="max-width:1140px">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="info-card">
                    <i class="bi bi-journal-text"></i>
                    <h5 class="mt-2">{{ __('Data Catalogue') }}</h5>
                    <p>{{ __('Each institution reports against a pre-defined data catalogue configured for its mandate, ensuring consistent and validated data every period.') }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-card">
                    <i class="bi bi-diagram-2"></i>
                    <h5 class="mt-2">{{ __('Approval Workflow') }}</h5>
                    <p>{{ __('Data passes an internal institutional approval chain — officer, supervisor and accounting officer — before Ministry review and publication.') }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-card">
                    <i class="bi bi-bar-chart"></i>
                    <h5 class="mt-2">{{ __('Reports & Analytics') }}</h5>
                    <p>{{ __('Dashboards, compliance tracking and consolidated reports turn submitted data into decisions for the Ministry and stakeholders.') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="landing-footer text-center small pb-4">
    {{ __('Copyright') }} © {{ date('Y') }} {{ __('Ministry of Industry and Trade') }} | {{ $siteName }}
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
