<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
<<<<<<< HEAD
    <title>{{ __('Sign In') }} | M/S Afrin Traders</title>

    <style>
        :root {
            --bg: #eef0f4;
            --card: #ffffff;
            --text: #17191f;
            --muted: #727889;
            --line: #e4e7ee;
            --primary: #7447f5;
            --primary-dark: #6538ec;
            --danger: #c83244;
            --shadow: 0 24px 70px rgba(29, 35, 54, .14);
=======
    <title>Sign In | M/S Afrin Traders</title>

    <style>
        :root {
            --brand: #2563eb;
            --brand-dark: #1d4ed8;
            --brand-soft: #eff6ff;
            --ink: #0f172a;
            --text: #334155;
            --muted: #64748b;
            --soft: #94a3b8;
            --line: #e2e8f0;
            --field: #f8fafc;
            --page: #f4f7fb;
            --white: #ffffff;
            --danger: #b91c1c;
            --danger-bg: #fef2f2;
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
        }

        * { box-sizing: border-box; }

        html, body { min-height: 100%; }

        body {
            margin: 0;
            min-height: 100vh;
<<<<<<< HEAD
            display: grid;
            place-items: center;
            padding: 32px 20px;
            background:
                radial-gradient(circle at 12% 10%, rgba(116, 71, 245, .07), transparent 28%),
                linear-gradient(135deg, #f5f6f8 0%, var(--bg) 100%);
            color: var(--text);
            font-family: Inter, ui-sans-serif, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        .login-shell {
            width: min(520px, 100%);
            overflow: hidden;
            background: var(--card);
            border: 1px solid rgba(20, 27, 45, .07);
            border-radius: 24px;
            box-shadow: var(--shadow);
        }

        .login-form-side {
            padding: 54px 58px 46px;
        }

        .login-form-wrap {
            width: 100%;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 42px;
            color: #22242a;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: -.02em;
        }

        .brand-mark {
            width: 12px;
            height: 18px;
            position: relative;
            display: inline-block;
        }

        .brand-mark::before,
        .brand-mark::after {
            content: "";
            position: absolute;
            left: 0;
            background: linear-gradient(180deg, #8c5cff 0%, #6738ed 100%);
        }

        .brand-mark::before {
            width: 9px;
            height: 13px;
            bottom: 0;
            border-radius: 4px 4px 1px 1px;
        }

        .brand-mark::after {
            width: 5px;
            height: 5px;
            left: 3px;
            top: 0;
            transform: rotate(45deg);
            border-radius: 1px;
        }

        .welcome {
            margin: 0;
            font-size: clamp(34px, 6vw, 44px);
            line-height: 1.05;
            letter-spacing: -.045em;
            font-weight: 800;
        }

        .welcome span { display: block; }

        .subtitle {
            margin: 16px 0 34px;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.65;
=======
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 50% -10%, rgba(37, 99, 235, .08), transparent 34%),
                linear-gradient(180deg, #f8fafc 0%, var(--page) 100%);
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }

        button, input { font: inherit; }

        .auth-shell {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 36px 20px;
        }

        .auth-card {
            width: min(438px, 100%);
            overflow: hidden;
            background: var(--white);
            border: 1px solid rgba(203, 213, 225, .72);
            border-radius: 18px;
            box-shadow:
                0 1px 2px rgba(15, 23, 42, .03),
                0 20px 55px rgba(15, 23, 42, .09);
        }

        .card-accent {
            height: 4px;
            background: linear-gradient(90deg, #2563eb 0%, #3b82f6 55%, #60a5fa 100%);
        }

        .card-body { padding: 34px 36px 30px; }

        .brand {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 30px;
        }

        .brand-main {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            flex: 0 0 42px;
            display: grid;
            place-items: center;
            color: #fff;
            background: linear-gradient(145deg, #1d4ed8, #2563eb);
            border-radius: 11px;
            box-shadow: 0 8px 20px rgba(37, 99, 235, .22);
        }

        .brand-mark svg { width: 21px; height: 21px; }

        .brand-name {
            margin: 0;
            color: var(--ink);
            font-size: 17px;
            font-weight: 760;
            line-height: 1.2;
            letter-spacing: -.02em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .brand-meta {
            margin-top: 3px;
            color: var(--soft);
            font-size: 11.5px;
            line-height: 1.35;
        }

        .secure-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            flex: 0 0 auto;
            padding: 6px 8px;
            color: #475569;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 650;
            white-space: nowrap;
        }

        .secure-badge svg {
            width: 12px;
            height: 12px;
            color: var(--brand);
        }

        .auth-heading { margin-bottom: 25px; }

        .auth-heading h1 {
            margin: 0;
            color: var(--ink);
            font-size: 27px;
            line-height: 1.18;
            font-weight: 770;
            letter-spacing: -.035em;
        }

        .auth-heading p {
            margin: 8px 0 0;
            color: var(--muted);
            font-size: 13.5px;
            line-height: 1.55;
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
        }

        .alert {
            display: flex;
<<<<<<< HEAD
            gap: 10px;
            align-items: flex-start;
            margin-bottom: 20px;
            padding: 12px 14px;
            border: 1px solid #f1c5cc;
            border-radius: 10px;
            background: #fff5f6;
            color: var(--danger);
            font-size: 13px;
            line-height: 1.45;
        }

        .alert-icon {
            width: 18px;
            height: 18px;
            flex: 0 0 18px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: var(--danger);
            color: #fff;
            font-size: 11px;
            font-weight: 800;
        }

        .field { margin-bottom: 17px; }

        .field label {
            display: block;
            margin-bottom: 8px;
            color: #383b45;
            font-size: 12px;
            font-weight: 700;
=======
            align-items: flex-start;
            gap: 9px;
            margin: 0 0 20px;
            padding: 11px 12px;
            color: var(--danger);
            background: var(--danger-bg);
            border: 1px solid #fecaca;
            border-radius: 9px;
            font-size: 12.5px;
            line-height: 1.45;
        }

        .alert svg {
            width: 17px;
            height: 17px;
            flex: 0 0 17px;
            margin-top: 1px;
        }

        .form-group { margin-bottom: 17px; }

        .form-label {
            display: block;
            margin: 0 0 7px;
            color: #1e293b;
            font-size: 12.5px;
            font-weight: 680;
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
        }

        .input-wrap { position: relative; }

<<<<<<< HEAD
        .control {
            width: 100%;
            height: 52px;
            border: 1px solid #d9dce4;
            border-radius: 9px;
            outline: 0;
            background: #fff;
            padding: 0 15px;
            color: #22252c;
            font: inherit;
            font-size: 14px;
            transition: border-color .18s ease, box-shadow .18s ease;
        }

        .control::placeholder { color: #a8adb8; }

        .control:focus {
            border-color: #936df7;
            box-shadow: 0 0 0 4px rgba(116, 71, 245, .10);
        }

        .password-control { padding-right: 52px; }
=======
        .input-icon {
            position: absolute;
            top: 50%;
            left: 13px;
            width: 17px;
            height: 17px;
            color: #94a3b8;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            height: 46px;
            padding: 0 42px 0 40px;
            color: var(--ink);
            background: var(--field);
            border: 1px solid #d6dee8;
            border-radius: 9px;
            outline: none;
            font-size: 13.5px;
            transition: background .16s ease, border-color .16s ease, box-shadow .16s ease;
        }

        .form-control:hover {
            background: #fff;
            border-color: #b8c4d3;
        }

        .form-control:focus {
            background: #fff;
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .10);
        }

        .form-control::placeholder { color: #a3afbf; }

        .form-control.is-invalid {
            border-color: #ef4444;
            background: #fff;
        }

        .field-error {
            margin-top: 6px;
            color: var(--danger);
            font-size: 11.5px;
            line-height: 1.4;
        }
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5

        .password-toggle {
            position: absolute;
            top: 50%;
<<<<<<< HEAD
            right: 10px;
            width: 34px;
            height: 34px;
            transform: translateY(-50%);
            display: grid;
            place-items: center;
            border: 0;
            border-radius: 7px;
            background: transparent;
            color: #858b99;
            cursor: pointer;
        }

        .password-toggle:hover {
            background: #f4f2fb;
            color: var(--primary);
        }

        .password-toggle svg { width: 18px; height: 18px; }
=======
            right: 7px;
            width: 34px;
            height: 34px;
            display: grid;
            place-items: center;
            padding: 0;
            color: #718096;
            background: transparent;
            border: 0;
            border-radius: 7px;
            cursor: pointer;
            transform: translateY(-50%);
        }

        .password-toggle:hover {
            color: var(--ink);
            background: #edf2f7;
        }

        .password-toggle svg { width: 17px; height: 17px; }
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
<<<<<<< HEAD
            gap: 16px;
            margin: 15px 0 30px;
=======
            gap: 12px;
            margin: 2px 0 20px;
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
        }

        .remember {
            display: inline-flex;
            align-items: center;
<<<<<<< HEAD
            gap: 9px;
            color: #656b78;
            font-size: 12px;
=======
            gap: 8px;
            color: #475569;
            font-size: 12.5px;
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
            cursor: pointer;
            user-select: none;
        }

        .remember input {
<<<<<<< HEAD
            appearance: none;
            width: 16px;
            height: 16px;
            display: grid;
            place-items: center;
            margin: 0;
            border: 1px solid #c7cbd4;
            border-radius: 4px;
            background: #fff;
            cursor: pointer;
        }

        .remember input:checked {
            border-color: var(--primary);
            background: var(--primary);
        }

        .remember input:checked::after {
            content: "✓";
            color: #fff;
            font-size: 11px;
            font-weight: 900;
            line-height: 1;
        }

        .forgot {
            color: #777c88;
            font-size: 12px;
            text-decoration: none;
        }

        .forgot:hover { color: var(--primary); }

        .submit-btn {
            width: 100%;
            height: 50px;
            border: 0;
            border-radius: 9px;
            background: linear-gradient(135deg, #7c4df8 0%, #6638ed 100%);
            box-shadow: 0 10px 24px rgba(108, 62, 239, .22);
            color: #fff;
            font-size: 14px;
            font-weight: 750;
            cursor: pointer;
            transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
        }

        .submit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 30px rgba(108, 62, 239, .27);
            filter: brightness(1.03);
=======
            width: 15px;
            height: 15px;
            margin: 0;
            accent-color: var(--brand);
        }

        .session-note {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: #94a3b8;
            font-size: 10.5px;
            white-space: nowrap;
        }

        .session-note svg { width: 12px; height: 12px; }

        .submit-btn {
            width: 100%;
            height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0 18px;
            color: #fff;
            background: var(--brand);
            border: 1px solid var(--brand-dark);
            border-radius: 9px;
            box-shadow: 0 7px 16px rgba(37, 99, 235, .20);
            cursor: pointer;
            font-size: 13.5px;
            font-weight: 720;
            transition: background .16s ease, transform .16s ease, box-shadow .16s ease;
        }

        .submit-btn:hover {
            background: var(--brand-dark);
            box-shadow: 0 9px 20px rgba(37, 99, 235, .24);
            transform: translateY(-1px);
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
        }

        .submit-btn:active { transform: translateY(0); }

<<<<<<< HEAD
        .form-footer {
            margin-top: 34px;
            padding-top: 22px;
            border-top: 1px solid var(--line);
            color: #9196a3;
            font-size: 11px;
            line-height: 1.75;
            text-align: center;
        }

        .form-footer strong {
            color: var(--primary);
            font-weight: 750;
        }

        @media (max-width: 560px) {
            body {
                display: block;
                padding: 0;
                background: #fff;
            }

            .login-shell {
                width: 100%;
                min-height: 100vh;
                border: 0;
                border-radius: 0;
                box-shadow: none;
            }

            .login-form-side {
                min-height: 100vh;
                padding: 42px 24px 30px;
            }

            .brand { margin-bottom: 38px; }
            .welcome { font-size: 37px; }
=======
        .submit-btn svg { width: 15px; height: 15px; }

        .submit-btn:focus-visible,
        .password-toggle:focus-visible {
            outline: 3px solid rgba(37, 99, 235, .20);
            outline-offset: 2px;
        }

        .dev-panel {
            margin-top: 20px;
            padding: 12px 13px;
            background: #fbfdff;
            border: 1px solid #dbe5f0;
            border-radius: 9px;
        }

        .dev-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 8px;
        }

        .dev-panel-title {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #475569;
            font-size: 10px;
            font-weight: 760;
            text-transform: uppercase;
            letter-spacing: .065em;
        }

        .dev-panel-title svg { width: 12px; height: 12px; }

        .local-chip {
            padding: 3px 6px;
            color: #1d4ed8;
            background: var(--brand-soft);
            border-radius: 5px;
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .045em;
        }

        .credential-grid {
            display: grid;
            grid-template-columns: 74px minmax(0, 1fr);
            column-gap: 10px;
            row-gap: 5px;
            font-size: 11.5px;
            line-height: 1.45;
        }

        .credential-label { color: #94a3b8; }

        .credential-value {
            min-width: 0;
            color: #334155;
            font-weight: 650;
            word-break: break-word;
        }

        .card-footer {
            padding: 16px 20px 17px;
            color: #94a3b8;
            background: #fbfcfe;
            border-top: 1px solid #edf1f5;
            text-align: center;
            font-size: 10.5px;
            line-height: 1.55;
        }

        .developer-credit {
            margin-top: 3px;
            color: #64748b;
        }

        .developer-credit strong {
            color: #2563eb;
            font-weight: 720;
        }

        @media (max-width: 520px) {
            .auth-shell { padding: 16px; }

            .auth-card { border-radius: 15px; }

            .card-body { padding: 29px 22px 26px; }

            .brand { margin-bottom: 27px; }

            .secure-badge { display: none; }

            .auth-heading h1 { font-size: 25px; }

            .form-options {
                align-items: flex-start;
                flex-direction: column;
                gap: 9px;
            }

            .credential-grid { grid-template-columns: 66px minmax(0, 1fr); }
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
        }
    </style>
</head>
<body>
<<<<<<< HEAD
<div class="login-shell">
    <section class="login-form-side">
        <div class="login-form-wrap">
            <div style="display:flex;justify-content:flex-end;margin-bottom:24px;">
                @include('partials.language-switcher')
            </div>
            <div class="brand" aria-label="{{ __('M/S Afrin Traders') }}">
                <span class="brand-mark" aria-hidden="true"></span>
                <span>{{ __('M/S Afrin Traders') }}</span>
            </div>

            <h1 class="welcome">
                <span>{{ __('Hello,') }}</span>
                <span>{{ __('Welcome Back') }}</span>
            </h1>
            <p class="subtitle">{{ __('Sign in with your email address or username to continue to your ERP workspace.') }}</p>

            @if ($errors->any())
                <div class="alert" role="alert">
                    <span class="alert-icon">!</span>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}">
                @csrf

                <div class="field">
                    <label for="login">{{ __('Email / Username') }}</label>
                    <div class="input-wrap">
                        <input
                            class="control"
                            id="login"
                            name="login"
                            type="text"
                            value="{{ old('login') }}"
                            placeholder="admin@example.com"
                            autocomplete="username"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <div class="field">
                    <label for="password">{{ __('Password') }}</label>
                    <div class="input-wrap">
                        <input
                            class="control password-control"
                            id="password"
                            name="password"
                            type="password"
                            placeholder="{{ __('Enter your password') }}"
                            autocomplete="current-password"
                            required
                        >
                        <button class="password-toggle" type="button" id="togglePassword" aria-label="{{ __('Show password') }}" data-show-label="{{ __('Show password') }}" data-hide-label="{{ __('Hide password') }}">
                            <svg id="eyeOpen" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/>
                                <circle cx="12" cy="12" r="2.6"/>
                            </svg>
                            <svg id="eyeClosed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display:none;">
                                <path d="m3 3 18 18"/>
                                <path d="M10.6 6.15A11.8 11.8 0 0 1 12 6c6.5 0 10 6 10 6a17.7 17.7 0 0 1-2.1 2.8"/>
                                <path d="M6.2 6.2C3.5 8 2 12 2 12s3.5 6 10 6c1.55 0 2.9-.34 4.07-.86"/>
                                <path d="M9.7 9.7A3.25 3.25 0 0 0 14.3 14.3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="remember" for="remember">
                        <input id="remember" type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                        <span>{{ __('Remember me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="forgot" href="{{ route('password.request') }}">{{ __('Forgot Password?') }}</a>
                    @endif
                </div>

                <button class="submit-btn" type="submit">{{ __('Sign In') }}</button>
            </form>

            <div class="form-footer">
                {{ __('Secure access for authorized users only.') }}<br>
                {{ __('Developed by') }} <strong><a href="https://www.facebook.com/dashingcoder" target="_blank" rel="noopener noreferrer">{{ __('Dashing Coder') }}</a></strong>
=======
<main class="auth-shell">
    <section class="auth-card" aria-label="ERP sign in">
        <div class="card-accent" aria-hidden="true"></div>

        <div class="card-body">
            <div class="brand">
                <div class="brand-main">
                    <div class="brand-mark" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9Z" stroke="currentColor" stroke-width="1.7"/>
                            <path d="M4.5 7.7 12 12l7.5-4.3M12 12v8.5" stroke="currentColor" stroke-width="1.7"/>
                        </svg>
                    </div>
                    <div>
                        <div class="brand-name">M/S Afrin Traders</div>
                        <div class="brand-meta">Business Management Platform</div>
                    </div>
                </div>

                <span class="secure-badge">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 3 5 6v5c0 4.6 2.9 8.4 7 10 4.1-1.6 7-5.4 7-10V6l-7-3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        <path d="m9.5 12 1.6 1.6 3.5-3.6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Secure Access
                </span>
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
            </div>

            <header class="auth-heading">
                <h1>Sign in to your account</h1>
                <p>Use your registered email address or username to access the ERP workspace.</p>
            </header>

            @if ($errors->any())
                <div class="alert" role="alert">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 9v4m0 4h.01M10.3 4.1 2.8 17.1A2 2 0 0 0 4.5 20h15a2 2 0 0 0 1.7-2.9L13.7 4.1a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="login">Email or Username</label>
                    <div class="input-wrap">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 6.5h16v11H4v-11Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                            <path d="m5 7.5 7 5 7-5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <input
                            class="form-control @error('login') is-invalid @enderror"
                            id="login"
                            name="login"
                            type="text"
                            value="{{ old('login') }}"
                            placeholder="name@company.com or username"
                            autocomplete="username"
                            required
                            autofocus
                        >
                    </div>
                    @error('login')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrap">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.7"/>
                            <path d="M8 10V7a4 4 0 0 1 8 0v3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                        </svg>
                        <input
                            class="form-control @error('password') is-invalid @enderror"
                            id="password"
                            name="password"
                            type="password"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required
                        >
                        <button class="password-toggle" type="button" id="passwordToggle" aria-label="Show password" aria-pressed="false">
                            <svg id="eyeOpen" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.7"/>
                            </svg>
                            <svg id="eyeClosed" viewBox="0 0 24 24" fill="none" aria-hidden="true" style="display:none">
                                <path d="m3 3 18 18M10.6 6.2A10.8 10.8 0 0 1 12 6c6 0 9.5 6 9.5 6a16.6 16.6 0 0 1-2.4 3.1M6.1 7.2C3.8 9 2.5 12 2.5 12S6 18 12 18c1.2 0 2.3-.2 3.3-.6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-options">
                    <label class="remember" for="remember">
                        <input
                            id="remember"
                            name="remember"
                            type="checkbox"
                            value="1"
                            {{ old('remember') ? 'checked' : '' }}
                        >
                        <span>Remember me on this device</span>
                    </label>

                    <span class="session-note">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 3 5 6v5c0 4.6 2.9 8.4 7 10 4.1-1.6 7-5.4 7-10V6l-7-3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                        </svg>
                        Protected session
                    </span>
                </div>

                <button class="submit-btn" type="submit">
                    <span>Sign in to ERP</span>
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M5 12h14m-5-5 5 5-5 5" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </form>

            @if (app()->environment('local'))
                <div class="dev-panel">
                    <div class="dev-panel-head">
                        <div class="dev-panel-title">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M9 11 5 15l4 4M15 5l4 4-4 4M14 3l-4 18" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Demo Credentials
                        </div>
                        <span class="local-chip">Local only</span>
                    </div>
                    <div class="credential-grid">
                        <span class="credential-label">Email</span>
                        <span class="credential-value">admin@example.com</span>
                        <span class="credential-label">Password</span>
                        <span class="credential-value">ChangeMe123!</span>
                    </div>
                </div>
            @endif
        </div>
<<<<<<< HEAD
    </section>
</div>

<script>
    (function () {
        const toggle = document.getElementById('togglePassword');
        const input = document.getElementById('password');
        const eyeOpen = document.getElementById('eyeOpen');
        const eyeClosed = document.getElementById('eyeClosed');

        if (!toggle || !input) return;

        toggle.addEventListener('click', function () {
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            eyeOpen.style.display = show ? 'none' : 'block';
            eyeClosed.style.display = show ? 'block' : 'none';
            toggle.setAttribute('aria-label', show ? toggle.dataset.hideLabel : toggle.dataset.showLabel);
=======

        <footer class="card-footer">
            <div>Authorized users only. Access may be logged for security and audit purposes.</div>
            <div class="developer-credit">Developed by <strong>Dashing Coder</strong></div>
        </footer>
    </section>
</main>

<script>
    (() => {
        const password = document.getElementById('password');
        const toggle = document.getElementById('passwordToggle');
        const eyeOpen = document.getElementById('eyeOpen');
        const eyeClosed = document.getElementById('eyeClosed');

        if (!password || !toggle) return;

        toggle.addEventListener('click', () => {
            const show = password.type === 'password';
            password.type = show ? 'text' : 'password';
            toggle.setAttribute('aria-pressed', show ? 'true' : 'false');
            toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            eyeOpen.style.display = show ? 'none' : 'block';
            eyeClosed.style.display = show ? 'block' : 'none';
            password.focus();
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
        });
    })();
</script>
</body>
</html>
