<div
    class="flex w-full flex-col gap-11 lg:col-span-2"
    id="<?php echo e('admin-card-' . ($widget?->name?->value ?? 'users-and-platform')); ?>"
>
    <div class="flex items-center justify-between">
        <h2 class="mb-0 font-bold"><?php echo app('translator')->get('Users and Platform'); ?></h2>
        <?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['variant' => 'link','href' => ''.e(route('dashboard.user.index')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
            <span class="text-nowrap font-bold text-foreground"> <?php echo e(__('Visit User Dashboard')); ?> </span>
            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-chevron-right'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-4 rtl:rotate-180']); ?>
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
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale67687e3e4e61f963b25a6bcf3983629)): ?>
<?php $attributes = $__attributesOriginale67687e3e4e61f963b25a6bcf3983629; ?>
<?php unset($__attributesOriginale67687e3e4e61f963b25a6bcf3983629); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale67687e3e4e61f963b25a6bcf3983629)): ?>
<?php $component = $__componentOriginale67687e3e4e61f963b25a6bcf3983629; ?>
<?php unset($__componentOriginale67687e3e4e61f963b25a6bcf3983629); ?>
<?php endif; ?>
    </div>
    <?php if (isset($component)) { $__componentOriginal740c66ff9bbfcb19a96a45ba2fa42d64 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal740c66ff9bbfcb19a96a45ba2fa42d64 = $attributes; } ?>
<?php $component = App\View\Components\Card::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Card::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class:body' => 'flex justify-between flex-wrap md:flex-nowrap py-6 px-10 max-sm:gap-8']); ?>
        <?php
            $users_change = percentageChange(cache()->get('last_week_new_users'), cache()->get('this_week_new_users'));
            $subscribers_change = percentageChange(cache()->get('last_week_subscribers'), cache()->get('this_week_subscribers'));
            $referrals_change = percentageChange(cache()->get('last_week_referrals'), cache()->get('this_week_referrals'));
            $total_users_change = percentageChange(cache()->get('last_week_total_users'), cache()->get('this_week_total_users'));
        ?>
        <div class="flex gap-4 max-sm:w-full">
            <div class="lqd-statistic-info flex grow flex-col gap-1">
                <div
                    class="lqd-statistic-title mb-1 flex items-center gap-2 text-sm font-medium text-heading-foreground">
                    <span class="size-2.5 rounded-sm bg-[#93C5FD]"></span>
                    <?php echo e(__('New Users')); ?>

                </div>
                <h3 class="lqd-statistic-change m-0.5 flex items-center gap-2 text-2xl sm:text-[30px]">
                    <?php echo e(cache()->get('this_week_new_users')); ?>

                </h3>
                <p class="mb-0 flex items-center gap-1 text-[12px] text-heading-foreground/50">
                    <?php echo app('translator')->get('vs Last week'); ?> <?php if (isset($component)) { $__componentOriginal32f0974b3b2a3c63adc06ac003dee85a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal32f0974b3b2a3c63adc06ac003dee85a = $attributes; } ?>
<?php $component = App\View\Components\ChangeIndicatorPlusMinus::resolve(['value' => ''.e(floatval($users_change)).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('change-indicator-plus-minus'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\ChangeIndicatorPlusMinus::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal32f0974b3b2a3c63adc06ac003dee85a)): ?>
<?php $attributes = $__attributesOriginal32f0974b3b2a3c63adc06ac003dee85a; ?>
<?php unset($__attributesOriginal32f0974b3b2a3c63adc06ac003dee85a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal32f0974b3b2a3c63adc06ac003dee85a)): ?>
<?php $component = $__componentOriginal32f0974b3b2a3c63adc06ac003dee85a; ?>
<?php unset($__componentOriginal32f0974b3b2a3c63adc06ac003dee85a); ?>
<?php endif; ?>
                </p>
            </div>
        </div>

        <span class="h-px w-full bg-border sm:h-auto sm:w-px"></span>

        <div class="flex gap-4 max-sm:w-full">
            <div class="lqd-statistic-info flex grow flex-col gap-1">
                <div
                    class="lqd-statistic-title mb-1 flex items-center gap-2 text-sm font-medium text-heading-foreground">
                    <span class="size-2.5 rounded-sm bg-secondary"></span>
                    <?php echo e(__('New Subscribers')); ?>

                </div>
                <h3 class="lqd-statistic-change m-0.5 flex items-center gap-2 text-2xl sm:text-[30px]">
                    <?php echo e(cache()->get('this_week_subscribers')); ?>

                </h3>
                <p class="mb-0 flex items-center gap-1 text-[12px] text-heading-foreground/50">
                    <?php echo app('translator')->get('vs Last week'); ?> <?php if (isset($component)) { $__componentOriginal32f0974b3b2a3c63adc06ac003dee85a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal32f0974b3b2a3c63adc06ac003dee85a = $attributes; } ?>
