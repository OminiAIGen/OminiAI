<a
    class="lqd-skip-link pointer-events-none fixed start-7 top-7 z-[90] rounded-md bg-background px-3 py-1 text-lg opacity-0 shadow-xl focus-visible:pointer-events-auto focus-visible:opacity-100 focus-visible:outline-primary"
    href="#lqd-titlebar"
>
    <?php echo e(__('Skip to content')); ?>

</a>

<button
    class="lqd-navbar-expander fixed start-[--navbar-width] top-[calc(var(--header-height)/2)] z-[999] inline-flex size-6 -translate-x-1/2 -translate-y-1/2 cursor-pointer items-center justify-center rounded-button border-0 bg-foreground/10 p-0 text-heading-foreground backdrop-blur-sm transition-all hover:bg-heading-foreground hover:text-heading-background group-[.navbar-shrinked]/body:!start-[80px] group-[.navbar-shrinked]/body:rotate-180 max-lg:hidden rtl:translate-x-1/2"
    x-init
    @click.prevent="$store.navbarShrink.toggle()"
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
<?php $component->withAttributes(['class' => 'w-4 rtl:rotate-180']); ?>
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
</button>

<aside
    class="lqd-navbar no-scrollbar z-[99] w-[--navbar-width] shrink-0 overflow-hidden rounded-ee-navbar-ee rounded-es-navbar-es rounded-se-navbar-se rounded-ss-navbar-ss border-e border-navbar-border bg-navbar-background text-navbar font-medium text-navbar-foreground transition-all max-lg:invisible max-lg:absolute max-lg:left-0 max-lg:top-[65px] max-lg:z-[99] max-lg:max-h-[calc(85vh-2rem)] max-lg:min-h-0 max-lg:w-full max-lg:origin-top max-lg:-translate-y-2 max-lg:scale-95 max-lg:rounded-b max-lg:bg-background max-lg:p-0 max-lg:opacity-0 max-lg:shadow-xl lg:sticky lg:top-0 lg:h-screen max-lg:[&.lqd-is-active]:visible max-lg:[&.lqd-is-active]:translate-y-0 max-lg:[&.lqd-is-active]:scale-100 max-lg:[&.lqd-is-active]:opacity-100"
    data-name="<?php echo e(\App\Enums\Introduction::SIDEBAR); ?>"
    x-init
    :class="{ 'lqd-is-active': !$store.mobileNav.navCollapse }"
>
    <div class="lqd-navbar-inner no-scrollbar h-full overflow-y-auto overscroll-contain pe-navbar-pe ps-navbar-ps max-lg:max-h-[inherit]">
        <div
            class="lqd-navbar-logo relative flex min-h-[--header-height] max-w-full items-center pe-navbar-link-pe ps-navbar-link-ps group-[.navbar-shrinked]/body:w-full group-[.navbar-shrinked]/body:justify-center group-[.navbar-shrinked]/body:px-0 group-[.navbar-shrinked]/body:text-center max-lg:hidden">
            <?php if (isset($component)) { $__componentOriginal0771f55ab00a25b66c062c4a13450933 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0771f55ab00a25b66c062c4a13450933 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.progressive-blur','data' => ['class' => 'lqd-navbar-logo-blur hidden lg:-bottom-4 lg:-start-navbar-ps lg:-me-navbar-pe lg:block','dir' => 'reverse']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('progressive-blur'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'lqd-navbar-logo-blur hidden lg:-bottom-4 lg:-start-navbar-ps lg:-me-navbar-pe lg:block','dir' => 'reverse']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0771f55ab00a25b66c062c4a13450933)): ?>
<?php $attributes = $__attributesOriginal0771f55ab00a25b66c062c4a13450933; ?>
<?php unset($__attributesOriginal0771f55ab00a25b66c062c4a13450933); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0771f55ab00a25b66c062c4a13450933)): ?>
<?php $component = $__componentOriginal0771f55ab00a25b66c062c4a13450933; ?>
<?php unset($__componentOriginal0771f55ab00a25b66c062c4a13450933); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginal9df5137c93463672e7ce03427a975a11 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9df5137c93463672e7ce03427a975a11 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.navbar.navbar-logo','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('navbar.navbar-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9df5137c93463672e7ce03427a975a11)): ?>
<?php $attributes = $__attributesOriginal9df5137c93463672e7ce03427a975a11; ?>
<?php unset($__attributesOriginal9df5137c93463672e7ce03427a975a11); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9df5137c93463672e7ce03427a975a11)): ?>
<?php $component = $__componentOriginal9df5137c93463672e7ce03427a975a11; ?>
<?php unset($__componentOriginal9df5137c93463672e7ce03427a975a11); ?>
<?php endif; ?>
        </div>

        <nav
            class="lqd-navbar-nav"
            id="navbar-menu"
        >
            <ul class="lqd-navbar-ul">
                
                <!-- Menu cache -->
                <?php echo App\Caches\BladeCache::navMenu(static fn() => view('panel.layout.partials.menu')->render()); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()?->isAdmin()): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($app_is_not_demo && setting('premium_support', true) && !\App\Helpers\Classes\Helper::isUserVIP()): ?>
                        <?php if (isset($component)) { $__componentOriginalb27b722146f03c6dfe842ca439b6bf3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb27b722146f03c6dfe842ca439b6bf3b = $attributes; } ?>
