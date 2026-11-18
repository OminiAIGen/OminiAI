<?php
    $container_base_class = 'lqd-input-container relative';
    $input_base_class = 'lqd-input block peer w-full px-4 py-2 border border-input-border bg-input-background text-input-foreground text-base ring-offset-0 transition-colors
		focus:border-secondary focus:outline-0 focus:ring focus:ring-secondary
		dark:focus:ring-foreground/10
		sm:text-2xs';
    $input_checkbox_base_class = 'lqd-input peer border-input-border
		focus:ring focus:ring-secondary
		dark:focus:ring-foreground/10';
    $input_checkbox_custom_wrapper_base_class = 'lqd-input-checkbox-custom-wrap inline-flex items-center justify-center size-[18px] shrink-0 rounded-full bg-foreground/10 text-heading-foreground bg-center bg-no-repeat
		peer-checked:bg-primary/[7%] peer-checked:text-primary';
    $label_base_class = 'lqd-input-label flex cursor-pointer items-center gap-2 text-2xs font-medium leading-none text-label transition';
    $label_extra_base_class = 'ms-auto';

    $variations = [
        'size' => [
            'none' => 'lqd-input-size-none',
            'sm' => 'lqd-input-sm h-9',
            'md' => 'lqd-input-md h-10',
            'lg' => 'lqd-input-lg h-11',
            'xl' => 'lqd-input-xl h-14 px-6',
        ]
    ];

    if ($type === 'textarea') {
        $size = 'none';
    }

    if ($switcher) {
        $input_checkbox_base_class .= ' lqd-input-switcher border-2 border-input-border [--input-rounded-multiplier:5] cursor-pointer appearance-none [background-size:1.3rem] bg-left bg-no-repeat transition-all
			checked:bg-right checked:bg-heading-foreground checked:border-heading-foreground
			dark:checked:bg-label dark:checked:border-label';

		if ( $switcherFill ) {
			$input_checkbox_base_class .= ' lqd-input-switcher-fill';
		}

        $variations['size'] = [
            'sm' => 'lqd-input-sm w-[34px] h-[18px]',
            'md' => 'lqd-input-md w-12 h-6',
        ];
    } elseif ($custom) {
        $input_checkbox_base_class = 'lqd-input peer rounded size-0 invisible absolute top-0 start-0';
    }

    if ($type !== 'checkbox' && $type !== 'radio') {
        $label_base_class .= ' mb-3';
    }

    if (!empty($label) && empty($id)) {
        $id = str()->random(10);
    }

    if ($stepper) {
        $input_base_class .= ' lqd-input-stepper appearance-none text-center px-2';
    }

    $size = isset($variations['size'][$size]) ? $variations['size'][$size] : $variations['size']['md'];
?>

<div
    <?php echo e($attributes->withoutTwMergeClasses()->twMergeFor('container', $container_base_class, $containerClass, $attributes->get('class:container'))); ?>

    <?php if($attributes->has('x-show')): ?> x-show="<?php echo e($attributes->get('x-show')); ?>" <?php endif; ?>
    <?php if($type === 'password'): ?> x-data='{
		type: "<?php echo e($type); ?>",
		get inputValueVisible() { return this.type !== "password" },
		toggleType() {
			this.type = this.type === "text" ? "password" : "text";
		}
    }' <?php endif; ?>
    <?php if($stepper): ?> x-data='{
		value: <?php echo e(!empty($value) ? $value : 0); ?>,
		min: <?php echo e($attributes->has('min') ? $attributes->get('min') : 0); ?>,
		max: <?php echo e($attributes->has('max') ? $attributes->get('max') : 999999); ?>,
		step: <?php echo e($attributes->has('step') ? $attributes->get('step') : 1); ?>,
		setValue(value) {
			this.value = value;
			this.$refs.input.setAttribute("value", this.value);
			this.$refs.input.dispatchEvent(new Event("input"));
			this.$refs.input.dispatchEvent(new Event("change"));
		}
	}' <?php endif; ?>
