<?php
    $is_active = false;

    foreach ($item['children'] as $child) {
        if (!Route::has($child['route'])) {
            continue;
        }

        $child_href = $child['route_slug'] ? route($child['route'], $child['route_slug']) : route($child['route']);

        if ($child_href === url()->current()) {
            $is_active = true;
            break;
        }
    }

    $storage_key = 'lqd-menu-collapse-' . data_get($item, 'key', '');
?>

<li
    class="lqd-navbar-item lqd-navbar-label-collapsible group/nav-item relative"
    x-data="{
        isOpen: <?php echo e($is_active ? 'true' : "localStorage.getItem('" . $storage_key . "') !== '0'"); ?>,
        toggle() {
            this.isOpen = !this.isOpen;
            localStorage.setItem('<?php echo e($storage_key); ?>', this.isOpen ? '1' : '0');
        }
    }"
>
    <span
        class="lqd-navbar-label-wrap flex w-full min-w-0 cursor-pointer items-center gap-2 pb-navbar-link-pb pe-navbar-link-pe ps-navbar-link-ps pt-navbar-link-pt lg:group-[&.navbar-shrinked]/body:hidden"
        @click="toggle()"
    >
        <span
            class="lqd-navbar-label inline-block min-w-0 max-w-full flex-1 overflow-hidden text-ellipsis text-4xs uppercase tracking-widest lg:group-[&.navbar-shrinked]/body:w-full lg:group-[&.navbar-shrinked]/body:px-2 lg:group-[&.navbar-shrinked]/body:text-center"
        >
            <?php echo e(__(data_get($item, 'label'))); ?>

        </span>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty(data_get($item, 'badge'))): ?>
            <?php if (isset($component)) { $__componentOriginald30cf9cba6bb540c6bffcc9785239679 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald30cf9cba6bb540c6bffcc9785239679 = $attributes; } ?>
<?php $component = App\View\Components\Badge::resolve(['variant' => 'secondary'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Badge::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'shrink-0 rounded-md text-[0.5625rem] group-[&.navbar-shrinked]/body:hidden']); ?>
                <?php echo e(mb_strtoupper(data_get($item, 'badge'))); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald30cf9cba6bb540c6bffcc9785239679)): ?>
<?php $attributes = $__attributesOriginald30cf9cba6bb540c6bffcc9785239679; ?>
<?php unset($__attributesOriginald30cf9cba6bb540c6bffcc9785239679); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald30cf9cba6bb540c6bffcc9785239679)): ?>
<?php $component = $__componentOriginald30cf9cba6bb540c6bffcc9785239679; ?>
<?php unset($__componentOriginald30cf9cba6bb540c6bffcc9785239679); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <span
            class="lqd-nav-label-chevron shrink-0 transition-transform group-[&.navbar-shrinked]/body:hidden"
            :class="{ 'rotate-180': isOpen }"
        >
            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-chevron-down'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-3','stroke-width' => '2.5']); ?>
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
        </span>
    </span>

    <ul
        class="lqd-navbar-label-children lg:group-[&.navbar-shrinked]/body:!block lg:group-[&.navbar-shrinked]/body:!h-auto lg:group-[&.navbar-shrinked]/body:!overflow-visible"
        x-show="isOpen"
        x-collapse
    >
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $item['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $childKey = data_get($child, 'key');
            ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(\App\Helpers\Classes\PlanHelper::planMenuCheck($userPlan, $childKey)): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(data_get($child, 'show_condition', true) && data_get($item, 'is_active')): ?>
                    <?php
                        $child_href =
                            $child['route_slug'] && \App\Helpers\Classes\Helper::hasRoute($child['route'])
                                ? route($child['route'], $child['route_slug'])
                                : route(\App\Helpers\Classes\Helper::hasRoute($child['route']) ? $child['route'] : 'default');
                        $child_is_active = $child_href === url()->current();
                    ?>

                    <?php if (isset($component)) { $__componentOriginalb27b722146f03c6dfe842ca439b6bf3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb27b722146f03c6dfe842ca439b6bf3b = $attributes; } ?>
<?php $component = App\View\Components\Navbar\Item::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('navbar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Navbar\Item::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => ''.e(data_get($item, 'key')).'-'.e(data_get($child, 'key')).'']); ?>
                        <?php if (isset($component)) { $__componentOriginal4bc111d20df937dde026191dc017d829 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4bc111d20df937dde026191dc017d829 = $attributes; } ?>
<?php $component = App\View\Components\Navbar\Link::resolve(['letterIconStyles' => ''.e($child['letter_icon_bg'] ?? '').'','label' => ''.__($child['label']).'','href' => ''.e($child['route']).'','slug' => ''.e($child['route_slug']).'','icon' => ''.e($child['icon']).'','activeCondition' => ''.e($child_is_active).'','letterIcon' => ''.e((int) ($child['letter_icon'] ?? 0)).'','badge' => ''.e(data_get($child, 'badge') ?? '').''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('navbar.link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Navbar\Link::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => ''.e(data_get($child, 'class')).'','class:letter-icon' => ''.e($child['letter_icon_bg'] ?? '').'','onclick' => ''.e(data_get($child, 'onclick') ?? '').'']); ?>
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
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </ul>
</li>
<?php /**PATH D:\omini_customization\OminiAI\resources\views/default/components/navbar/partials/types/label-collapsible.blade.php ENDPATH**/ ?>