<?php $component = App\View\Components\Navbar\Item::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('navbar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Navbar\Item::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                            <?php if (isset($component)) { $__componentOriginal4bc111d20df937dde026191dc017d829 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4bc111d20df937dde026191dc017d829 = $attributes; } ?>
<?php $component = App\View\Components\Navbar\Link::resolve(['label' => ''.e(__('Premium Membership')).'','href' => '#','icon' => 'tabler-diamond','triggerType' => 'modal'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('navbar.link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Navbar\Link::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                                 <?php $__env->slot('modal', null, []); ?> 
                                    <?php if ($__env->exists('premium-support.index')) echo $__env->make('premium-support.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                 <?php $__env->endSlot(); ?>
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4bc111d20df937dde026191dc017d829)): ?>
<?php $attributes = $__attributesOriginal4bc111d20df937dde026191dc017d829; ?>
<?php unset($__attributesOriginal4bc111d20df937dde026191dc017d829); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4bc111d20df937dde026191dc017d829)): ?>
<?php $component = $__componentOriginal4bc111d20df937dde026191dc017d829; ?>
<?php unset($__componentOriginal4bc111d20df937dde026191dc017d829); ?>
<?php endif; ?>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb27b722146f03c6dfe842ca439b6bf3b)): ?>
<?php $attributes = $__attributesOriginalb27b722146f03c6dfe842ca439b6bf3b; ?>
<?php unset($__attributesOriginalb27b722146f03c6dfe842ca439b6bf3b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb27b722146f03c6dfe842ca439b6bf3b)): ?>
<?php $component = $__componentOriginalb27b722146f03c6dfe842ca439b6bf3b; ?>
<?php unset($__componentOriginalb27b722146f03c6dfe842ca439b6bf3b); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if (isset($component)) { $__componentOriginalb27b722146f03c6dfe842ca439b6bf3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb27b722146f03c6dfe842ca439b6bf3b = $attributes; } ?>
<?php $component = App\View\Components\Navbar\Item::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('navbar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Navbar\Item::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                    <?php if (isset($component)) { $__componentOriginal126c4535e58580b638d09b22979d8bfc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal126c4535e58580b638d09b22979d8bfc = $attributes; } ?>
<?php $component = App\View\Components\Navbar\Divider::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('navbar.divider'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Navbar\Divider::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal126c4535e58580b638d09b22979d8bfc)): ?>
<?php $attributes = $__attributesOriginal126c4535e58580b638d09b22979d8bfc; ?>
<?php unset($__attributesOriginal126c4535e58580b638d09b22979d8bfc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal126c4535e58580b638d09b22979d8bfc)): ?>
<?php $component = $__componentOriginal126c4535e58580b638d09b22979d8bfc; ?>
<?php unset($__componentOriginal126c4535e58580b638d09b22979d8bfc); ?>
<?php endif; ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb27b722146f03c6dfe842ca439b6bf3b)): ?>
<?php $attributes = $__attributesOriginalb27b722146f03c6dfe842ca439b6bf3b; ?>
<?php unset($__attributesOriginalb27b722146f03c6dfe842ca439b6bf3b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb27b722146f03c6dfe842ca439b6bf3b)): ?>
<?php $component = $__componentOriginalb27b722146f03c6dfe842ca439b6bf3b; ?>
<?php unset($__componentOriginalb27b722146f03c6dfe842ca439b6bf3b); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginalb27b722146f03c6dfe842ca439b6bf3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb27b722146f03c6dfe842ca439b6bf3b = $attributes; } ?>
<?php $component = App\View\Components\Navbar\Item::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('navbar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Navbar\Item::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'group-[&.navbar-shrinked]/body:hidden']); ?>
                    <?php if (isset($component)) { $__componentOriginalc9fbb615564265c2282eb7b2e59c05dd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc9fbb615564265c2282eb7b2e59c05dd = $attributes; } ?>
<?php $component = App\View\Components\Navbar\Label::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('navbar.label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Navbar\Label::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                        <?php echo e(__('Credits')); ?>

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc9fbb615564265c2282eb7b2e59c05dd)): ?>
<?php $attributes = $__attributesOriginalc9fbb615564265c2282eb7b2e59c05dd; ?>
<?php unset($__attributesOriginalc9fbb615564265c2282eb7b2e59c05dd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc9fbb615564265c2282eb7b2e59c05dd)): ?>
<?php $component = $__componentOriginalc9fbb615564265c2282eb7b2e59c05dd; ?>
<?php unset($__componentOriginalc9fbb615564265c2282eb7b2e59c05dd); ?>
<?php endif; ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb27b722146f03c6dfe842ca439b6bf3b)): ?>
<?php $attributes = $__attributesOriginalb27b722146f03c6dfe842ca439b6bf3b; ?>
<?php unset($__attributesOriginalb27b722146f03c6dfe842ca439b6bf3b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb27b722146f03c6dfe842ca439b6bf3b)): ?>
<?php $component = $__componentOriginalb27b722146f03c6dfe842ca439b6bf3b; ?>
<?php unset($__componentOriginalb27b722146f03c6dfe842ca439b6bf3b); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginalb27b722146f03c6dfe842ca439b6bf3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb27b722146f03c6dfe842ca439b6bf3b = $attributes; } ?>
<?php $component = App\View\Components\Navbar\Item::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('navbar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Navbar\Item::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'pb-navbar-link-pb pe-navbar-link-pe ps-navbar-link-ps pt-navbar-link-pt group-[&.navbar-shrinked]/body:hidden']); ?>
                    <?php if (isset($component)) { $__componentOriginalb223a1c6a53e59e4d348d69e8bc0381b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb223a1c6a53e59e4d348d69e8bc0381b = $attributes; } ?>
<?php $component = App\View\Components\CreditList::resolve(['modalTriggerPos' => 'block'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('credit-list'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\CreditList::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['expanded-modal-trigger' => 'true']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb223a1c6a53e59e4d348d69e8bc0381b)): ?>
<?php $attributes = $__attributesOriginalb223a1c6a53e59e4d348d69e8bc0381b; ?>
<?php unset($__attributesOriginalb223a1c6a53e59e4d348d69e8bc0381b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb223a1c6a53e59e4d348d69e8bc0381b)): ?>
<?php $component = $__componentOriginalb223a1c6a53e59e4d348d69e8bc0381b; ?>
<?php unset($__componentOriginalb223a1c6a53e59e4d348d69e8bc0381b); ?>
<?php endif; ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb27b722146f03c6dfe842ca439b6bf3b)): ?>
<?php $attributes = $__attributesOriginalb27b722146f03c6dfe842ca439b6bf3b; ?>
<?php unset($__attributesOriginalb27b722146f03c6dfe842ca439b6bf3b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb27b722146f03c6dfe842ca439b6bf3b)): ?>
<?php $component = $__componentOriginalb27b722146f03c6dfe842ca439b6bf3b; ?>
<?php unset($__componentOriginalb27b722146f03c6dfe842ca439b6bf3b); ?>
<?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($setting->feature_affilates && \auth()->user()?->affiliate_status == 1): ?>
                    <?php if (isset($component)) { $__componentOriginalb27b722146f03c6dfe842ca439b6bf3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb27b722146f03c6dfe842ca439b6bf3b = $attributes; } ?>