<?php $component = App\View\Components\ChangeIndicatorPlusMinus::resolve(['value' => ''.e(floatval($subscribers_change)).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('change-indicator-plus-minus'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\ChangeIndicatorPlusMinus::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal32f0974b3b2a3c63adc06ac003dee85a)): ?>
<?php $attributes = $__attributesOriginal32f0974b3b2a3c63adc06ac003dee85a; ?>
<?php unset($__attributesOriginal32f0974b3b2a3c63adc06ac003dee85a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal32f0974b3b2a3c63adc06ac003dee85a)): ?>
<?php $component = $__componentOriginal32f0974b3b2a3c63adc06ac003dee85a; ?>
<?php unset($__componentOriginal32f0974b3b2a3c63adc06ac003dee85a); ?>
<?php endif; ?>
                </p>
            </div>
        </div>

        <span class="h-px w-full bg-border sm:h-auto sm:w-px"></span>

        <div class="flex gap-4 max-sm:w-full">
            <div class="lqd-statistic-info flex grow flex-col gap-1">
                <div
                    class="lqd-statistic-title mb-1 flex items-center gap-2 text-sm font-medium text-heading-foreground">
                    <span class="size-2.5 rounded-sm bg-[#89E1C5]"></span>
                    <?php echo e(__('New Referrals')); ?>

                </div>
                <h3 class="lqd-statistic-change m-0.5 flex items-center gap-2 text-2xl sm:text-[30px]">
                    <?php echo e(cache()->get('this_week_referrals')); ?>

                </h3>
                <p class="mb-0 flex items-center gap-1 text-[12px] text-heading-foreground/50">
                    <?php echo app('translator')->get('vs Last week'); ?> <?php if (isset($component)) { $__componentOriginal32f0974b3b2a3c63adc06ac003dee85a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal32f0974b3b2a3c63adc06ac003dee85a = $attributes; } ?>
<?php $component = App\View\Components\ChangeIndicatorPlusMinus::resolve(['value' => ''.e(floatval($referrals_change)).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('change-indicator-plus-minus'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\ChangeIndicatorPlusMinus::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal32f0974b3b2a3c63adc06ac003dee85a)): ?>
<?php $attributes = $__attributesOriginal32f0974b3b2a3c63adc06ac003dee85a; ?>
<?php unset($__attributesOriginal32f0974b3b2a3c63adc06ac003dee85a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal32f0974b3b2a3c63adc06ac003dee85a)): ?>
<?php $component = $__componentOriginal32f0974b3b2a3c63adc06ac003dee85a; ?>
<?php unset($__componentOriginal32f0974b3b2a3c63adc06ac003dee85a); ?>
<?php endif; ?>
                </p>
            </div>
        </div>

        <span class="h-px w-full bg-border sm:h-auto sm:w-px"></span>

        <div class="flex gap-4 max-sm:w-full">
            <div class="lqd-statistic-info flex grow flex-col gap-1">
                <div
                    class="lqd-statistic-title mb-1 flex items-center gap-2 text-sm font-medium text-heading-foreground">
                    <span class="size-2.5 rounded-sm bg-[#93C5FD]"></span>
                    <?php echo e(__('Total Users')); ?>

                </div>
                <h3 class="lqd-statistic-change m-0.5 flex items-center gap-2 text-2xl sm:text-[30px]">
                    <?php echo e(\App\Helpers\Classes\Helper::appIsDemo() ? 2 : cache()->get('this_week_total_users')); ?>

                </h3>
                <p class="mb-0 flex items-center gap-1 text-[12px] text-heading-foreground/50">
                    <?php echo app('translator')->get('vs Last week'); ?> <?php if (isset($component)) { $__componentOriginal32f0974b3b2a3c63adc06ac003dee85a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal32f0974b3b2a3c63adc06ac003dee85a = $attributes; } ?>
<?php $component = App\View\Components\ChangeIndicatorPlusMinus::resolve(['value' => ''.e(\App\Helpers\Classes\Helper::appIsDemo() ? 10 : floatval($total_users_change)).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('change-indicator-plus-minus'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\ChangeIndicatorPlusMinus::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal32f0974b3b2a3c63adc06ac003dee85a)): ?>
<?php $attributes = $__attributesOriginal32f0974b3b2a3c63adc06ac003dee85a; ?>
<?php unset($__attributesOriginal32f0974b3b2a3c63adc06ac003dee85a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal32f0974b3b2a3c63adc06ac003dee85a)): ?>
<?php $component = $__componentOriginal32f0974b3b2a3c63adc06ac003dee85a; ?>
<?php unset($__componentOriginal32f0974b3b2a3c63adc06ac003dee85a); ?>
<?php endif; ?>
                </p>
            </div>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal740c66ff9bbfcb19a96a45ba2fa42d64)): ?>
<?php $attributes = $__attributesOriginal740c66ff9bbfcb19a96a45ba2fa42d64; ?>
<?php unset($__attributesOriginal740c66ff9bbfcb19a96a45ba2fa42d64); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal740c66ff9bbfcb19a96a45ba2fa42d64)): ?>
<?php $component = $__componentOriginal740c66ff9bbfcb19a96a45ba2fa42d64; ?>
<?php unset($__componentOriginal740c66ff9bbfcb19a96a45ba2fa42d64); ?>
<?php endif; ?>
</div>
<?php /**PATH D:\omini_customization\OminiAI\resources\views/default/panel/admin/dashboard/users-and-platform.blade.php ENDPATH**/ ?>