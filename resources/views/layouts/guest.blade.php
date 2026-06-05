<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') — {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4f46e5 100%);
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
        }
        .auth-card {
            background: #fff; border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0,0,0,.25);
            width: 100%; max-width: 420px; padding: 2.5rem 2rem;
        }
        .auth-brand { text-align: center; margin-bottom: 2rem; }
        .auth-brand .brand-icon {
            width: 60px; height: 60px; background: #4f46e5;
            border-radius: 1rem; display: flex; align-items: center;
            justify-content: center; margin: 0 auto 1rem;
        }
        .auth-brand h1 { font-size: 1.5rem; font-weight: 700; color: #1e293b; }
        .auth-brand p  { color: #64748b; font-size: .875rem; }
        .btn-primary { background: #4f46e5; border-color: #4f46e5; }
        .btn-primary:hover { background: #3730a3; border-color: #3730a3; }
        .form-control:focus { border-color: #4f46e5; box-shadow: 0 0 0 .2rem rgba(79,70,229,.15); }
        #toast-container {
            position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999;
            display: flex; flex-direction: column; gap: .5rem;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to   { transform: translateX(0);    opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="auth-card">
        @yield('content')
    </div>

    <div id="toast-container"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showToast(message, type = 'success') {
            const colors = { success:'#22c55e', danger:'#ef4444', warning:'#f59e0b', info:'#3b82f6' };
            const icons  = { success:'bi-check-circle-fill', danger:'bi-x-circle-fill', warning:'bi-exclamation-triangle-fill', info:'bi-info-circle-fill' };
            const id = 'toast-' + Date.now();
            const html = `
                <div id="${id}" style="background:#fff;border-radius:.6rem;padding:.75rem 1rem;box-shadow:0 4px 20px rgba(0,0,0,.12);display:flex;align-items:center;gap:.65rem;min-width:260px;max-width:360px;border-left:4px solid ${colors[type]};animation:slideIn .25s ease;">
                    <i class="bi ${icons[type]}" style="color:${colors[type]};font-size:1.1rem;flex-shrink:0"></i>
                    <span style="font-size:.875rem;color:#1e293b;flex:1">${message}</span>
                    <button onclick="document.getElementById('${id}').remove()" style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:1rem;padding:0 0 0 .5rem"><i class="bi bi-x"></i></button>
                </div>`;
            document.getElementById('toast-container').insertAdjacentHTML('beforeend', html);
            setTimeout(() => document.getElementById(id)?.remove(), 5000);
        }
        @if(session('toast_success'))
            showToast(@json(session('toast_success')), 'success');
        @endif
        @if(session('toast_danger'))
            showToast(@json(session('toast_danger')), 'danger');
        @endif
    </script>
</body>
</html>
