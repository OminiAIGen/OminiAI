<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($recently_launched) > 0): ?>
    <ul>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $recently_launched; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item && $item->slug): ?>
				<li class="border-b px-3 py-2 transition-colors last:border-b-0 hover:bg-foreground/5">
					<a
						class="flex items-center gap-2 text-heading-foreground"
						href="<?php echo e(route('dashboard.user.openai.documents.single', $item->slug)); ?>"
					>
						<?php if (isset($component)) { $__componentOriginalfcf158437a8f91c7c912d5fdba2a4dd9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfcf158437a8f91c7c912d5fdba2a4dd9 = $attributes; } ?>
<?php $component = App\View\Components\LqdIcon::resolve(['size' => 'lg'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('lqd-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\LqdIcon::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['style' => 'background: '.e($item->generator->color).'']); ?>
							<span class="flex size-5">
								<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->generator->image !== 'none'): ?>
									<?php echo html_entity_decode($item->generator->image); ?>

								<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
							</span>
						 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfcf158437a8f91c7c912d5fdba2a4dd9)): ?>
<?php $attributes = $__attributesOriginalfcf158437a8f91c7c912d5fdba2a4dd9; ?>
<?php unset($__attributesOriginalfcf158437a8f91c7c912d5fdba2a4dd9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfcf158437a8f91c7c912d5fdba2a4dd9)): ?>
<?php $component = $__componentOriginalfcf158437a8f91c7c912d5fdba2a4dd9; ?>
<?php unset($__componentOriginalfcf158437a8f91c7c912d5fdba2a4dd9); ?>
<?php endif; ?>
						<?php echo e($item->title ?: 'Document'); ?>

						<small class="ms-auto text-foreground/50"><?php echo e($item->generator->type == 'text' ? __('Document') : __(ucfirst($item->generator->type))); ?></small>
					</a>
				</li>
			<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </ul>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($recently_launched) and $recently_launched == 'null'): ?>
    <div class="block p-6 text-center font-medium text-heading-foreground">
        <h3 class="mb-2"><?php echo e(__('There is no recent lunch.')); ?></h3>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH D:\omini_customization\OminiAI\resources\views/default/panel/layout/includes/recently-lunched.blade.php ENDPATH**/ ?>