>
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($label) || ($type === 'checkbox' || $type === 'radio')): ?>
        <label
            <?php echo e($attributes->withoutTwMergeClasses()->twMergeFor('label', $label_base_class, $attributes->get('class:label'))); ?>

            <?php if($label instanceof \Illuminate\View\ComponentSlot): ?>
                <?php echo e($label->attributes->whereDoesntStartWith('class')); ?>

            <?php endif; ?>
			for="<?php echo e($id); ?>"
        >
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'checkbox' || $type === 'radio'): ?>
                <input
                    id="<?php echo e($id); ?>"
                    <?php echo e($attributes->withoutTwMergeClasses()->twMerge($input_checkbox_base_class, $size, $attributes->get('class'))); ?>

                    name="<?php echo e($name); ?>"
                    type=<?php echo e($type); ?>

                    <?php if($value): ?> value=<?php echo e($value); ?> <?php endif; ?>
                    <?php echo e($attributes); ?>

                    <?php if($attributes->has('x-model')): ?> x-model="<?php echo e($attributes->get('x-model')); ?>" <?php endif; ?>
                >
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($custom): ?>
                    <span <?php echo e($attributes->withoutTwMergeClasses()->twMergeFor('custom-wrap', $input_checkbox_custom_wrapper_base_class)); ?>></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <span <?php echo e($attributes->withoutTwMergeClasses()->twMergeFor('label-txt', 'lqd-input-label-txt', $attributes->get('class:label-txt'))); ?>>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($labelIcon)): ?>
					<?php echo $labelIcon; ?>

				<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
				<?php echo e($label); ?>

				<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attributes->has('required')): ?>
					<span class="lqd-input-label-required text-red-500">*</span>
				<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </span>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'checkbox' || $type === 'radio'): ?>
                <?php echo e($slot); ?>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($labelExtra)): ?>
                <span <?php echo e($attributes->withoutTwMergeClasses()->twMergeFor('label-extra', $label_extra_base_class, $attributes->get('class:label-extra'))); ?>>
                    <?php echo e($labelExtra); ?>

                </span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($tooltip)): ?>
                <?php if (isset($component)) { $__componentOriginal9acd6c99af8d8c9491f2759be41ef2c4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9acd6c99af8d8c9491f2759be41ef2c4 = $attributes; } ?>
