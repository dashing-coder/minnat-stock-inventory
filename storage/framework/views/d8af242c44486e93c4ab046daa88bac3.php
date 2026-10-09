<?php ($currentLocale = app()->getLocale()); ?>

<div class="erp-language-switcher" role="group" aria-label="<?php echo e(__('Language')); ?>">
    <a href="<?php echo e(route('language.switch', 'en')); ?>"
       class="erp-language-option <?php echo e($currentLocale === 'en' ? 'active' : ''); ?>"
       lang="en" hreflang="en"
       aria-current="<?php echo e($currentLocale === 'en' ? 'true' : 'false'); ?>"
       title="English">
        <span class="erp-language-flag" aria-hidden="true">🇺🇸</span>
        <span>EN</span>
    </a>

    <a href="<?php echo e(route('language.switch', 'bn')); ?>"
       class="erp-language-option <?php echo e($currentLocale === 'bn' ? 'active' : ''); ?>"
       lang="bn" hreflang="bn"
       aria-current="<?php echo e($currentLocale === 'bn' ? 'true' : 'false'); ?>"
       title="বাংলা">
        <span class="erp-language-flag" aria-hidden="true">🇧🇩</span>
        <span>বাংলা</span>
    </a>
</div>

<?php if (! $__env->hasRenderedOnce('bd05c3f6-ef89-465a-b517-cb04463e2f6e')): $__env->markAsRenderedOnce('bd05c3f6-ef89-465a-b517-cb04463e2f6e'); ?>
<style>
.erp-language-switcher{display:inline-flex;align-items:center;gap:4px;padding:4px;border:1px solid #e2e8f0;border-radius:10px;background:#f8fafc;white-space:nowrap}
.erp-language-option{min-height:34px;display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:5px 10px;border-radius:7px;color:#64748b;font-size:12px;font-weight:700;line-height:1;text-decoration:none;transition:background .15s ease,color .15s ease,box-shadow .15s ease}
.erp-language-option:hover{color:#2563eb;background:#fff}
.erp-language-option.active{color:#fff;background:#2563eb;box-shadow:0 3px 8px rgba(37,99,235,.22)}
.erp-language-flag{font-size:15px;line-height:1}
@media(max-width:575px){.erp-language-option{padding-left:7px;padding-right:7px}.erp-language-option>span:last-child{display:none}.erp-language-flag{font-size:17px}}
</style>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\afrintraders\resources\views/partials/language-switcher.blade.php ENDPATH**/ ?>