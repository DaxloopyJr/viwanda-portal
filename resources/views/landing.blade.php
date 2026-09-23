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
            --vp-navy: #1d2f6f;
            --vp-navy-2: #0f1c4e;
            --vp-navy-3: #16244f;
            --vp-gold: #ffc928;
            --vp-gold-dark: #e0a800;
            --vp-green: #16a34a;
            --vp-green-dark: #0f7a37;
            --vp-slate: #0f172a;
            --vp-slate-2: #475569;
            --vp-slate-3: #94a3b8;
            --vp-line: rgba(255,255,255,.14);
        }
        * { box-sizing: border-box; }
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: var(--vp-navy-2);
            color: #eef1fb;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ---- Full-screen hero background (auth-engine style) ---- */
        .lg-bg { position: fixed; inset: 0; z-index: -2; overflow: hidden; background: var(--vp-navy-2); }
        .lg-bg::before {
            content: ""; position: absolute; inset: 0;
            background-image: url('{{ asset('images/landing/bg-hero.jpg') }}');
            background-size: cover; background-repeat: no-repeat; background-position: center center;
        }
        .lg-bg::after {
            content: ""; position: absolute; inset: 0;
            background: linear-gradient(90deg, rgba(10,19,52,.38) 0%, rgba(10,19,52,.12) 46%, rgba(10,19,52,.05) 100%);
        }

        /* ---- Floating header ---- */
        .floating-header {
            position: fixed; top: 16px; left: 50%; transform: translateX(-50%);
            width: min(1180px, calc(100% - 32px)); z-index: 1030;
            background: rgba(255,255,255,.97); backdrop-filter: blur(12px);
            border-radius: 20px; box-shadow: 0 24px 64px rgba(0,0,0,.25), 0 4px 16px rgba(0,0,0,.12);
            padding: .55rem 1.25rem;
        }
        .floating-header .brand { line-height: 1.1; }
        .floating-header .brand img { height: 56px; width: 56px; object-fit: contain; }
        .floating-header .brand .default-emblem {
            height: 56px; width: 56px; border-radius: 14px; flex: none;
            background: linear-gradient(135deg, var(--vp-navy), var(--vp-navy-2));
            color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.25rem;
        }
        .floating-header .brand-name { font-weight: 800; color: var(--vp-navy); display: block; font-size: 1.25rem; }
        .floating-header .brand-slogan { display: block; font-size: .78rem; color: #64748b; letter-spacing: .04em; }
        .btn-login-pill {
            background: var(--vp-navy); color: #fff; border-radius: 12px;
            padding: .5rem 1.6rem; font-weight: 700; border: none; letter-spacing: .02em;
        }
        .btn-login-pill:hover { background: var(--vp-navy-3); color: #fff; }
        .lang-switch .dropdown-toggle {
            border-radius: 12px; border: 1.5px solid #e2e8f0; background: #fff; font-weight: 600; color: #334155;
        }
        .lang-switch .dropdown-menu { border-radius: 14px; box-shadow: 0 8px 24px rgba(0,0,0,.18); }

        /* ---- Hero: wording left ---- */
        .hero { padding: 150px 0 56px; }
        .hero .badge-pill {
            display: inline-flex; align-items: center; gap: .55rem;
            border: 1px solid var(--vp-line); border-radius: 999px;
            padding: .42rem 1.15rem; font-size: .72rem; letter-spacing: .18em; font-weight: 700;
            color: #dbe3f8; background: rgba(255,255,255,.07); backdrop-filter: blur(6px);
        }
        .hero .badge-pill .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--vp-gold); box-shadow: 0 0 12px var(--vp-gold); }
        .hero h1 { font-weight: 800; font-size: clamp(2rem, 4.4vw, 3.4rem); line-height: 1.1; color: #fff; text-shadow: 0 2px 18px rgba(6,12,38,.55); }
        .hero h1 .accent { color: var(--vp-gold); }
        .hero .lead { color: #c6d0ec; max-width: 540px; text-shadow: 0 1px 10px rgba(6,12,38,.5); }
        .hero .btn-hero {
            background: var(--vp-gold); color: var(--vp-navy-2); border-radius: 12px; font-weight: 800;
            padding: .8rem 2rem; letter-spacing: .04em; border: none;
            box-shadow: 0 12px 28px rgba(255,201,40,.28);
        }
        .hero .btn-hero:hover { background: var(--vp-gold-dark); color: #fff; }
        .hero .btn-ghost {
            border: 1px solid var(--vp-line); color: #eef1fb; border-radius: 12px; font-weight: 600;
            padding: .8rem 1.7rem; background: rgba(255,255,255,.08); backdrop-filter: blur(6px);
        }
        .hero .btn-ghost:hover { background: rgba(255,255,255,.16); color: #fff; }
        .hero .chips span {
            display: inline-block; border: 1px solid var(--vp-line); border-radius: 999px;
            padding: .42rem 1.05rem; margin: 0 .25rem .4rem 0; font-size: .8rem; color: #d4dcf3;
            background: rgba(255,255,255,.06); backdrop-filter: blur(6px);
        }
        .stats-strip .num { font-size: 1.9rem; font-weight: 800; color: #fff; }
        .stats-strip .num em { font-style: normal; color: var(--vp-gold); }
        .stats-strip .lbl { color: #aab6d8; font-size: .84rem; }

        /* ---- Overlapping channel cards (right column, white glass) ---- */
        .inst-stack { position: relative; }
        .inst-card {
            position: relative; width: 92%;
            background: rgba(255,255,255,.96); backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,.65); border-radius: 20px;
            box-shadow: 0 24px 64px rgba(4,10,34,.4), 0 4px 16px rgba(4,10,34,.18);
            padding: 1.5rem 1.6rem 1.25rem; transition: transform .28s ease, box-shadow .28s ease;
            cursor: pointer;
        }
        .inst-card.manual { z-index: 1; }
        .inst-card.api { z-index: 2; margin-top: -96px; margin-left: auto; }
        .inst-card:hover, .inst-card.open { transform: translateY(-6px) scale(1.015); box-shadow: 0 34px 84px rgba(4,10,34,.55); z-index: 5; }
        .inst-card .ic-head { display: flex; align-items: center; gap: 1rem; }
        .inst-card .ic-icon {
            height: 56px; width: 56px; border-radius: 16px; flex: none;
            display: inline-flex; align-items: center; justify-content: center; font-size: 1.6rem; color: #fff;
        }
        .inst-card.manual .ic-icon { background: linear-gradient(135deg, var(--vp-navy), var(--vp-navy-3)); }
        .inst-card.api .ic-icon { background: linear-gradient(135deg, var(--vp-green), var(--vp-green-dark)); }
        .inst-card h3 { letter-spacing: .1em; font-size: 1rem; font-weight: 800; color: var(--vp-slate); margin-bottom: .15rem; }
        .inst-card .ic-sub { color: var(--vp-slate-2); font-size: .82rem; }
        .inst-card .ic-count { margin-left: auto; text-align: center; }
        .inst-card .ic-count .n { font-size: 1.5rem; font-weight: 800; color: var(--vp-navy); line-height: 1; }
        .inst-card.api .ic-count .n { color: var(--vp-green-dark); }
        .inst-card .ic-count .t { font-size: .68rem; color: var(--vp-slate-3); letter-spacing: .06em; text-transform: uppercase; }
        .inst-card .ic-hint { font-size: .74rem; color: var(--vp-slate-3); margin-top: .8rem; }
        .inst-card .ic-hint i { transition: transform .25s; }
        .inst-card:hover .ic-hint i, .inst-card.open .ic-hint i { transform: rotate(180deg); }

        .inst-details { max-height: 0; overflow: hidden; transition: max-height .4s ease; }
        .inst-card:hover .inst-details, .inst-card.open .inst-details { max-height: 460px; }
        .inst-details .inst-row {
            display: flex; align-items: flex-start; gap: .9rem; padding: .8rem 0;
            border-top: 1px solid #e8edf5;
        }
        .inst-row:first-child { margin-top: .9rem; }
        .inst-row .code-badge {
            flex: none; background: rgba(29,47,111,.08); color: var(--vp-navy); border: 1px solid rgba(29,47,111,.28);
            border-radius: 8px; font-weight: 700; font-size: .8rem; padding: .3rem .6rem;
        }
        .inst-card.api .inst-row .code-badge { background: rgba(22,163,74,.09); color: var(--vp-green-dark); border-color: rgba(22,163,74,.32); }
        .inst-row .nm { font-weight: 700; color: var(--vp-slate); font-size: .92rem; }
        .inst-row .meta { color: var(--vp-slate-2); font-size: .78rem; }
        .inst-row .ds-chip {
            display: inline-block; border: 1px solid #dbe3ef; border-radius: 999px;
            padding: .12rem .6rem; font-size: .7rem; color: #52607a; margin: .15rem .15rem 0 0; background: #f8fafc;
        }
        .inst-card .cta {
            display: inline-block; background: var(--vp-navy); color: #fff; border-radius: 12px;
            padding: .5rem 1.4rem; font-weight: 700; text-decoration: none; font-size: .85rem; margin-top: 1rem;
        }
        .inst-card.api .cta { background: var(--vp-green); }
        .inst-card .cta:hover { filter: brightness(1.08); color: #fff; }
        .inst-empty { color: var(--vp-slate-3); font-size: .85rem; padding: .9rem 0 0; border-top: 1px solid #e8edf5; margin-top: .9rem; }

        /* ---- Info cards ---- */
        .info-card {
            background: rgba(255,255,255,.94); backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,.6); border-radius: 20px; padding: 1.5rem; height: 100%;
            box-shadow: 0 18px 44px rgba(4,10,34,.28);
        }
        .info-card i { font-size: 1.7rem; color: var(--vp-navy); }
        .info-card h5 { font-weight: 800; color: var(--vp-slate); }
        .info-card p { color: var(--vp-slate-2); font-size: .9rem; margin-bottom: 0; }

        footer.landing-footer { color: #c3cde8; text-shadow: 0 1px 8px rgba(6,12,38,.6); }

        @media (max-width: 991px) {
            .hero { padding: 132px 0 40px; }
            .inst-card { width: 96%; }
            .inst-card.api { margin-top: -64px; }
        }
        @media (max-width: 767px) {
            .floating-header .brand-slogan { display: none; }
        }
    </style>
</head>
<body>

{{-- Full-screen Tanzania industry & trade background --}}
<div class="lg-bg" aria-hidden="true"></div>

{{-- Floating header: logo + name + slogan, language switch and login only --}}
<header class="floating-header d-flex align-items-center justify-content-between">
    <a href="{{ route('landing') }}" class="brand d-flex align-items-center gap-2 text-decoration-none">
        @if($logoUrl)
            <img src="{{ $logoUrl }}" alt="{{ $siteName }}">
        @else
            <span class="default-emblem"><i class="bi bi-bank"></i></span>
        @endif
        <span>
            <span class="brand-name">{{ $siteName }}</span>
            <span class="brand-slogan d-none d-sm-block">{{ $tagline }}</span>
        </span>
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

{{-- Hero: wording on the left, overlapping channel cards on the right --}}
<section class="hero">
    <div class="container" style="max-width:1180px">
        <div class="row align-items-center g-5">

            <div class="col-lg-5">
                <span class="badge-pill"><span class="dot"></span>{{ __('MINISTRY OF INDUSTRY AND TRADE') }}</span>
                <h1 class="mt-4 mb-3">
                    {{ __('Reliable Industry & Trade Data') }}<br>
                    <span class="accent">{{ __('for a Growing Tanzania') }}</span>
                </h1>
                <p class="lead mb-4">{{ $tagline }} — {{ __('institutions report once, the Ministry and the public use trusted data everywhere.') }}</p>
                <div class="d-flex gap-3 flex-wrap mb-4">
                    <a href="{{ route('login') }}" class="btn btn-hero">{{ strtoupper(__('Sign in to the portal')) }} &nbsp;<i class="bi bi-arrow-right"></i></a>
                    <a href="#channels" class="btn btn-ghost">{{ __('Reporting channels') }}</a>
                </div>
                <div class="chips mb-4">
                    <span>{{ __('Data Catalogue') }}</span>
                    <span>{{ __('Approval Workflow') }}</span>
                    <span>{{ __('Reports & Analytics') }}</span>
                </div>
                <div class="stats-strip d-flex gap-4 gap-md-5">
                    <div><div class="num">{{ $stats['institutions'] }}<em>+</em></div><div class="lbl">{{ __('Institutions') }}</div></div>
                    <div><div class="num">{{ $stats['datasets'] }}<em>+</em></div><div class="lbl">{{ __('Datasets') }}</div></div>
                    <div><div class="num">{{ $stats['published'] }}<em>+</em></div><div class="lbl">{{ __('Published reports') }}</div></div>
                </div>
            </div>

            <div class="col-lg-7" id="channels">
                <div class="inst-stack">

                    {{-- DATA SUBMISSION: institutions reporting through the portal / CSV --}}
                    <div class="inst-card manual" tabindex="0">
                        <div class="ic-head">
                            <span class="ic-icon"><i class="bi bi-inbox"></i></span>
                            <div>
                                <h3>{{ __('DATA SUBMISSION') }}</h3>
                                <div class="ic-sub">{{ __('Portal & CSV reporting — institutions under the Ministry') }}</div>
                            </div>
                            <div class="ic-count">
                                <div class="n">{{ $manualInstitutions->count() }}</div>
                                <div class="t">{{ __('Institutions') }}</div>
                            </div>
                        </div>
                        <div class="ic-hint"><i class="bi bi-chevron-down me-1"></i>{{ __('Touch to view the institutions on this channel') }}</div>
                        <div class="inst-details">
                            @forelse($manualInstitutions as $institution)
                                <div class="inst-row">
                                    <span class="code-badge">{{ $institution->code }}</span>
                                    <div>
                                        <div class="nm">{{ $institution->name }}</div>
                                        <div class="meta">
                                            {{ $institution->contact_email ?? __('Ministry of Industry and Trade') }}
                                            &nbsp;·&nbsp; {{ $institution->datasets_count }} {{ __('Datasets') }}
                                        </div>
                                        <div>
                                            @foreach($institution->datasets->take(4) as $ds)
                                                <span class="ds-chip">{{ $ds->code }} · {{ ucfirst($ds->frequency) }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="inst-empty">{{ __('No institutions on this channel yet.') }}</div>
                            @endforelse
                        </div>
                        <a href="{{ route('login') }}" class="cta">{{ __('Submit Data') }} <i class="bi bi-arrow-right ms-1"></i></a>
                    </div>

                    {{-- API INTEGRATION: institutions submitting system-to-system --}}
                    <div class="inst-card api" tabindex="0">
                        <div class="ic-head">
                            <span class="ic-icon"><i class="bi bi-diagram-3"></i></span>
                            <div>
                                <h3>{{ __('API INTEGRATION') }}</h3>
                                <div class="ic-sub">{{ __('System-to-system reporting — institutions under the Ministry') }}</div>
                            </div>
                            <div class="ic-count">
                                <div class="n">{{ $apiInstitutions->count() }}</div>
                                <div class="t">{{ __('Institutions') }}</div>
                            </div>
                        </div>
                        <div class="ic-hint"><i class="bi bi-chevron-down me-1"></i>{{ __('Touch to view the institutions on this channel') }}</div>
                        <div class="inst-details">
                            @forelse($apiInstitutions as $institution)
                                <div class="inst-row">
                                    <span class="code-badge">{{ $institution->code }}</span>
                                    <div>
                                        <div class="nm">{{ $institution->name }}</div>
                                        <div class="meta">
                                            {{ $institution->contact_email ?? __('Ministry of Industry and Trade') }}
                                            &nbsp;·&nbsp; {{ $institution->datasets_count }} {{ __('Datasets') }}
                                        </div>
                                        <div>
                                            @foreach($institution->datasets->take(4) as $ds)
                                                <span class="ds-chip">{{ $ds->code }} · {{ ucfirst($ds->frequency) }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="inst-empty">{{ __('No institutions on this channel yet.') }}</div>
                            @endforelse
                        </div>
                        <a href="{{ route('login') }}" class="cta">{{ __('Get API Access') }} <i class="bi bi-arrow-right ms-1"></i></a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

{{-- Info cards --}}
<section class="pb-5 pt-3">
    <div class="container" style="max-width:1180px">
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
<script>
// Touch support: tap toggles the institution details (hover already covers pointers)
document.querySelectorAll('.inst-card').forEach(function (card) {
    card.addEventListener('click', function (e) {
        if (e.target.closest('a')) { return; }
        card.classList.toggle('open');
    });
});
</script>
</body>
</html>
