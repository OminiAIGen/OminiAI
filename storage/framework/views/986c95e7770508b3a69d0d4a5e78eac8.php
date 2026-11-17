<div
    class="relative rounded-3xl p-5 backdrop-blur-[50px] before:pointer-events-none before:absolute before:-inset-px before:-z-1 before:rounded-[inherit] before:border before:border-white before:opacity-30 before:[mask-image:linear-gradient(to_bottom_left,black,transparent_30%)] hover:z-2 md:px-10 md:py-10 lg:px-12"
    style="background: radial-gradient(circle at center -40%, hsl(0 0% 100% / 5%), transparent)"
>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan->is_featured): ?>
        <?php if (isset($component)) { $__componentOriginal77a08a15bb8fd56a2ef1c4a7aa26a8ad = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal77a08a15bb8fd56a2ef1c4a7aa26a8ad = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.outline-glow','data' => ['class' => 'lqd-outline-glow-custom rounded-[27px] [--outline-glow-w:3px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('outline-glow'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'lqd-outline-glow-custom rounded-[27px] [--outline-glow-w:3px]']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal77a08a15bb8fd56a2ef1c4a7aa26a8ad)): ?>
<?php $attributes = $__attributesOriginal77a08a15bb8fd56a2ef1c4a7aa26a8ad; ?>
<?php unset($__attributesOriginal77a08a15bb8fd56a2ef1c4a7aa26a8ad); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal77a08a15bb8fd56a2ef1c4a7aa26a8ad)): ?>
<?php $component = $__componentOriginal77a08a15bb8fd56a2ef1c4a7aa26a8ad; ?>
<?php unset($__componentOriginal77a08a15bb8fd56a2ef1c4a7aa26a8ad); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <p class="mx-auto mb-4 inline-flex rounded-full border border-white/10 px-5 py-2 text-xs">
        <?php echo e(__($plan->name)); ?>

    </p>
    <p class="mb-0 text-[46px] font-semibold leading-none -tracking-wider">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(currencyShouldDisplayOnRight(currency()->symbol)): ?>
            <?php echo e(formatPrice($plan->price, 2)); ?><sub class="top-[-0.1em] text-[0.57em] leading-none"><?php echo e(currency()->symbol); ?></sub>
        <?php else: ?>
            <sub class="top-[-0.1em] text-[0.57em] leading-none"><?php echo e(currency()->symbol); ?></sub><?php echo e(formatPrice($plan->price, 2)); ?>

        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </p>
    <p class="text-[12px] opacity-50">
        <?php echo e(__($period)); ?>

    </p>

    <div class="text-base font-medium decoration-dotted [&_.lqd-price-table-info]:text-xs [&_.lqd-price-table-info]:text-black">
        <?php if (isset($component)) { $__componentOriginal108762a00a61bbda2e149b49ad544dca = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal108762a00a61bbda2e149b49ad544dca = $attributes; } ?>
<?php $component = App\View\Components\PlanDetailsCard::resolve(['style' => 'style-3','plan' => $plan,'period' => $period] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('plan-details-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\PlanDetailsCard::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal108762a00a61bbda2e149b49ad544dca)): ?>
<?php $attributes = $__attributesOriginal108762a00a61bbda2e149b49ad544dca; ?>
<?php unset($__attributesOriginal108762a00a61bbda2e149b49ad544dca); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal108762a00a61bbda2e149b49ad544dca)): ?>
<?php $component = $__componentOriginal108762a00a61bbda2e149b49ad544dca; ?>
<?php unset($__componentOriginal108762a00a61bbda2e149b49ad544dca); ?>
<?php endif; ?>
    </div>

    <a
        class="group relative mt-6 flex w-full items-center justify-center gap-4 rounded-xl bg-[#1A1A1A] p-5 text-center transition-all hover:bg-white hover:text-black hover:shadow-xl hover:shadow-white/20"
        href="<?php echo e(route('register', ['plan' => $plan->id])); ?>"
    >
        <?php echo e(__('Get Started')); ?>

        <svg
            width="17"
            height="13"
            viewBox="0 0 17 13"
            fill="currentColor"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M15.6832 5.38486C13.4942 5.38486 11.499 3.39061 11.499 1.20063V0.302734H9.70323V1.20063C9.70323 2.79351 10.4018 4.28761 11.4981 5.38486H0.418945V7.18066H11.4981C10.4018 8.27789 9.70323 9.77199 9.70323 11.3649V12.2628H11.499V11.3649C11.499 9.17489 13.4942 7.18066 15.6832 7.18066H16.5811V5.38486H15.6832Z"
            />
        </svg>
    </a>
</div>
<?php /**PATH D:\omini_customization\OminiAI\resources\views/marketing-bot/landing-page/pricing/item-content.blade.php ENDPATH**/ ?>