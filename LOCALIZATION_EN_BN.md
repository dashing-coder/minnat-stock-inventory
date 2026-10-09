# M/S Afrin Traders — English + Bangla Localization

This build includes a session-based language switcher for English (`en`) and Bangla (`bn`).

## Included

- `app/Http/Middleware/SetLocale.php`
- Global web middleware registration in `bootstrap/app.php`
- `/language/{locale}` switch route in `routes/web.php`
- `resources/views/partials/language-switcher.blade.php`
- English/Bangla JSON UI translations: `lang/en.json`, `lang/bn.json`
- English/Bangla Laravel language files under `lang/en/` and `lang/bn/`
- Language switcher in both the authenticated ERP header and login page
- Localized static UI labels across all Blade views
- Localized common controller success/error messages
- Localized common validation, auth, password and pagination messages

## Supported languages

- English: `/language/en`
- Bangla: `/language/bn`

The selected language is stored in the Laravel session and remains active while navigating the ERP.

## Deployment

After uploading/replacing the project files, run:

```bash
php artisan view:clear
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan optimize:clear
```

If your hosting does not provide terminal access, delete old compiled Blade files under `storage/framework/views/` if necessary; Laravel will rebuild them automatically.

## QA performed

- PHP lint passed for application/bootstrap/routes/config/database/tests/lang PHP files.
- All 80 Blade templates (79 existing + language switcher partial) compiled successfully.
- Compiled Blade PHP syntax validation passed for all 80 templates.
- Bangla translator smoke test passed for Dashboard, Purchases and Sign In.
