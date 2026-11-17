<a
    class="relative z-1 block px-0"
    href="<?php echo e(route('dashboard.index')); ?>"
>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($setting->logo_dashboard)): ?>
        <img
            class="h-auto w-full group-[.navbar-shrinked]/body:hidden dark:hidden"
            src="<?php echo e(custom_theme_url($setting->logo_dashboard_path, true)); ?>"
            <?php if(isset($setting->logo_dashboard_2x_path) && !empty($setting->logo_dashboard_2x_path)): ?> srcset="/<?php echo e($setting->logo_dashboard_2x_path); ?> 2x" <?php endif; ?>
            alt="<?php echo e($setting->site_name); ?>"
        >
        <img
            class="hidden h-auto w-full group-[.navbar-shrinked]/body:hidden dark:block"
            src="<?php echo e(custom_theme_url($setting->logo_dashboard_dark_path, true)); ?>"
            <?php if(isset($setting->logo_dashboard_dark_2x_path) && !empty($setting->logo_dashboard_dark_2x_path)): ?> srcset="/<?php echo e($setting->logo_dashboard_dark_2x_path); ?> 2x" <?php endif; ?>
            alt="<?php echo e($setting->site_name); ?>"
        >
    <?php else: ?>
        <img
            class="h-auto w-full group-[.navbar-shrinked]/body:hidden dark:hidden"
            src="<?php echo e(custom_theme_url($setting->logo_path, true)); ?>"
            <?php if(isset($setting->logo_2x_path) && !empty($setting->logo_2x_path)): ?> srcset="/<?php echo e($setting->logo_2x_path); ?> 2x" <?php endif; ?>
            alt="<?php echo e($setting->site_name); ?>"
        >
        <img
            class="hidden h-auto w-full group-[.navbar-shrinked]/body:hidden dark:block"
            src="<?php echo e(custom_theme_url($setting->logo_dark_path, true)); ?>"
            <?php if(isset($setting->logo_dark_2x_path) && !empty($setting->logo_dark_2x_path)): ?> srcset="/<?php echo e($setting->logo_dark_2x_path); ?> 2x" <?php endif; ?>
            alt="<?php echo e($setting->site_name); ?>"
        >
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- collapsed -->
    <img
        class="mx-auto hidden h-auto w-full max-w-10 group-[.navbar-shrinked]/body:block dark:!hidden"
        src="<?php echo e(custom_theme_url($setting->logo_collapsed_path, true)); ?>"
        <?php if(isset($setting->logo_collapsed_2x_path) && !empty($setting->logo_collapsed_2x_path)): ?> srcset="/<?php echo e($setting->logo_collapsed_2x_path); ?> 2x" <?php endif; ?>
        alt="<?php echo e($setting->site_name); ?>"
    >
    <img
        class="mx-auto hidden h-auto w-full max-w-10 group-[.theme-dark.navbar-shrinked]/body:block"
        src="<?php echo e(custom_theme_url($setting->logo_collapsed_dark_path, true)); ?>"
        <?php if(isset($setting->logo_collapsed_dark_2x_path) && !empty($setting->logo_collapsed_dark_2x_path)): ?> srcset="/<?php echo e($setting->logo_collapsed_dark_2x_path); ?> 2x" <?php endif; ?>
        alt="<?php echo e($setting->site_name); ?>"
    >

</a>
<?php /**PATH D:\omini_customization\OminiAI\resources\views/default/components/navbar/navbar-logo.blade.php ENDPATH**/ ?>