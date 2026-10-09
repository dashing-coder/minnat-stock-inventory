<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
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
        }

        * { box-sizing: border-box; }

        html, body { min-height: 100%; }

        body {
            margin: 0;
            min-height: 100vh;
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
        }

        .alert {
            display: flex;
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
        }

        .input-wrap { position: relative; }

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

        .password-toggle {
            position: absolute;
            top: 50%;
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

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 2px 0 20px;
        }

        .remember {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #475569;
            font-size: 12.5px;
            cursor: pointer;
            user-select: none;
        }

        .remember input {
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
        }

        .submit-btn:active { transform: translateY(0); }

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
        }
    </style>
</head>
<body>
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
            </div>

            <header class="auth-heading">
                <h1>Sign in to your account</h1>
                <p>Use your registered email address or username to access the ERP workspace.</p>
            </header>

            <?php if($errors->any()): ?>
                <div class="alert" role="alert">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 9v4m0 4h.01M10.3 4.1 2.8 17.1A2 2 0 0 0 4.5 20h15a2 2 0 0 0 1.7-2.9L13.7 4.1a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span><?php echo e($errors->first()); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('login.store')); ?>">
                <?php echo csrf_field(); ?>

                <div class="form-group">
                    <label class="form-label" for="login">Email or Username</label>
                    <div class="input-wrap">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 6.5h16v11H4v-11Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                            <path d="m5 7.5 7 5 7-5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <input
                            class="form-control <?php $__errorArgs = ['login'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            id="login"
                            name="login"
                            type="text"
                            value="<?php echo e(old('login')); ?>"
                            placeholder="name@company.com or username"
                            autocomplete="username"
                            required
                            autofocus
                        >
                    </div>
                    <?php $__errorArgs = ['login'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="field-error"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrap">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.7"/>
                            <path d="M8 10V7a4 4 0 0 1 8 0v3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                        </svg>
                        <input
                            class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
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
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="field-error"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-options">
                    <label class="remember" for="remember">
                        <input
                            id="remember"
                            name="remember"
                            type="checkbox"
                            value="1"
                            <?php echo e(old('remember') ? 'checked' : ''); ?>

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

            <?php if(app()->environment('local')): ?>
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
            <?php endif; ?>
        </div>

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
        });
    })();
</script>
</body>
</html>
<?php /**PATH D:\xampp\htdocs\erp-system\resources\views/auth/login.blade.php ENDPATH**/ ?>