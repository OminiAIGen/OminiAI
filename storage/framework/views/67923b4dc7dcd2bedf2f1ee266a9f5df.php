<?php
	$items = app(\App\Services\Common\MenuService::class)->generate();

	$user = auth()->user();
	$isAdmin = $user?->isAdmin();

?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
	<?php
		// Cache values once
		$isActive       = data_get($item, 'is_active', false);
		$showCondition  = data_get($item, 'show_condition', true);
		$isAdminOnly    = data_get($item, 'is_admin', false);
		$childrenCount  = data_get($item, 'children_count', 0);
		$type           = data_get($item, 'type');

		// Skip early if plan doesn’t allow it
		if (!\App\Helpers\Classes\PlanHelper::planMenuCheck($userPlan, $key)) {
			continue;
		}

		// Skip if inactive or condition fails
		if (!$isActive || !$showCondition) {
			continue;
		}

		// Skip if admin-only and user not allowed
		if ($isAdminOnly && (!$isAdmin || !$user->checkPermission($key))) {
			continue;
		}
	?>

	
	<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($childrenCount): ?>
		<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'label'): ?>
			<?php if ($__env->exists('default.components.navbar.partials.types.label-collapsible')) echo $__env->make('default.components.navbar.partials.types.label-collapsible', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		<?php else: ?>
			<?php if ($__env->exists('default.components.navbar.partials.types.item-dropdown')) echo $__env->make('default.components.navbar.partials.types.item-dropdown', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
	<?php else: ?>
		<?php if ($__env->exists('default.components.navbar.partials.types.' . $type)) echo $__env->make('default.components.navbar.partials.types.' . $type, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH D:\omini_customization\OminiAI\resources\views/default/panel/layout/partials/menu.blade.php ENDPATH**/ ?>