<?php $component = App\View\Components\Navbar\Item::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('navbar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Navbar\Item::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'group-[&.navbar-shrinked]/body:hidden']); ?>
                        <?php if (isset($component)) { $__componentOriginal126c4535e58580b638d09b22979d8bfc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal126c4535e58580b638d09b22979d8bfc = $attributes; } ?>
<?php $component = App\View\Components\Navbar\Divider::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('navbar.divider'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Navbar\Divider::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal126c4535e58580b638d09b22979d8bfc)): ?>
<?php $attributes = $__attributesOriginal126c4535e58580b638d09b22979d8bfc; ?>
<?php unset($__attributesOriginal126c4535e58580b638d09b22979d8bfc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal126c4535e58580b638d09b22979d8bfc)): ?>
<?php $component = $__componentOriginal126c4535e58580b638d09b22979d8bfc; ?>
<?php unset($__componentOriginal126c4535e58580b638d09b22979d8bfc); ?>
<?php endif; ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb27b722146f03c6dfe842ca439b6bf3b)): ?>
<?php $attributes = $__attributesOriginalb27b722146f03c6dfe842ca439b6bf3b; ?>
<?php unset($__attributesOriginalb27b722146f03c6dfe842ca439b6bf3b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb27b722146f03c6dfe842ca439b6bf3b)): ?>
<?php $component = $__componentOriginalb27b722146f03c6dfe842ca439b6bf3b; ?>
<?php unset($__componentOriginalb27b722146f03c6dfe842ca439b6bf3b); ?>
<?php endif; ?>

                    <?php if (isset($component)) { $__componentOriginalb27b722146f03c6dfe842ca439b6bf3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb27b722146f03c6dfe842ca439b6bf3b = $attributes; } ?>
