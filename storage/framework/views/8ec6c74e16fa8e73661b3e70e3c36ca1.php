<?php
	$user = auth()?->user();
	$user_avatar = $user?->avatar;

	if (!$user?->github_token && !$user?->google_token && !$user?->facebook_token) {
	   $user_avatar = '/' . $user_avatar;
	}

	$avatar_url = custom_theme_url($user_avatar);
?>

<?php if (isset($component)) { $__componentOriginal0feec11b8470b4bf00c37924b86dc0af = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0feec11b8470b4bf00c37924b86dc0af = $attributes; } ?>
<?php $component = App\View\Components\Dropdown\Dropdown::resolve(['anchor' => 'end','offsetY' => ''.e($attributes->get('dropdown-offset-y') ?? '20px').''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dropdown.dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Dropdown\Dropdown::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['attributes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attributes->twMerge('header-user-dropdown'))]); ?>
	 <?php $__env->slot('trigger', null, ['class' => ''.e(@twMerge('size-9 p-0', $attributes->get('class:trigger'))).'']); ?> 
		<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($trigger) && filled($trigger)): ?>
			<?php echo e($trigger); ?>

		<?php else: ?>
			<span
				class="inline-block size-full rounded-full bg-cover bg-center"
				style="background-image: url(<?php echo e($avatar_url); ?>)"
				role="img"
				aria-label="<?php echo e(__('User avatar')); ?>"
			></span>
		<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
	 <?php $__env->endSlot(); ?>

	 <?php $__env->slot('dropdown', null, ['class' => 'w-52']); ?> 
		<div class="px-3 pt-3">
			<p class="m-0 text-foreground"><?php echo e($user->fullName()); ?></p>
			<p class="text-3xs text-foreground/70"><?php echo e($user->email); ?></p>
		</div>

		<hr>

		<?php if (isset($component)) { $__componentOriginalb223a1c6a53e59e4d348d69e8bc0381b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb223a1c6a53e59e4d348d69e8bc0381b = $attributes; } ?>
<?php $component = App\View\Components\CreditList::resolve(['modalTriggerPos' => 'block'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('credit-list'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\CreditList::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class:legends' => 'gap-1','class:modal-trigger' => 'text-2xs w-full','modal-trigger-variant' => 'ghost-shadow','expanded-modal-trigger' => true]); ?>
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

		<hr>

		<div class="pb-2 text-2xs">
			<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
			   ['route' => 'dashboard.user.2fa.activate', 'label' => '2-Factor Auth.'],
			   ['route' => 'dashboard.user.payment.subscription', 'label' => 'Plan'],
			   ['route' => 'dashboard.user.orders.index', 'label' => 'Orders'],
			   ['route' => 'dashboard.user.settings.index', 'label' => 'Settings'],
			]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

				<a class="flex w-full items-center px-3 py-2 hover:bg-foreground/5"
				   href="<?php echo e(route($item['route'])); ?>"
				>
					<?php echo e(__($item['label'])); ?>

				</a>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

			<form
				class="flex w-full"
				id="logout"
				method="POST"
				action="<?php echo e(route('logout')); ?>"
			>
				<?php echo csrf_field(); ?>
				<button
					class="flex w-full items-center px-3 py-2 hover:bg-foreground/10"
					type="submit"
				>
					<?php echo e(__('Logout')); ?>

				</button>
			</form>
		</div>

	 <?php $__env->endSlot(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0feec11b8470b4bf00c37924b86dc0af)): ?>
<?php $attributes = $__attributesOriginal0feec11b8470b4bf00c37924b86dc0af; ?>
<?php unset($__attributesOriginal0feec11b8470b4bf00c37924b86dc0af); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0feec11b8470b4bf00c37924b86dc0af)): ?>
<?php $component = $__componentOriginal0feec11b8470b4bf00c37924b86dc0af; ?>
<?php unset($__componentOriginal0feec11b8470b4bf00c37924b86dc0af); ?>
<?php endif; ?><?php /**PATH /home/u504467222/domains/omini.ai/public_html/resources/views/default/components/user-dropdown.blade.php ENDPATH**/ ?>