<?php $component = App\View\Components\InfoTooltip::resolve(['text' => ''.$tooltip.''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('info-tooltip'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\InfoTooltip::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9acd6c99af8d8c9491f2759be41ef2c4)): ?>
<?php $attributes = $__attributesOriginal9acd6c99af8d8c9491f2759be41ef2c4; ?>
<?php unset($__attributesOriginal9acd6c99af8d8c9491f2759be41ef2c4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9acd6c99af8d8c9491f2759be41ef2c4)): ?>
<?php $component = $__componentOriginal9acd6c99af8d8c9491f2759be41ef2c4; ?>
<?php unset($__componentOriginal9acd6c99af8d8c9491f2759be41ef2c4); ?>
<?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </label>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'password' || !empty($icon) || !empty($action) || $stepper): ?>
        <div class="relative">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type !== 'checkbox' && $type !== 'radio' && $type !== 'select' && $type !== 'textarea' && $type !== 'color'): ?>
        <input
            id="<?php echo e($id); ?>"
            <?php echo e($attributes->withoutTwMergeClasses()->twMerge($input_base_class, $size, $attributes->get('class'))); ?>

            name="<?php echo e($name); ?>"
            value="<?php echo e($value); ?>"
            <?php if($type === 'password'): ?> :type="type" <?php endif; ?>
            type=<?php echo e($type); ?>

            placeholder="<?php echo $placeholder; ?>"
            <?php echo e($attributes); ?>

            <?php if($stepper): ?> :value="(value).toString().includes('.') ? parseFloat(value).toFixed(2) : value" x-ref="input" <?php endif; ?>
            <?php if($attributes->has('x-ref') && filled($attributes->get('x-ref'))): ?> x-ref="<?php echo e($attributes->get('x-ref')); ?>" <?php endif; ?>
            <?php if($attributes->has('x-trap') && filled($attributes->get('x-trap'))): ?> x-trap="<?php echo e($attributes->get('x-trap')); ?>" <?php endif; ?>
            <?php if($attributes->has('x-model')): ?> x-model="<?php echo e($attributes->get('x-model')); ?>" <?php endif; ?>
        />

        <?php echo e($slot); ?>

    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'select'): ?>
        <select
            id="<?php echo e($id); ?>"
            <?php echo e($attributes->withoutTwMergeClasses()->twMerge('cursor-pointer', $input_base_class, $size, $attributes->get('class'))); ?>

            name="<?php echo e($name); ?>"
            value="<?php echo e($value); ?>"
            placeholder="<?php echo $placeholder; ?>"
            <?php echo e($attributes); ?>

            <?php if($attributes->has('x-model')): ?> x-model="<?php echo e($attributes->get('x-model')); ?>" <?php endif; ?>
        >
            <?php echo e($slot); ?>

        </select>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'textarea'): ?>
        <textarea
            id="<?php echo e($id); ?>"
            <?php echo e($attributes->withoutTwMergeClasses()->twMerge($input_base_class, $size, $attributes->get('class'))); ?>

            name="<?php echo e($name); ?>"
            value="<?php echo e($value); ?>"
            placeholder="<?php echo $placeholder; ?>"
            <?php echo e($attributes); ?>

            <?php if($attributes->has('x-model')): ?> x-model="<?php echo e($attributes->get('x-model')); ?>" <?php endif; ?>
        ><?php echo e($slot); ?></textarea>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'color'): ?>
        <div
            <?php echo e($attributes->withoutTwMergeClasses()->twMerge($input_base_class, 'flex items-center gap-3', $size, $attributes->get('class'))); ?>

            x-data="liquidColorPicker({ colorVal: '<?php echo e($value); ?>' })"
        >
            <div
                class="lqd-input-color-wrap relative size-5 shrink-0 gap-4 overflow-hidden rounded-full border shadow-sm focus-within:ring focus-within:ring-secondary"
                x-ref="colorInputWrap"
            >
                <span
                    class="absolute left-0 top-0 size-full"
                    :style="{ backgroundColor: colorVal }"
                ></span>
            </div>

			<input
				<?php echo e($attributes->twMergeFor('input', 'grow border-none bg-transparent text-inherit outline-none')); ?>

				id="<?php echo e($id); ?>"
				name="<?php echo e($name); ?>"
				value="<?php echo e($value); ?>"
				type="text"
				placeholder="<?php echo $placeholder; ?>"
				:value="colorVal"
				x-ref="colorInput"
				<?php if($attributes->has('x-model')): ?> x-model="<?php echo e($attributes->get('x-model')); ?>" <?php endif; ?>
				<?php echo e($attributes); ?>

				@change="picker?.setColor($event.target.value);"
				@keydown.enter.prevent="picker?.setColor($event.target.value);"
				@focus="picker?.open(); $el.select()"
			/>
            <?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['variant' => 'outline','size' => 'sm','type' => 'button'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['attributes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attributes->twMergeFor('clear-btn', 'hidden text-2xs font-medium')),'@click.prevent' => 'picker?.clear()',':class' => '{ \'hidden\': colorVal === \'\' }']); ?>
                <?php echo app('translator')->get('Clear'); ?>
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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'password'): ?>
        <button
            class="lqd-show-password absolute end-3 top-1/2 z-10 inline-flex size-7 -translate-y-1/2 cursor-pointer items-center justify-center rounded bg-none transition-colors hover:bg-foreground/10"
            type="button"
            @click="toggleType()"
        >
            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-eye'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5','stroke-width' => '1.5',':class' => 'inputValueVisible ? \'hidden\' : \'\'']); ?>
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
            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-eye-off'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'hidden w-5','stroke-width' => '1.5',':class' => 'inputValueVisible ? \'!block\' : \'hidden\'']); ?>
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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($icon)): ?>
        <?php echo $icon; ?>

    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($action)): ?>
        <div
			<?php echo e($attributes->twMergeFor('action', "absolute inset-y-0 end-0 border-s")); ?>

		>
            <?php echo e($action); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($stepper): ?>
        <button
            class="lqd-stepper-btn absolute start-0 top-0 inline-flex aspect-square h-full w-10 items-center justify-center rounded-s-input transition-colors hover:bg-heading-foreground hover:text-heading-background"
            type="button"
            @click="setValue(Math.max(min, value - step))"
        >
            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-minus'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4','stroke-width' => '1.5']); ?>
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
        <button
            class="lqd-stepper-btn absolute end-0 top-0 inline-flex aspect-square h-full w-10 items-center justify-center rounded-e-input transition-colors hover:bg-heading-foreground hover:text-heading-background"
            type="button"
            @click="setValue(Math.min(max, value + step))"
        >
            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-plus'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4','stroke-width' => '1.5']); ?>
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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'password' || !empty($icon) || !empty($action) || $stepper): ?>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'color'): ?>
    <?php if (! $__env->hasRenderedOnce('420feeba-35a2-4221-9ad9-907163425be7')): $__env->markAsRenderedOnce('420feeba-35a2-4221-9ad9-907163425be7');
