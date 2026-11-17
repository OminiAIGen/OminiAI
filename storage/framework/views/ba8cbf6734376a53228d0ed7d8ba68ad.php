<?php $__env->startPush('css'); ?>
    <style>
        .lqd-auth-content h1 {
            text-align: center
        }

        .lqd-auth-content .lqd-auth-form-foot-text {
            margin-top: 42px;
            text-align: center;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <header class="absolute left-0 right-0 top-0 z-2 flex items-center justify-between px-5 pt-6 lg:px-8 lg:pt-8">
        <a
            class="navbar-brand"
            href="<?php echo e(route('index')); ?>"
        >
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($setting->logo_dashboard)): ?>
                <img
                    class="dark:hidden"
                    src="<?php echo e(custom_theme_url($setting->logo_dashboard_path, true)); ?>"
                    <?php if(isset($setting->logo_dashboard_2x_path) && !empty($setting->logo_dashboard_2x_path)): ?> srcset="/<?php echo e($setting->logo_dashboard_2x_path); ?> 2x" <?php endif; ?>
                    alt="<?php echo e($setting->site_name); ?>"
                >
                <img
                    class="hidden dark:block"
                    src="<?php echo e(custom_theme_url($setting->logo_dashboard_dark_path, true)); ?>"
                    <?php if(isset($setting->logo_dashboard_dark_2x_path) && !empty($setting->logo_dashboard_dark_2x_path)): ?> srcset="/<?php echo e($setting->logo_dashboard_dark_2x_path); ?> 2x" <?php endif; ?>
                    alt="<?php echo e($setting->site_name); ?>"
                >
            <?php else: ?>
                <img
                    class="dark:hidden"
                    src="<?php echo e(custom_theme_url($setting->logo_path, true)); ?>"
                    <?php if(isset($setting->logo_2x_path) && !empty($setting->logo_2x_path)): ?> srcset="/<?php echo e($setting->logo_2x_path); ?> 2x" <?php endif; ?>
                    alt="<?php echo e($setting->site_name); ?>"
                >
                <img
                    class="hidden dark:block"
                    src="<?php echo e(custom_theme_url($setting->logo_dark_path, true)); ?>"
                    <?php if(isset($setting->logo_dark_2x_path) && !empty($setting->logo_dark_2x_path)): ?> srcset="/<?php echo e($setting->logo_dark_2x_path); ?> 2x" <?php endif; ?>
                    alt="<?php echo e($setting->site_name); ?>"
                >
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </a>

        <a
            class="inline-flex items-center gap-1 text-foreground no-underline hover:underline"
            href="<?php echo e(route('index')); ?>"
        >
            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-chevron-left'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
            <?php echo e(__('Back to Home')); ?>

        </a>
    </header>

    <div class="relative grid h-screen w-screen grid-cols-1 place-items-start px-5 pb-10 pt-20 sm:place-items-center">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(
            $setting->auth_view_options != null &&
                $setting->auth_view_options != 'undefined' &&
                json_decode($setting->auth_view_options)?->login_enabled == true &&
                json_decode($setting->auth_view_options)?->login_image != null &&
                json_decode($setting->auth_view_options)?->login_image != ''): ?>
            <div
                class="absolute inset-0 z-0 bg-cover bg-center"
                style="background-image: url(<?php echo e(json_decode($setting->auth_view_options)->login_image); ?>)"
            ></div>
        <?php else: ?>
            <div
                class="absolute inset-0 z-0 bg-cover bg-bottom bg-no-repeat"
                style="background-image: url(<?php echo e(custom_theme_url('/assets/img/bg/bg-auth.png')); ?>); background-size: min(90%,1000px);"
            ></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="lqd-auth-content relative z-2 max-h-[calc(100vh-120px)] w-[min(calc(100vw-40px),460px)] overflow-y-auto rounded-[20px] bg-card-background p-8 md:px-8 md:py-10">
            <?php echo $__env->yieldContent('form'); ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('panel.layout.app', ['layout_wide' => true, 'wide_layout_px' => 'px-0'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u504467222/domains/omini.ai/public_html/resources/views/marketing-bot/panel/authentication/layout/app.blade.php ENDPATH**/ ?>