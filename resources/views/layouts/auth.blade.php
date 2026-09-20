<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — Viwanda Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #1a3c6e, #16304f); min-height: 100vh; display: flex; align-items: center; }
        .auth-card { max-width: 460px; width: 100%; margin: auto; border: none; border-radius: .75rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card auth-card shadow">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <h4 class="fw-bold" style="color:#1a3c6e">VIWANDA PORTAL</h4>
                    <p class="text-muted small mb-0">Ministry of Industry and Trade &mdash; Data Collection and Reporting</p>
                </div>
                @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
                @yield('content')
            </div>
        </div>
        <p class="text-center text-white-50 small mt-3">Authorized users only. All activity is audited.</p>
    </div>
</body>
</html>