$__env->startPush('script'); ?>
        <link
            rel="stylesheet"
            href="<?php echo e(custom_theme_url('assets/libs/jscolorpicker/dist/colorpicker.css')); ?>"
        >
        <script src="<?php echo e(custom_theme_url('assets/libs/jscolorpicker/dist/colorpicker.iife.min.js')); ?>"></script>
	<?php $__env->stopPush(); endif; ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'select' && $attributes->has('multiple')): ?>
	<?php if (! $__env->hasRenderedOnce('e8568c11-79b7-45d8-8d64-a103e328cf9f')): $__env->markAsRenderedOnce('e8568c11-79b7-45d8-8d64-a103e328cf9f');
$__env->startPush('css'); ?>
	<link
		href="<?php echo e(custom_theme_url('/assets/libs/tom-select/dist/css/tom-select.min.css')); ?>"
		rel="stylesheet"
	/>
	<?php $__env->stopPush(); endif; ?>

    <?php if (! $__env->hasRenderedOnce('19d65554-add2-43ce-8502-490936b1a107')): $__env->markAsRenderedOnce('19d65554-add2-43ce-8502-490936b1a107');
$__env->startPush('script'); ?>
        <script src="<?php echo e(custom_theme_url('/assets/libs/tom-select/dist/js/tom-select.complete.min.js')); ?>"></script>

        <script>
            (() => {
                document.addEventListener('alpine:init', () => {
                    _.defer(() => {

                        const allSelectElements = document.querySelectorAll('select[multiple]');

                        allSelectElements.forEach(el => {
                            el.style.display = 'none';
                            const elId = el.id;
							const label = el.closest('.lqd-input-container')?.querySelector('.lqd-input-label');

                            const tomSelect = new TomSelect(el, {
                                create: <?php echo e($addNew ? 'true' : 'false'); ?> && elId === '<?php echo e($id); ?>',
                                plugins: {
                                    remove_button: {
                                        title: '<?php echo e(__('Remove this item')); ?>',
                                    }
                                },
                            });

							const buttonsWrapper = document.createElement('div');
							buttonsWrapper.classList.add('flex', 'flex-wrap', 'items-center', 'gap-2.5', 'mt-1');

                            const selectAllBtn = document.createElement('button');
							selectAllBtn.classList.add('text-[12px]', 'underline', 'font-medium');
                            selectAllBtn.setAttribute('type', 'button');
                            selectAllBtn.innerText = '<?php echo e(__('Select All')); ?>';

                            const deselectAllBtn = document.createElement('button');
							deselectAllBtn.classList.add('text-[12px]', 'underline', 'font-medium', 'hidden');
                            deselectAllBtn.setAttribute('type', 'button');
                            deselectAllBtn.innerText = '<?php echo e(__('Clear Selection')); ?>';

							selectAllBtn.addEventListener('click', () => {
								let values = Object.keys(tomSelect.options);
								tomSelect.addItems(values);
							});
							deselectAllBtn.addEventListener('click', () => {
								tomSelect.clear();
							});

							if ( !Object.keys(tomSelect.options).length ) {
								buttonsWrapper.classList.add('hidden');
							}

							if ( label ) {
								buttonsWrapper.classList.remove('mt-1');
								buttonsWrapper.classList.add('ms-auto');
								label.insertAdjacentElement('beforeend', buttonsWrapper);
							} else {
								tomSelect.wrapper.insertAdjacentElement('afterend', buttonsWrapper);
							}

                            buttonsWrapper.appendChild(selectAllBtn);
                            buttonsWrapper.appendChild(deselectAllBtn);

							tomSelect.on('change', () => {
								const total = Object.keys(tomSelect.options).length;
								const currentValues = tomSelect.getValue();

								buttonsWrapper.classList.toggle('hidden', !currentValues.length);
								selectAllBtn.classList.toggle('hidden', currentValues.length === total);
								deselectAllBtn.classList.toggle('hidden', !currentValues.length);
							})
                        })
                    })
                })
            })
            ();
        </script>
    <?php $__env->stopPush(); endif; ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH D:\omini_customization\OminiAI\resources\views/default/components/forms/input.blade.php ENDPATH**/ ?>