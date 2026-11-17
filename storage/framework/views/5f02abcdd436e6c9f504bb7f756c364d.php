<a
    <?php echo e($attributes->class(['flex shrink-0 items-center justify-center'])); ?>

    href="<?php echo e(route('dashboard.index')); ?>"
>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($setting->logo_dashboard)): ?>
        <img
            class="dark:hidden"
            src="<?php echo e(custom_theme_url($setting->logo_dashboard_path, true)); ?>"
            <?php if(isset($setting->logo_dashboard_2x_path)): ?> srcset="/<?php echo e($setting->logo_dashboard_2x_path); ?> 2x" <?php endif; ?>
            alt="<?php echo e($setting->site_name); ?>"
        >
        <img
            class="hidden dark:block"
            src="<?php echo e(custom_theme_url($setting->logo_dashboard_dark_path, true)); ?>"
            <?php if(isset($setting->logo_dashboard_dark_2x_path)): ?> srcset="/<?php echo e($setting->logo_dashboard_dark_2x_path); ?> 2x" <?php endif; ?>
            alt="<?php echo e($setting->site_name); ?>"
        >
    <?php else: ?>
        <img
            class="dark:hidden"
            src="<?php echo e(custom_theme_url($setting->logo_path, true)); ?>"
            <?php if(isset($setting->logo_2x_path)): ?> srcset="/<?php echo e($setting->logo_2x_path); ?> 2x" <?php endif; ?>
            alt="<?php echo e($setting->site_name); ?>"
        >
        <img
            class="hidden dark:block"
            src="<?php echo e(custom_theme_url($setting->logo_dark_path, true)); ?>"
            <?php if(isset($setting->logo_dark_2x_path)): ?> srcset="/<?php echo e($setting->logo_dark_2x_path); ?> 2x" <?php endif; ?>
            alt="<?php echo e($setting->site_name); ?>"
        >
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</a>
<?php /**PATH /home/u504467222/domains/omini.ai/public_html/resources/views/components/header-logo.blade.php ENDPATH**/ ?>