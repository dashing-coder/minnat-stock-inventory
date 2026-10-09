<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login - Laravel ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center mt-5">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h3 class="mb-1">Laravel ERP</h3>
                    <p class="text-muted mb-4">Sign in with email or username.</p>
                    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
                    <form method="POST" action="{{ route('login.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Email / Username</label>
                            <input class="form-control" name="login" value="{{ old('login') }}" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input class="form-control" type="password" name="password" required>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        <button class="btn btn-primary w-100">Login</button>
                    </form>
                    <div class="small text-muted mt-3">Seed login: admin@example.com / ChangeMe123!</div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