<?php $component = App\View\Components\Navbar\Item::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('navbar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Navbar\Item::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'group-[&.navbar-shrinked]/body:hidden']); ?>
                        <?php if (isset($component)) { $__componentOriginalc9fbb615564265c2282eb7b2e59c05dd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc9fbb615564265c2282eb7b2e59c05dd = $attributes; } ?>
<?php $component = App\View\Components\Navbar\Label::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('navbar.label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Navbar\Label::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                            <?php echo e(__('Affiliation')); ?>

                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc9fbb615564265c2282eb7b2e59c05dd)): ?>
<?php $attributes = $__attributesOriginalc9fbb615564265c2282eb7b2e59c05dd; ?>
<?php unset($__attributesOriginalc9fbb615564265c2282eb7b2e59c05dd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc9fbb615564265c2282eb7b2e59c05dd)): ?>
<?php $component = $__componentOriginalc9fbb615564265c2282eb7b2e59c05dd; ?>
<?php unset($__componentOriginalc9fbb615564265c2282eb7b2e59c05dd); ?>
<?php endif; ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb27b722146f03c6dfe842ca439b6bf3b)): ?>
<?php $attributes = $__attributesOriginalb27b722146f03c6dfe842ca439b6bf3b; ?>
<?php unset($__attributesOriginalb27b722146f03c6dfe842ca439b6bf3b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb27b722146f03c6dfe842ca439b6bf3b)): ?>
<?php $component = $__componentOriginalb27b722146f03c6dfe842ca439b6bf3b; ?>
<?php unset($__componentOriginalb27b722146f03c6dfe842ca439b6bf3b); ?>
<?php endif; ?>

                    <?php if (isset($component)) { $__componentOriginalb27b722146f03c6dfe842ca439b6bf3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb27b722146f03c6dfe842ca439b6bf3b = $attributes; } ?>
<?php $component = App\View\Components\Navbar\Item::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('navbar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Navbar\Item::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'pb-navbar-link-pb pe-navbar-link-pe ps-navbar-link-ps pt-navbar-link-pt group-[&.navbar-shrinked]/body:hidden']); ?>
                        <div
                            class="lqd-navbar-affiliation inline-block w-full rounded-xl border border-navbar-divider px-8 py-4 text-center text-2xs leading-tight transition-border">
                            <p class="m-0 mb-2 text-[20px] not-italic">🎁</p>
                            <p class="mb-4"><?php echo e(__('Invite your friend and get')); ?>

                                <?php echo e($setting->affiliate_commission_percentage); ?>%
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($is_onetime_commission): ?>
                                    <?php echo e(__('on their first purchase.')); ?>

                                <?php else: ?>
                                    <?php echo e(__('on all their purchases.')); ?>

                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </p>
                            <?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['href' => ''.e(route('dashboard.user.affiliates.index')).'','variant' => 'ghost-shadow'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-3xs']); ?>
                                <?php echo e(__('Invite')); ?>

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
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb27b722146f03c6dfe842ca439b6bf3b)): ?>
<?php $attributes = $__attributesOriginalb27b722146f03c6dfe842ca439b6bf3b; ?>
<?php unset($__attributesOriginalb27b722146f03c6dfe842ca439b6bf3b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb27b722146f03c6dfe842ca439b6bf3b)): ?>
<?php $component = $__componentOriginalb27b722146f03c6dfe842ca439b6bf3b; ?>
<?php unset($__componentOriginalb27b722146f03c6dfe842ca439b6bf3b); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </ul>
        </nav>
    </div>
</aside>
<?php /**PATH D:\omini_customization\OminiAI\resources\views/default/components/navbar/navbar-default.blade.php ENDPATH**/ ?>