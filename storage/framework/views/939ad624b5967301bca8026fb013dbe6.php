<?php
	$isSharedCreditPlan = isset($plan) && $plan->isSharedCreditPlan()
		&& (bool) setting('shared_credit_system_enabled');

	$isSharedCreditUser = !isset($plan) && auth()->check()
		&& auth()->user()->isSharedCreditUser()
		&& (bool) setting('shared_credit_system_enabled');
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isSharedCreditPlan): ?>
	<div class="flex items-center justify-between rounded-xl bg-foreground/[3%] px-5 py-4">
		<div class="flex items-center gap-3">
			<?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-bolt'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-5 text-primary']); ?>
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
			<span class="font-medium text-heading-foreground"><?php echo e(__('Total Credits')); ?></span>
		</div>
		<span class="text-lg font-semibold text-heading-foreground">
			<?php echo is_numeric($plan->shared_credits_amount) ? rtrim(rtrim(number_format((float) $plan->shared_credits_amount, 2), '0'), '.') : ($plan->shared_credits_amount); ?>
		</span>
	</div>
<?php elseif($isSharedCreditUser): ?>
	<?php
		$sharedBalance = (float) auth()->user()->shared_credits;
		$sharedTeam = auth()->user()->myTeam;
		if ($sharedTeam && (!$sharedTeam->exists || !$sharedTeam->isSharedCreditTeam())) {
			$sharedTeam = null;
		}
	?>

	<div class="flex flex-col gap-4">
		<div class="flex items-center justify-between rounded-xl bg-foreground/[3%] px-5 py-4">
			<div class="flex items-center gap-3">
				<?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-bolt'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-5 text-primary']); ?>
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
				<span class="font-medium text-heading-foreground"><?php echo e(__('Your Credits')); ?></span>
			</div>
			<span class="text-lg font-semibold text-heading-foreground">
				<?php echo is_numeric($sharedBalance) ? rtrim(rtrim(number_format((float) $sharedBalance, 2), '0'), '.') : ($sharedBalance); ?>
			</span>
		</div>

		<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sharedTeam): ?>
			<div class="flex items-center justify-between rounded-xl bg-foreground/[3%] px-5 py-4">
				<div class="flex items-center gap-3">
					<?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-users'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-5 text-secondary']); ?>
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
					<span class="font-medium text-heading-foreground"><?php echo e(__('Team Pool')); ?></span>
				</div>
				<span class="text-lg font-semibold text-heading-foreground">
					<?php echo is_numeric($sharedTeam->shared_credits) ? rtrim(rtrim(number_format((float) $sharedTeam->shared_credits, 2), '0'), '.') : ($sharedTeam->shared_credits); ?>
				</span>
			</div>

			<div class="flex items-center justify-between rounded-xl bg-foreground/[3%] px-5 py-4">
				<div class="flex items-center gap-3">
					<?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-calculator'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-5 text-foreground/50']); ?>
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
					<span class="font-medium text-heading-foreground"><?php echo e(__('Total Available')); ?></span>
				</div>
				<span class="text-lg font-semibold text-primary">
					<?php echo is_numeric($sharedBalance + $sharedTeam->shared_credits) ? rtrim(rtrim(number_format((float) $sharedBalance + $sharedTeam->shared_credits, 2), '0'), '.') : ($sharedBalance + $sharedTeam->shared_credits); ?>
				</span>
			</div>
		<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
	</div>
<?php else: ?>
	<table class="mb-4 w-full table-auto border-collapse border">
		<thead>
			<tr class="bg-foreground/10">
				<th class="border p-2 text-start"><?php echo e(__('Model')); ?></th>
				<th class="border p-2 text-end"><?php echo e(__('Credits')); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<?php
					if (isset($plan) && $plan->exists) {
						$drivers = $model->forPlan($plan)->list();
					} else {
						$drivers = $model->forUser($user ?? auth()->user())->list();
					}

					$groupName = $drivers->isNotEmpty() ? $drivers->first()->enum()->subLabel() : '';
					$isUnlimited = $model->checkIfThereUnlimited();
					$credits = $model->totalCredits();
					$tooltip_anchor = $loop->index < 4 ? 'top' : 'bottom';
				?>

				<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isUnlimited && $credits <= 0): ?>
					<?php continue; ?>
				<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
				<tr>
					<td class="flex justify-between border-b p-2">
						<?php echo e($groupName); ?>

						<?php if (isset($component)) { $__componentOriginal9acd6c99af8d8c9491f2759be41ef2c4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9acd6c99af8d8c9491f2759be41ef2c4 = $attributes; } ?>
<?php $component = App\View\Components\InfoTooltip::resolve(['drivers' => $drivers,'anchor' => $tooltip_anchor] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('info-tooltip'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\InfoTooltip::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class:content' => 'max-h-48 overflow-y-auto']); ?>
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
					</td>
					<td class="border p-2 text-end">
						<?php echo e($isUnlimited ? __('Unlimited') : $credits); ?>

					</td>
				</tr>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

			<?php if ($__env->exists('video-dubbing::partials.credit-list-row', [
				'plan' => $plan ?? null,
				'user' => $user ?? null,
			])) echo $__env->make('video-dubbing::partials.credit-list-row', [
				'plan' => $plan ?? null,
				'user' => $user ?? null,
			], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

			<?php if ($__env->exists('ugc-creator::partials.credit-list-row', [
				'plan' => $plan ?? null,
				'user' => $user ?? null,
			])) echo $__env->make('ugc-creator::partials.credit-list-row', [
				'plan' => $plan ?? null,
				'user' => $user ?? null,
			], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

			<?php if ($__env->exists('ai-captions::partials.credit-list-row', [
				'plan' => $plan ?? null,
				'user' => $user ?? null,
			])) echo $__env->make('ai-captions::partials.credit-list-row', [
				'plan' => $plan ?? null,
				'user' => $user ?? null,
			], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

		</tbody>
	</table>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /home/u504467222/domains/omini.ai/public_html/resources/views/default/components/credit-list-partial.blade.php ENDPATH**/ ?>