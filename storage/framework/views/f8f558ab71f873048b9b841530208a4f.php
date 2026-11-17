<?php if (isset($component)) { $__componentOriginal740c66ff9bbfcb19a96a45ba2fa42d64 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal740c66ff9bbfcb19a96a45ba2fa42d64 = $attributes; } ?>
<?php $component = App\View\Components\Card::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Card::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'lg:col-span-2','class:body' => 'flex justify-between flex-wrap md:flex-nowrap py-6 px-10 max-sm:gap-8','id' => ''.e('admin-card-' . ($widget?->name?->value ?? 'usage-overview')).'']); ?>
    <?php
        $sales_change = percentageChange($sales_prev_week, $sales_this_week);
        $users_change = percentageChange(cache()->get('last_week_new_users'), cache()->get('this_week_new_users'));
        $generated_change = percentageChange(cache()->get('usage_previous_week'), cache()->get('usage_this_week'));
        $dialy_activity_change = percentageChange(cache()->get('daily_activity_last_week'), cache()->get('daily_activity_this_week'));
    ?>
    <div class="flex gap-4 max-sm:w-full">
        <div class="lqd-statistic-info flex grow flex-col gap-1">
            <div class="lqd-statistic-title mb-1 flex items-center gap-2 text-sm font-medium text-heading-foreground">
                <span class="size-2.5 rounded-sm bg-secondary"></span>
                <?php echo e(__('Earnings')); ?>

            </div>
            <h3 class="lqd-statistic-change mb-0.5 flex items-center gap-2 text-2xl sm:text-[30px]">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(currencyShouldDisplayOnRight($currencySymbol)): ?>
                    <?php echo e(number_format(cache()->get('total_sales'))); ?> <?php echo e($currencySymbol); ?>

                <?php else: ?>
                    <?php echo e($currencySymbol); ?><?php echo e(number_format(cache()->get('total_sales'))); ?>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </h3>
            <p class="mb-0 flex items-center gap-1 text-[12px] text-heading-foreground/50">
                <?php echo app('translator')->get('vs Last Week'); ?> <?php if (isset($component)) { $__componentOriginal32f0974b3b2a3c63adc06ac003dee85a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal32f0974b3b2a3c63adc06ac003dee85a = $attributes; } ?>
<?php $component = App\View\Components\ChangeIndicatorPlusMinus::resolve(['value' => ''.e(\App\Helpers\Classes\Helper::appIsDemo() ? 34 : floatval($sales_change)).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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
            <div class="lqd-statistic-title mb-1 flex items-center gap-2 text-sm font-medium text-heading-foreground">
                <span class="size-2.5 rounded-sm bg-secondary"></span>
                <?php echo e(__('New Users')); ?>

            </div>
            <h3 class="lqd-statistic-change mb-0.5 flex items-center gap-2 text-2xl sm:text-[30px]">
                <?php echo e(\App\Helpers\Classes\Helper::appIsDemo() ? 12 : cache()->get('this_week_new_users')); ?>

            </h3>
            <p class="mb-0 flex items-center gap-1 text-[12px] text-heading-foreground/50">
                <?php echo app('translator')->get('vs Last Week'); ?> <?php if (isset($component)) { $__componentOriginal32f0974b3b2a3c63adc06ac003dee85a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal32f0974b3b2a3c63adc06ac003dee85a = $attributes; } ?>
<?php $component = App\View\Components\ChangeIndicatorPlusMinus::resolve(['value' => ''.e(\App\Helpers\Classes\Helper::appIsDemo() ? 18 : floatval($users_change)).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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
            <div class="lqd-statistic-title mb-1 flex items-center gap-2 text-sm font-medium text-heading-foreground">
                <span class="size-2.5 rounded-sm bg-secondary"></span>
                <?php echo e(__('AI Usage')); ?>

            </div>
            <h3 class="lqd-statistic-change mb-0.5 flex items-center gap-2 text-2xl sm:text-[30px]">
                <?php echo e(cache()->get('total_usage')); ?>

            </h3>
            <p class="mb-0 flex items-center gap-1 text-[12px] text-heading-foreground/50">
                <?php echo app('translator')->get('vs Last Week'); ?> <?php if (isset($component)) { $__componentOriginal32f0974b3b2a3c63adc06ac003dee85a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal32f0974b3b2a3c63adc06ac003dee85a = $attributes; } ?>
<?php $component = App\View\Components\ChangeIndicatorPlusMinus::resolve(['value' => ''.e(floatval($generated_change)).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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
            <div class="lqd-statistic-title mb-1 flex items-center gap-2 text-sm font-medium text-heading-foreground">
                <span class="size-2.5 rounded-sm bg-secondary"></span>
                <?php echo e(__('Daily Visit')); ?>

            </div>
            <h3 class="lqd-statistic-change mb-0.5 flex items-center gap-2 text-2xl sm:text-[30px]">
                <?php echo e(\App\Helpers\Classes\Helper::appIsDemo() ? 2421 : cache()->get('total_daily_activity')); ?>

            </h3>
            <p class="mb-0 flex items-center gap-1 text-[12px] text-heading-foreground/50">
                <?php echo app('translator')->get('vs Last Week'); ?>
				<?php if (isset($component)) { $__componentOriginal32f0974b3b2a3c63adc06ac003dee85a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal32f0974b3b2a3c63adc06ac003dee85a = $attributes; } ?>
<?php $component = App\View\Components\ChangeIndicatorPlusMinus::resolve(['value' => ''.e(\App\Helpers\Classes\Helper::appIsDemo() ? 14 : floatval($dialy_activity_change)).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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
<?php /**PATH D:\omini_customization\OminiAI\resources\views/default/panel/admin/dashboard/usage-overview.blade.php ENDPATH**/ ?>