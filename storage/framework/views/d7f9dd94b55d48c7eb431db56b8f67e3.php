<?php
    $base_class = 'lqd-dropdown flex relative group/dropdown [--dropdown-offset:0px]';
    $trigger_base_class = 'lqd-dropdown-trigger hover:translate-y-0
	before:absolute before:-inset-3 before:pointer-events-none
	focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2
	group-[&.lqd-is-active]/dropdown:before:pointer-events-auto';
    $dropdown_base_class = 'lqd-dropdown-dropdown opacity-0 invisible z-[1000] translate-y-1 pointer-events-none transition
		before:absolute before:bottom-full before:-top-[--dropdown-offset] before:inset-x-0
		[&.dropdown-anchor-bottom]:before:top-full [&.dropdown-anchor-bottom]:before:-bottom-[--dropdown-offset]
		[&.lqd-is-active]/dropdown:opacity-100 [&.lqd-is-active]/dropdown:visible [&.lqd-is-active]/dropdown:translate-y-0 [&.lqd-is-active]/dropdown:pointer-events-auto';
    $dropdown_content_base_class =
        'lqd-dropdown-dropdown-content w-44 border border-dropdown-border rounded-dropdown bg-dropdown-background text-dropdown-foreground shadow-lg shadow-black/5';

    if ($teleport) {
        $dropdown_base_class .= ' fixed top-0';
    } else {
        $dropdown_base_class .= ' absolute top-full';
    }

    if ($anchor === 'start') {
        $dropdown_base_class .= ' start-0';
    } else {
        $dropdown_base_class .= ' end-0';
    }
?>

<div
    class="<?php echo e(@twMerge($base_class, $attributes->get('class'))); ?>"
    style="<?php echo \Illuminate\Support\Arr::toCssStyles([
        '--dropdown-offset: ' . $offsetY . '' => !empty($offsetY),
    ]) ?>"
    x-data="dropdown({ triggerType: '<?php echo e($triggerType); ?>', preferredAnchor: '<?php echo e($anchor); ?>', offsetY: '<?php echo e($offsetY); ?>', teleport: <?php echo e($teleport ? 'true' : 'false'); ?> })"
    x-bind="parent"
    x-ref="parent"
>
    <?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['variant' => ''.e($trigger->attributes->get('variant') ? $trigger->attributes->get('variant') : 'link').'','size' => ''.e($trigger->attributes->get('size', '')).'','type' => ''.e($trigger->attributes->get('type', '')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => ''.e(@twMerge($trigger_base_class, $attributes->get('class:trigger'), $trigger->attributes->get('class'))).'','x-bind' => 'trigger','x-ref' => 'trigger','attributes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($trigger->attributes->except(['variant', 'size', 'type']))]); ?>
        <?php echo e($trigger); ?>

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

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($teleport): ?>
        <template x-teleport="body">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <div
        class="<?php echo e(@twMerge($dropdown_base_class, $attributes->get('class:dropdown-dropdown'))); ?>"
        style="<?php echo \Illuminate\Support\Arr::toCssStyles([
            '--dropdown-offset: ' . $offsetY . '' => !empty($offsetY),
        ]) ?>"
        :class="{ 'lqd-is-active': open }"
        x-bind="dropdown"
        x-ref="dropdown"
    >
        <div
            class="<?php echo e(@twMerge($dropdown_content_base_class, $attributes->get('class:dropdown'), $dropdown->attributes->get('class'))); ?>"
            <?php echo e($dropdown->attributes->only('x-bind:class')); ?>

        >
            <?php echo e($dropdown); ?>

        </div>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($teleport): ?>
        </template>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH D:\omini_customization\OminiAI\resources\views/default/components/dropdown/dropdown.blade.php ENDPATH**/ ?>