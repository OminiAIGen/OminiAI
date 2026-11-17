<?php
	$team = Auth::user()->getAttribute('team');
	$teamManager = Auth::user()->getAttribute('teamManager');

	$titlebar_links = [];
	$filters = [];
	if ($plansSubscriptionMonthly->count() > 0) {
		$titlebar_links[] = [
			'label' => 'Monthly',
			'link' => '#monthly',
		];
		$filters[] = 'Monthly';
	}
	if ($plansSubscriptionAnnual->count() > 0) {
		$titlebar_links[] = [
			'label' => 'Yearly',
			'link' => '#yearly',
		];
		$filters[] = 'Yearly';
	}
	if ($prepaidplans->count() > 0) {
		$titlebar_links[] = [
			'label' => 'Pre-Paid',
			'link' => '#pre-paid',
		];
		$filters[] = 'Pre-Paid';
	}
	if ($plansSubscriptionLifetime->count() > 0) {
		$titlebar_links[] = [
			'label' => 'Lifetime',
			'link' => '#lifetime',
		];
		$filters[] = 'Lifetime';
	}
?>


<?php $__env->startSection('title', __('Plans and Pricing')); ?>
<?php $__env->startSection('titlebar_actions', ''); ?>

<?php $paymentControls = app('App\Http\Controllers\Finance\PaymentProcessController'); ?>
<?php $gatewayControls = app('App\Http\Controllers\Finance\GatewayController'); ?>

<?php $__env->startSection('content'); ?>
	<div class="py-10">
		<div class="flex flex-col gap-14">
			<div class="w-full">
				<?php if (isset($component)) { $__componentOriginal740c66ff9bbfcb19a96a45ba2fa42d64 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal740c66ff9bbfcb19a96a45ba2fa42d64 = $attributes; } ?>
<?php $component = App\View\Components\Card::resolve(['size' => 'lg'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Card::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'lqd-plan-overview scroll-mt-11 bg-gradient-to-b from-accent/20 to-transparent pb-4 pt-2 dark:from-pink-300/10 dark:to-transparent max-md:text-center','id' => 'overview']); ?>
					<div class="mb-8 flex flex-wrap items-center justify-between gap-4">
						<h3 class="mb-0">
							<?php echo app('translator')->get('Here is your plan summary:'); ?>
						</h3>
						<div class="flex items-center gap-2">
							<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($getCurrentActiveSubscription = \App\Helpers\Classes\Helper::getCurrentActiveSubscription()): ?>
								<?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['variant' => 'link','href' => ''.e(route('dashboard.user.payment.cancelActiveSubscription')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'hover:text-red-500','onclick' => 'return confirm(\'Are you sure to cancel your plan? You will lose your remaining usage.\');']); ?>
									<?php echo e(__('Cancel My Plan')); ?>

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
							<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
							<?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['variant' => 'ghost-shadow','href' => ''.e(route('dashboard.user.payment.subscription')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
								<?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-plus'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-4']); ?>
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
								<?php echo e(__('Upgrade Your plan')); ?>

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
					</div>

					<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
						<?php if (isset($component)) { $__componentOriginal740c66ff9bbfcb19a96a45ba2fa42d64 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal740c66ff9bbfcb19a96a45ba2fa42d64 = $attributes; } ?>
<?php $component = App\View\Components\Card::resolve(['variant' => 'shadow','size' => 'sm'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Card::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'flex items-center text-start text-2xs hover:-translate-y-1 hover:shadow-lg hover:shadow-black/5']); ?>
							<div class="flex items-center justify-between gap-1.5">
								<p class="m-0">
                                    <span class="block font-semibold">
                                        <?php echo app('translator')->get('Active Plan'); ?>
                                    </span>
									<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->activePlan() != null): ?>
										<?php echo e(getSubscriptionName()); ?>

									<?php else: ?>
										<?php echo app('translator')->get('None'); ?>
									<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
								</p>
								
								<svg class="shrink-0" width="40" height="20" viewBox="0 0 40 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path
										d="M10.8346 9.99992L20.0013 19.1666L38.3346 0.833252M1.66797 9.99992L10.8346 19.1666M20.0013 9.99992L29.168 0.833252"
										stroke="url(#paint0_linear_210_8)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									<defs>
										<linearGradient id="paint0_linear_210_8" x1="1.66797" y1="4.57325" x2="14.9702" y2="28.0385"
														gradientUnits="userSpaceOnUse">
											<stop offset="0.139297" stop-color="#82E2F4"/>
											<stop offset="0.620738" stop-color="#8A8AED"/>
											<stop offset="1" stop-color="#6977DE"/>
										</linearGradient>
									</defs>
								</svg>
								
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

						<?php if (isset($component)) { $__componentOriginal740c66ff9bbfcb19a96a45ba2fa42d64 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal740c66ff9bbfcb19a96a45ba2fa42d64 = $attributes; } ?>
<?php $component = App\View\Components\Card::resolve(['variant' => 'shadow','size' => 'sm'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Card::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'flex items-center text-start text-2xs hover:-translate-y-1 hover:shadow-lg hover:shadow-black/5']); ?>
							<div class="flex items-center justify-between gap-1.5">
								<p class="m-0">
                                    <span class="block font-semibold">
                                        <?php echo app('translator')->get('Renewal Date'); ?>
                                    </span>
									<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->activePlan() != null): ?>
										<?php echo e(getSubscriptionDaysLeft()); ?> <?php echo app('translator')->get('Days'); ?>
									<?php else: ?>
										<?php echo app('translator')->get('None'); ?>
									<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
								</p>
								
								<svg width="44" height="44" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path
										d="M7.33203 12.8334C7.33203 11.861 7.71834 10.9283 8.40597 10.2407C9.09361 9.55306 10.0262 9.16675 10.9987 9.16675H32.9987C33.9712 9.16675 34.9038 9.55306 35.5914 10.2407C36.2791 10.9283 36.6654 11.861 36.6654 12.8334V34.8334C36.6654 35.8059 36.2791 36.7385 35.5914 37.4261C34.9038 38.1138 33.9712 38.5001 32.9987 38.5001H10.9987C10.0262 38.5001 9.09361 38.1138 8.40597 37.4261C7.71834 36.7385 7.33203 35.8059 7.33203 34.8334V12.8334Z"
										stroke="url(#paint0_linear_210_9)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M29.332 5.5V12.8333" stroke="url(#paint1_linear_210_9)" stroke-width="1.5" stroke-linecap="round"
										  stroke-linejoin="round"/>
									<path d="M14.668 5.5V12.8333" stroke="url(#paint2_linear_210_9)" stroke-width="1.5" stroke-linecap="round"
										  stroke-linejoin="round"/>
									<path d="M7.33203 20.1667H36.6654" stroke="url(#paint3_linear_210_9)" stroke-width="1.5" stroke-linecap="round"
										  stroke-linejoin="round"/>
									<path d="M12.832 25.6667H12.8559" stroke="url(#paint4_linear_210_9)" stroke-width="1.5" stroke-linecap="round"
										  stroke-linejoin="round"/>
									<path d="M18.3516 25.6667H18.3607" stroke="url(#paint5_linear_210_9)" stroke-width="1.5" stroke-linecap="round"
										  stroke-linejoin="round"/>
									<path d="M23.8516 25.6667H23.8607" stroke="url(#paint6_linear_210_9)" stroke-width="1.5" stroke-linecap="round"
										  stroke-linejoin="round"/>
									<path d="M29.3594 25.6667H29.3685" stroke="url(#paint7_linear_210_9)" stroke-width="1.5" stroke-linecap="round"
										  stroke-linejoin="round"/>
									<path d="M23.8594 31.1667H23.8685" stroke="url(#paint8_linear_210_9)" stroke-width="1.5" stroke-linecap="round"
										  stroke-linejoin="round"/>
									<path d="M12.8516 31.1667H12.8607" stroke="url(#paint9_linear_210_9)" stroke-width="1.5" stroke-linecap="round"
										  stroke-linejoin="round"/>
									<path d="M18.3516 31.1667H18.3607" stroke="url(#paint10_linear_210_9)" stroke-width="1.5" stroke-linecap="round"
										  stroke-linejoin="round"/>
									<defs>
										<linearGradient id="paint0_linear_210_9" x1="7.33203" y1="15.1507" x2="31.9427" y2="36.8574"
														gradientUnits="userSpaceOnUse">
											<stop stop-color="#82E2F4"/>
											<stop offset="0.502" stop-color="#8A8AED"/>
											<stop offset="1" stop-color="#6977DE"/>
										</linearGradient>
										<linearGradient id="paint1_linear_210_9" x1="29.332" y1="6.996" x2="30.8024" y2="7.17285"
														gradientUnits="userSpaceOnUse">
											<stop stop-color="#82E2F4"/>
											<stop offset="0.502" stop-color="#8A8AED"/>
											<stop offset="1" stop-color="#6977DE"/>
										</linearGradient>
										<linearGradient id="paint2_linear_210_9" x1="14.668" y1="6.996" x2="16.1384" y2="7.17285"
														gradientUnits="userSpaceOnUse">
											<stop stop-color="#82E2F4"/>
											<stop offset="0.502" stop-color="#8A8AED"/>
											<stop offset="1" stop-color="#6977DE"/>
										</linearGradient>
										<linearGradient id="paint3_linear_210_9" x1="7.33203" y1="20.3707" x2="7.3973" y2="22.0595"
														gradientUnits="userSpaceOnUse">
											<stop stop-color="#82E2F4"/>
											<stop offset="0.502" stop-color="#8A8AED"/>
											<stop offset="1" stop-color="#6977DE"/>
										</linearGradient>
										<linearGradient id="paint4_linear_210_9" x1="12.832" y1="25.8707" x2="12.8676" y2="25.8715"
														gradientUnits="userSpaceOnUse">
											<stop stop-color="#82E2F4"/>
											<stop offset="0.502" stop-color="#8A8AED"/>
											<stop offset="1" stop-color="#6977DE"/>
										</linearGradient>
										<linearGradient id="paint5_linear_210_9" x1="18.3516" y1="25.8707" x2="18.3652" y2="25.8709"
														gradientUnits="userSpaceOnUse">
											<stop stop-color="#82E2F4"/>
											<stop offset="0.502" stop-color="#8A8AED"/>
											<stop offset="1" stop-color="#6977DE"/>
										</linearGradient>
										<linearGradient id="paint6_linear_210_9" x1="23.8516" y1="25.8707" x2="23.8652" y2="25.8709"
														gradientUnits="userSpaceOnUse">
											<stop stop-color="#82E2F4"/>
											<stop offset="0.502" stop-color="#8A8AED"/>
											<stop offset="1" stop-color="#6977DE"/>
										</linearGradient>
										<linearGradient id="paint7_linear_210_9" x1="29.3594" y1="25.8707" x2="29.3731" y2="25.8709"
														gradientUnits="userSpaceOnUse">
											<stop stop-color="#82E2F4"/>
											<stop offset="0.502" stop-color="#8A8AED"/>
											<stop offset="1" stop-color="#6977DE"/>
										</linearGradient>
										<linearGradient id="paint8_linear_210_9" x1="23.8594" y1="31.3707" x2="23.873" y2="31.3709"
														gradientUnits="userSpaceOnUse">
											<stop stop-color="#82E2F4"/>
											<stop offset="0.502" stop-color="#8A8AED"/>
											<stop offset="1" stop-color="#6977DE"/>
										</linearGradient>
										<linearGradient id="paint9_linear_210_9" x1="12.8516" y1="31.3707" x2="12.8652" y2="31.3709"
														gradientUnits="userSpaceOnUse">
											<stop stop-color="#82E2F4"/>
											<stop offset="0.502" stop-color="#8A8AED"/>
											<stop offset="1" stop-color="#6977DE"/>
										</linearGradient>
										<linearGradient id="paint10_linear_210_9" x1="18.3516" y1="31.3707" x2="18.3652" y2="31.3709"
														gradientUnits="userSpaceOnUse">
											<stop stop-color="#82E2F4"/>
											<stop offset="0.502" stop-color="#8A8AED"/>
											<stop offset="1" stop-color="#6977DE"/>
										</linearGradient>
									</defs>
								</svg>
								
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

						<?php if (isset($component)) { $__componentOriginal740c66ff9bbfcb19a96a45ba2fa42d64 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal740c66ff9bbfcb19a96a45ba2fa42d64 = $attributes; } ?>
<?php $component = App\View\Components\Card::resolve(['variant' => 'shadow','size' => 'sm'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Card::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'flex items-center text-start text-2xs hover:-translate-y-1 hover:shadow-lg hover:shadow-black/5']); ?>
							<div class="flex items-center justify-between gap-1.5">
								<p class="m-0">
                                    <span class="block font-semibold">
                                        <?php echo app('translator')->get('Team Plan'); ?>
                                    </span>
									<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($team && $team?->allow_seats > 0): ?>
										<?php echo app('translator')->get('Active'); ?>
									<?php else: ?>
										<?php echo app('translator')->get('Not Active'); ?>
									<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
								</p>
								
								<svg width="44" height="44" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
									<g clip-path="url(#clip0_6425_3048)">
										<path
											d="M5.5 22C5.5 24.1668 5.92678 26.3124 6.75599 28.3143C7.58519 30.3161 8.80057 32.1351 10.3327 33.6673C11.8649 35.1994 13.6839 36.4148 15.6857 37.244C17.6876 38.0732 19.8332 38.5 22 38.5C24.1668 38.5 26.3124 38.0732 28.3143 37.244C30.3161 36.4148 32.1351 35.1994 33.6673 33.6673C35.1994 32.1351 36.4148 30.3161 37.244 28.3143C38.0732 26.3124 38.5 24.1668 38.5 22C38.5 19.8332 38.0732 17.6876 37.244 15.6857C36.4148 13.6839 35.1994 11.8649 33.6673 10.3327C32.1351 8.80057 30.3161 7.58519 28.3143 6.75599C26.3124 5.92679 24.1668 5.5 22 5.5C19.8332 5.5 17.6876 5.92679 15.6857 6.75599C13.6839 7.58519 11.8649 8.80057 10.3327 10.3327C8.80057 11.8649 7.58519 13.6839 6.75599 15.6857C5.92678 17.6876 5.5 19.8332 5.5 22Z"
											stroke="url(#paint0_linear_6425_3048)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										<path
											d="M16.5 18.3333C16.5 19.792 17.0795 21.191 18.1109 22.2224C19.1424 23.2539 20.5413 23.8333 22 23.8333C23.4587 23.8333 24.8576 23.2539 25.8891 22.2224C26.9205 21.191 27.5 19.792 27.5 18.3333C27.5 16.8746 26.9205 15.4757 25.8891 14.4442C24.8576 13.4128 23.4587 12.8333 22 12.8333C20.5413 12.8333 19.1424 13.4128 18.1109 14.4442C17.0795 15.4757 16.5 16.8746 16.5 18.3333Z"
											stroke="url(#paint1_linear_6425_3048)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										<path
											d="M11.3086 34.5565C11.7624 33.0462 12.6909 31.7225 13.9564 30.7816C15.2219 29.8407 16.757 29.3329 18.3339 29.3333H25.6673C27.2462 29.3328 28.7832 29.8419 30.0496 30.7849C31.3161 31.728 32.2443 33.0546 32.6963 34.5675"
											stroke="url(#paint2_linear_6425_3048)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									</g>
									<defs>
										<linearGradient id="paint0_linear_6425_3048" x1="5.5" y1="12.232" x2="33.187" y2="36.652"
														gradientUnits="userSpaceOnUse">
											<stop stop-color="#82E2F4"/>
											<stop offset="0.502" stop-color="#8A8AED"/>
											<stop offset="1" stop-color="#6977DE"/>
										</linearGradient>
										<linearGradient id="paint1_linear_6425_3048" x1="16.5" y1="15.0773" x2="25.729" y2="23.2173"
														gradientUnits="userSpaceOnUse">
											<stop stop-color="#82E2F4"/>
											<stop offset="0.502" stop-color="#8A8AED"/>
											<stop offset="1" stop-color="#6977DE"/>
										</linearGradient>
										<linearGradient id="paint2_linear_6425_3048" x1="11.3086" y1="30.4011" x2="13.5892" y2="38.6205"
														gradientUnits="userSpaceOnUse">
											<stop stop-color="#82E2F4"/>
											<stop offset="0.502" stop-color="#8A8AED"/>
											<stop offset="1" stop-color="#6977DE"/>
										</linearGradient>
										<clipPath id="clip0_6425_3048">
											<rect width="44" height="44" fill="white"/>
										</clipPath>
									</defs>
								</svg>
								
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

						<?php if (isset($component)) { $__componentOriginal740c66ff9bbfcb19a96a45ba2fa42d64 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal740c66ff9bbfcb19a96a45ba2fa42d64 = $attributes; } ?>
<?php $component = App\View\Components\Card::resolve(['variant' => 'shadow','size' => 'sm'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Card::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'flex items-center text-start text-2xs hover:-translate-y-1 hover:shadow-lg hover:shadow-black/5']); ?>
							<?php if (isset($component)) { $__componentOriginalb223a1c6a53e59e4d348d69e8bc0381b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb223a1c6a53e59e4d348d69e8bc0381b = $attributes; } ?>
<?php $component = App\View\Components\CreditList::resolve(['showLegend' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('credit-list'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\CreditList::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
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
<?php if (isset($__attributesOriginal740c66ff9bbfcb19a96a45ba2fa42d64)): ?>
<?php $attributes = $__attributesOriginal740c66ff9bbfcb19a96a45ba2fa42d64; ?>
<?php unset($__attributesOriginal740c66ff9bbfcb19a96a45ba2fa42d64); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal740c66ff9bbfcb19a96a45ba2fa42d64)): ?>
<?php $component = $__componentOriginal740c66ff9bbfcb19a96a45ba2fa42d64; ?>
<?php unset($__componentOriginal740c66ff9bbfcb19a96a45ba2fa42d64); ?>
<?php endif; ?>
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
			</div>

			<div class="w-full">
				<h2 class="mb-5">
					<?php echo e(__('Select a Plan')); ?>:
				</h2>
				<p class="mb-5 lg:w-1/3">
					<?php echo app('translator')->get('Please select a subscription plan or a token pack to upgrade your current plan.'); ?>
				</p>

				<div class="flex justify-center">
					<ul class="mb-8 inline-flex justify-between gap-3 rounded-full bg-foreground/10 p-1 text-xs font-medium">
						<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $filters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<li>
								<button
									class="<?php echo \Illuminate\Support\Arr::toCssClasses([
										'px-6 py-3 lg:min-w-40 leading-tight rounded-full transition-all hover:bg-background/80 [&.lqd-is-active]:bg-background [&.lqd-is-active]:shadow-[0_2px_12px_hsl(0_0%_0%/10%)]',
										'lqd-is-active' => $loop->first,
									]); ?>"
									x-data="{}"
									type="button"
									@click.prevent="$store.plansFilter.toggle('<?php echo e($filter); ?>')"
									:class="{ 'lqd-is-active': $store.plansFilter.isActive('<?php echo e($filter); ?>') }"
								>
									<?php echo app('translator')->get($filter); ?>

									<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filter === 'Yearly'): ?>
										<span
											class="-my-[0.275rem] ms-1 inline-block rounded-md bg-[#684AE2] bg-opacity-10 px-1 py-[0.275rem] text-[#684AE2]"><?php echo e($fSectSettings?->pricing_save_percent); ?></span>
									<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
								</button>
							</li>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
					</ul>
				</div>

				<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plansSubscriptionMonthly->count() > 0): ?>
					<div
						class="grid scroll-mt-28 grid-cols-4 gap-3 max-lg:grid-cols-2 max-md:grid-cols-1"
						id="monthly"
						x-data="{}"
						:class="{ grid: $store.plansFilter.isActive('Monthly'), hidden: !$store.plansFilter.isActive('Monthly') }"
					>
						<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $plansSubscriptionMonthly; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                'lqd-price-table w-full rounded-3xl border bg-background',
                                'shadow-[0_7px_20px_rgba(0,0,0,0.04)]' => $plan->is_featured,
                            ]); ?>">
								<div class="flex h-full flex-col p-7">
									<div class="mb-2 flex flex-wrap text-[50px] font-bold leading-none text-heading-foreground">
										<?php echo displayPlanPrice($plan, $currency); ?>

										<div class="ms-2 mt-2 inline-flex flex-col items-start gap-2 text-[0.3em]">
											<?php echo e(__(formatCamelCase($plan->frequency))); ?>

											<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan->is_featured == 1): ?>
												<div
													class="inline-flex rounded-full bg-gradient-to-r from-[#ece7f7] via-[#e7c5e6] to-[#e7ebf9] px-3 py-1 text-3xs text-black">
													<?php echo e(__('Popular plan')); ?>

												</div>
											<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
										</div>
									</div>

									<p class="text-sm font-medium leading-none opacity-50">
										<?php echo e(__($plan->name)); ?>

									</p>

									<?php if (isset($component)) { $__componentOriginal108762a00a61bbda2e149b49ad544dca = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal108762a00a61bbda2e149b49ad544dca = $attributes; } ?>
<?php $component = App\View\Components\PlanDetailsCard::resolve(['plan' => $plan,'period' => $plan->frequency] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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

									<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activesubid == $plan->id): ?>
										<div class="mt-7 text-center">
											<div class="flex flex-col gap-2">
                                                <span class="text-green-500">
                                                    <b><?php echo e(__('Already Subscribed')); ?></b>
                                                </span>
												<?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['size' => 'lg','variant' => 'danger','href' => ''.e(route('dashboard.user.payment.cancelActiveSubscription')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['onclick' => 'return confirm(\'Are you sure to cancel your plan? You will lose your remaining usage.\');']); ?>
													<?php echo e(__('Cancel Subscription')); ?>

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
										</div>
									<?php elseif($activesubid != null): ?>
										<div class="mt-7 text-center">
											<div class="flex flex-col gap-2">
                                                <span class="text-foreground/60">
                                                    <b><?php echo e(__('You have an active subscription.')); ?></b>
                                                </span>
											</div>
										</div>
									<?php else: ?>
										<div class="mt-7 text-center">
											<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($is_active_gateway == 1): ?>
												<?php ($planid = $plan->id); ?>
												<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan->price == 0): ?>
													<?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['href' => ''.e($app_is_demo ? '#' : route('dashboard.user.payment.startSubscriptionProcess', ['planId' => $planid, 'gatewayCode' => 'freeservice'])).'','variant' => 'ghost-shadow'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full','onclick' => ''.e($app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '').'']); ?>
														<?php echo e(__('Choose plan')); ?>

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
												<?php elseif($lastPrivateDate): ?>
													<?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['variant' => 'ghost-shadow'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full','onclick' => ''.e('return toastr.info(\'The expiration date for this plan has passed.\')').'']); ?>
														<?php echo e(__('Expired')); ?>

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
												<?php elseif($maxSubscribe): ?>
													<?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['variant' => 'ghost-shadow'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full','onclick' => ''.e('return toastr.info(\'This plan has reached its maximum capacity.\')').'']); ?>
														<?php echo e(__('Limit reached')); ?>

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
												<?php else: ?>
													<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($activeGateways) == 1 || setting('single_page_checkout', 0)): ?>
														<?php ($gateway = $activeGateways->first()); ?>
														<?php ($data = $gatewayControls->gatewayData($gateway->code)); ?>
														<?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['href' => ''.e($app_is_demo ? '#' : route('dashboard.user.payment.startSubscriptionProcess', ['planId' => $planid, 'gatewayCode' => $data['code']])).'','variant' => 'ghost-shadow'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full','onclick' => ''.e($app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '').'']); ?>
															<?php echo e(__('Choose plan')); ?>

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
													<?php else: ?>
														<?php if (isset($component)) { $__componentOriginale6a555649da86b3de44465cdfe004aa4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale6a555649da86b3de44465cdfe004aa4 = $attributes; } ?>
<?php $component = App\View\Components\Modal::resolve(['title' => ''.e(__('Continue with')).'','disableModal' => ''.e($app_is_demo).'','disableModalMessage' => ''.e(__('This feature is disabled in Demo version.')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Modal::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
															 <?php $__env->slot('trigger', null, ['class' => 'w-full','variant' => 'ghost-shadow']); ?> 
																<?php echo e(__('Choose plan')); ?>

															 <?php $__env->endSlot(); ?>
															 <?php $__env->slot('modal', null, []); ?> 
																<div class="flex flex-col gap-4">
																	<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $activeGateways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gateway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																		<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($gateway->code == 'revenuecat'): ?>
																			<?php continue; ?>
																		<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
																		<?php ($planid = $plan->id); ?>
																		<?php ($data = $gatewayControls->gatewayData($gateway->code)); ?>
																		<?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['hoverVariant' => 'secondary','href' => ''.e($app_is_demo ? '#' : route('dashboard.user.payment.startSubscriptionProcess', ['planId' => $planid, 'gatewayCode' => $data['code']])).'','variant' => 'ghost-shadow'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full','onclick' => ''.e($app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '').'']); ?>
																			<div
																				class="m-0 flex h-9 w-full items-center justify-between align-middle">
																				<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($data['whiteLogo'] == 1): ?>
																					<img
																						class="rounded-3xl bg-primary px-3"
																						src="<?php echo e(custom_theme_url($data['img'])); ?>"
																						style="max-height:24px;"
																						alt="<?php echo e($data['title']); ?>"
																					/>
																				<?php else: ?>
																					<img
																						class="rounded-3xl px-3"
																						src="<?php echo e(custom_theme_url($data['img'])); ?>"
																						style="max-height:24px;"
																						alt="<?php echo e($data['title']); ?>"
																					/>
																				<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
																				<?php echo e($data['title']); ?>

																			</div>
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
																	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
																</div>
															 <?php $__env->endSlot(); ?>
														 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale6a555649da86b3de44465cdfe004aa4)): ?>
<?php $attributes = $__attributesOriginale6a555649da86b3de44465cdfe004aa4; ?>
<?php unset($__attributesOriginale6a555649da86b3de44465cdfe004aa4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale6a555649da86b3de44465cdfe004aa4)): ?>
<?php $component = $__componentOriginale6a555649da86b3de44465cdfe004aa4; ?>
<?php unset($__componentOriginale6a555649da86b3de44465cdfe004aa4); ?>
<?php endif; ?>
													<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
												<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
											<?php else: ?>
												<p><?php echo e(__('Please enable a payment gateway')); ?></p>
											<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
										</div>
									<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

								</div>
							</div>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
					</div>
				<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

				<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prepaidplans->count() > 0): ?>
					<div
						class="hidden scroll-mt-28 grid-cols-4 gap-3 max-lg:grid-cols-2 max-md:grid-cols-1"
						id="pre-paid"
						x-data="{}"
						:class="{ grid: $store.plansFilter.isActive('Pre-Paid'), hidden: !$store.plansFilter.isActive('Pre-Paid') }"
					>
						<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $prepaidplans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                'lqd-price-table w-full rounded-3xl border bg-background',
                                'shadow-[0_7px_20px_rgba(0,0,0,0.04)]' => $plan->is_featured,
                            ]); ?>">
								<div class="flex h-full flex-col p-7">
									<div class="mb-2 flex flex-wrap text-[50px] font-bold leading-none text-heading-foreground">
										<?php echo displayPlanPrice($plan, $currency); ?>

										<div class="ms-2 mt-2 inline-flex flex-col items-start gap-2 text-[0.3em]">
											<?php echo e(__('One time')); ?>

											<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan->is_featured == 1): ?>
												<div
													class="inline-flex rounded-full bg-gradient-to-r from-[#ece7f7] via-[#e7c5e6] to-[#e7ebf9] px-[0.75rem] py-[0.25rem] text-3xs text-black">
													<?php echo e(__('Popular pack')); ?>

												</div>
											<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
										</div>
									</div>
									<p class="text-sm font-medium leading-none opacity-60">
										<?php echo e(__($plan->name)); ?>

									</p>
									<?php if (isset($component)) { $__componentOriginal108762a00a61bbda2e149b49ad544dca = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal108762a00a61bbda2e149b49ad544dca = $attributes; } ?>
<?php $component = App\View\Components\PlanDetailsCard::resolve(['plan' => $plan,'period' => $plan->frequency] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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
									<div class="mt-7 text-center">
										<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($is_active_gateway == 1): ?>
											<?php ($planid = $plan->id); ?>
											<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan->price == 0): ?>
												<?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['href' => ''.e($app_is_demo ? '#' : route('dashboard.user.payment.startPrepaidPaymentProcess', ['planId' => $planid, 'gatewayCode' => 'freeservice'])).'','variant' => 'ghost-shadow'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full','onclick' => ''.e($app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '').'']); ?>
													<?php echo e(__('Choose pack')); ?>

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
											<?php elseif($lastPrivateDate): ?>
												<?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['variant' => 'ghost-shadow'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full','onclick' => ''.e('return toastr.info(\'The expiration date for this plan has passed.\')').'']); ?>
													<?php echo e(__('Expired')); ?>

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
											<?php elseif($maxSubscribe): ?>
												<?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['variant' => 'ghost-shadow'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full','onclick' => ''.e('return toastr.info(\'This plan has reached its maximum capacity.\')').'']); ?>
													<?php echo e(__('Limit reached')); ?>

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
											<?php else: ?>
												<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($activeGateways) == 1 || setting('single_page_checkout', 0)): ?>
													<?php ($gateway = $activeGateways->first()); ?>
													<?php ($data = $gatewayControls->gatewayData($gateway->code)); ?>
													<?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['href' => ''.e($app_is_demo ? '#' : route('dashboard.user.payment.startPrepaidPaymentProcess', ['planId' => $planid, 'gatewayCode' => $data['code']])).'','variant' => 'ghost-shadow'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full','onclick' => ''.e($app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '').'']); ?>
														<?php echo e(__('Choose pack')); ?>

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
												<?php else: ?>
													<?php if (isset($component)) { $__componentOriginale6a555649da86b3de44465cdfe004aa4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale6a555649da86b3de44465cdfe004aa4 = $attributes; } ?>
<?php $component = App\View\Components\Modal::resolve(['title' => ''.e(__('Continue with')).'','disableModal' => ''.e($app_is_demo).'','disableModalMessage' => ''.e(__('This feature is disabled in Demo version.')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Modal::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
														 <?php $__env->slot('trigger', null, ['class' => 'w-full','variant' => 'ghost-shadow']); ?> 
															<?php echo e(__('Choose pack')); ?>

														 <?php $__env->endSlot(); ?>
														 <?php $__env->slot('modal', null, []); ?> 
															<div class="flex flex-col gap-4">
																<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $activeGateways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gateway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																	<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($gateway->code == 'revenuecat'): ?>
																		<?php continue; ?>
																	<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
																	<?php ($data = $gatewayControls->gatewayData($gateway->code)); ?>
																	<?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['hoverVariant' => 'secondary','href' => ''.e($app_is_demo ? '#' : route('dashboard.user.payment.startPrepaidPaymentProcess', ['planId' => $planid, 'gatewayCode' => $data['code']])).'','variant' => 'ghost-shadow'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full','onclick' => ''.e($app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '').'']); ?>
																		<div class="flex h-9 w-full items-center justify-between align-middle">
																			<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($data['whiteLogo'] == 1): ?>
																				<img
																					class="rounded-3xl bg-primary px-3"
																					src="<?php echo e(custom_theme_url($data['img'])); ?>"
																					style="max-height:24px;"
																					alt="<?php echo e($data['title']); ?>"
																				/>
																			<?php else: ?>
																				<img
																					class="rounded-3xl px-3"
																					src="<?php echo e(custom_theme_url($data['img'])); ?>"
																					style="max-height:24px;"
																					alt="<?php echo e($data['title']); ?>"
																				/>
																			<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
																			<?php echo e($data['title']); ?>

																		</div>
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
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
															</div>
														 <?php $__env->endSlot(); ?>
													 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale6a555649da86b3de44465cdfe004aa4)): ?>
<?php $attributes = $__attributesOriginale6a555649da86b3de44465cdfe004aa4; ?>
<?php unset($__attributesOriginale6a555649da86b3de44465cdfe004aa4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale6a555649da86b3de44465cdfe004aa4)): ?>
<?php $component = $__componentOriginale6a555649da86b3de44465cdfe004aa4; ?>
<?php unset($__componentOriginale6a555649da86b3de44465cdfe004aa4); ?>
<?php endif; ?>
												<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
											<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
										<?php else: ?>
											<p>
												<?php echo e(__('Please enable a payment gateway')); ?>

											</p>
										<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
									</div>
								</div>
							</div>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
					</div>
				<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

				<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plansSubscriptionLifetime->count() > 0): ?>
					<div
						class="grid scroll-mt-28 grid-cols-4 gap-3 max-lg:grid-cols-2 max-md:grid-cols-1"
						id="lifetime"
						x-data="{}"
						:class="{ grid: $store.plansFilter.isActive('Lifetime'), hidden: !$store.plansFilter.isActive('Lifetime') }"
					>
						<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $plansSubscriptionLifetime; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                'lqd-price-table w-full rounded-3xl border bg-background',
                                'shadow-[0_7px_20px_rgba(0,0,0,0.04)]' => $plan->is_featured,
                            ]); ?>">
								<div class="flex h-full flex-col p-7">
									<div class="mb-2 flex flex-wrap text-[50px] font-bold leading-none text-heading-foreground">
										<?php echo displayPlanPrice($plan, $currency); ?>

										<div class="ms-2 mt-2 inline-flex flex-col items-start gap-2 text-[0.3em]">
											<?php echo e(__(formatCamelCase($plan->frequency))); ?>

											<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan->is_featured == 1): ?>
												<div
													class="inline-flex rounded-full bg-gradient-to-r from-[#ece7f7] via-[#e7c5e6] to-[#e7ebf9] px-3 py-1 text-3xs text-black">
													<?php echo e(__('Popular plan')); ?>

												</div>
											<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
										</div>
									</div>

									<p class="text-sm font-medium leading-none opacity-50">
										<?php echo e(__($plan->name)); ?>

									</p>

									<?php if (isset($component)) { $__componentOriginal108762a00a61bbda2e149b49ad544dca = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal108762a00a61bbda2e149b49ad544dca = $attributes; } ?>
<?php $component = App\View\Components\PlanDetailsCard::resolve(['plan' => $plan,'period' => $plan->frequency] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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

									<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activesubid == $plan->id): ?>
										<div class="mt-7 text-center">
											<div class="flex flex-col gap-2">
                                                <span class="text-green-500">
                                                    <b><?php echo e(__('Already Subscribed')); ?></b>
                                                </span>
												<?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['size' => 'lg','variant' => 'danger','href' => ''.e(route('dashboard.user.payment.cancelActiveSubscription')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['onclick' => 'return confirm(\'Are you sure to cancel your plan? You will lose your remaining usage.\');']); ?>
													<?php echo e(__('Cancel Subscription')); ?>

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
										</div>
									<?php elseif($activesubid != null): ?>
										<div class="mt-7 text-center">
											<div class="flex flex-col gap-2">
                                                <span class="text-foreground/60">
                                                    <b><?php echo e(__('You have an active subscription.')); ?></b>
                                                </span>
											</div>
										</div>
									<?php else: ?>
										<div class="mt-7 text-center">
											<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($is_active_gateway == 1): ?>
												<?php ($planid = $plan->id); ?>
												<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan->price == 0): ?>
													<?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['href' => ''.e($app_is_demo ? '#' : route('dashboard.user.payment.startSubscriptionProcess', ['planId' => $planid, 'gatewayCode' => 'freeservice'])).'','variant' => 'ghost-shadow'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full','onclick' => ''.e($app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '').'']); ?>
														<?php echo e(__('Choose plan')); ?>

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
												<?php elseif($lastPrivateDate): ?>
													<?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['variant' => 'ghost-shadow'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full','onclick' => ''.e('return toastr.info(\'The expiration date for this plan has passed.\')').'']); ?>
														<?php echo e(__('Expired')); ?>

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
												<?php elseif($maxSubscribe): ?>
													<?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['variant' => 'ghost-shadow'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full','onclick' => ''.e('return toastr.info(\'This plan has reached its maximum capacity.\')').'']); ?>
														<?php echo e(__('Limit reached')); ?>

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
												<?php else: ?>
													<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($activeGateways) == 1 || setting('single_page_checkout', 0)): ?>
														<?php ($gateway = $activeGateways->first()); ?>
														<?php ($data = $gatewayControls->gatewayData($gateway->code)); ?>
														<?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['href' => ''.e($app_is_demo ? '#' : route('dashboard.user.payment.startSubscriptionProcess', ['planId' => $planid, 'gatewayCode' => $data['code']])).'','variant' => 'ghost-shadow'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full','onclick' => ''.e($app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '').'']); ?>
															<?php echo e(__('Choose plan')); ?>

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
													<?php else: ?>
														<?php if (isset($component)) { $__componentOriginale6a555649da86b3de44465cdfe004aa4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale6a555649da86b3de44465cdfe004aa4 = $attributes; } ?>
<?php $component = App\View\Components\Modal::resolve(['title' => ''.e(__('Continue with')).'','disableModal' => ''.e($app_is_demo).'','disableModalMessage' => ''.e(__('This feature is disabled in Demo version.')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Modal::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
															 <?php $__env->slot('trigger', null, ['class' => 'w-full','variant' => 'ghost-shadow']); ?> 
																<?php echo e(__('Choose plan')); ?>

															 <?php $__env->endSlot(); ?>
															 <?php $__env->slot('modal', null, []); ?> 
																<div class="flex flex-col gap-4">
																	<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $activeGateways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gateway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																		<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($gateway->code == 'revenuecat'): ?>
																			<?php continue; ?>
																		<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
																		<?php ($data = $gatewayControls->gatewayData($gateway->code)); ?>
																		<?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['hoverVariant' => 'secondary','href' => ''.e($app_is_demo ? '#' : route('dashboard.user.payment.startSubscriptionProcess', ['planId' => $planid, 'gatewayCode' => $data['code']])).'','variant' => 'ghost-shadow'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full','onclick' => ''.e($app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '').'']); ?>
																			<div
																				class="m-0 flex h-9 w-full items-center justify-between align-middle">
																				<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($data['whiteLogo'] == 1): ?>
																					<img
																						class="rounded-3xl bg-primary px-3"
																						src="<?php echo e(custom_theme_url($data['img'])); ?>"
																						style="max-height:24px;"
																						alt="<?php echo e($data['title']); ?>"
																					/>
																				<?php else: ?>
																					<img
																						class="rounded-3xl px-3"
																						src="<?php echo e(custom_theme_url($data['img'])); ?>"
																						style="max-height:24px;"
																						alt="<?php echo e($data['title']); ?>"
																					/>
																				<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
																				<?php echo e($data['title']); ?>

																			</div>
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
																	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
																</div>
															 <?php $__env->endSlot(); ?>
														 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale6a555649da86b3de44465cdfe004aa4)): ?>
<?php $attributes = $__attributesOriginale6a555649da86b3de44465cdfe004aa4; ?>
<?php unset($__attributesOriginale6a555649da86b3de44465cdfe004aa4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale6a555649da86b3de44465cdfe004aa4)): ?>
<?php $component = $__componentOriginale6a555649da86b3de44465cdfe004aa4; ?>
<?php unset($__componentOriginale6a555649da86b3de44465cdfe004aa4); ?>
<?php endif; ?>
													<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
												<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
											<?php else: ?>
												<p><?php echo e(__('Please enable a payment gateway')); ?></p>
											<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
										</div>
									<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
								</div>
							</div>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
					</div>
				<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

				<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plansSubscriptionAnnual->count() > 0): ?>
					<div
						class="grid scroll-mt-28 grid-cols-4 gap-3 max-lg:grid-cols-2 max-md:grid-cols-1"
						id="yearly"
						x-data="{}"
						:class="{ grid: $store.plansFilter.isActive('Yearly'), hidden: !$store.plansFilter.isActive('Yearly') }"
					>
						<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $plansSubscriptionAnnual; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                'lqd-price-table w-full rounded-3xl border bg-background',
                                'shadow-[0_7px_20px_rgba(0,0,0,0.04)]' => $plan->is_featured,
                            ]); ?>">
								<div class="flex h-full flex-col p-7">
									<div class="mb-2 flex flex-wrap text-[50px] font-bold leading-none text-heading-foreground">
										<?php echo displayPlanPrice($plan, $currency); ?>

										<div class="ms-2 mt-2 inline-flex flex-col items-start gap-2 text-[0.3em]">
											<?php echo e(__(formatCamelCase($plan->frequency))); ?>

											<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan->is_featured == 1): ?>
												<div
													class="inline-flex rounded-full bg-gradient-to-r from-[#ece7f7] via-[#e7c5e6] to-[#e7ebf9] px-3 py-1 text-3xs text-black">
													<?php echo e(__('Popular plan')); ?>

												</div>
											<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
										</div>
									</div>

									<p class="text-sm font-medium leading-none opacity-50">
										<?php echo e(__($plan->name)); ?>

									</p>

									<?php if (isset($component)) { $__componentOriginal108762a00a61bbda2e149b49ad544dca = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal108762a00a61bbda2e149b49ad544dca = $attributes; } ?>
<?php $component = App\View\Components\PlanDetailsCard::resolve(['plan' => $plan,'period' => $plan->frequency] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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

									<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activesubid == $plan->id): ?>
										<div class="mt-7 text-center">
											<div class="flex flex-col gap-2">
                                                <span class="text-green-500">
                                                    <b><?php echo e(__('Already Subscribed')); ?></b>
                                                </span>
												<?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['size' => 'lg','variant' => 'danger','href' => ''.e(route('dashboard.user.payment.cancelActiveSubscription')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['onclick' => 'return confirm(\'Are you sure to cancel your plan? You will lose your remaining usage.\');']); ?>
													<?php echo e(__('Cancel Subscription')); ?>

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
										</div>
									<?php elseif($activesubid != null): ?>
										<div class="mt-7 text-center">
											<div class="flex flex-col gap-2">
                                                <span class="text-foreground/60">
                                                    <b><?php echo e(__('You have an active subscription.')); ?></b>
                                                </span>
											</div>
										</div>
									<?php else: ?>
										<div class="mt-7 text-center">
											<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($is_active_gateway == 1): ?>
												<?php ($planid = $plan->id); ?>
												<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan->price == 0): ?>
													<?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['href' => ''.e($app_is_demo ? '#' : route('dashboard.user.payment.startSubscriptionProcess', ['planId' => $planid, 'gatewayCode' => 'freeservice'])).'','variant' => 'ghost-shadow'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full','onclick' => ''.e($app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '').'']); ?>
														<?php echo e(__('Choose plan')); ?>

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
												<?php elseif($lastPrivateDate): ?>
													<?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['variant' => 'ghost-shadow'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full','onclick' => ''.e('return toastr.info(\'The expiration date for this plan has passed.\')').'']); ?>
														<?php echo e(__('Expired')); ?>

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
												<?php elseif($maxSubscribe): ?>
													<?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['variant' => 'ghost-shadow'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full','onclick' => ''.e('return toastr.info(\'This plan has reached its maximum capacity.\')').'']); ?>
														<?php echo e(__('Limit reached')); ?>

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
												<?php else: ?>
													<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($activeGateways) == 1 || setting('single_page_checkout', 0)): ?>
														<?php ($gateway = $activeGateways->first()); ?>
														<?php ($data = $gatewayControls->gatewayData($gateway->code)); ?>
														<?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['href' => ''.e($app_is_demo ? '#' : route('dashboard.user.payment.startSubscriptionProcess', ['planId' => $planid, 'gatewayCode' => $data['code'] ?: 'stripe'])).'','variant' => 'ghost-shadow'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full','onclick' => ''.e($app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '').'']); ?>
															<?php echo e(__('Choose plan')); ?>

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
													<?php else: ?>
														<?php if (isset($component)) { $__componentOriginale6a555649da86b3de44465cdfe004aa4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale6a555649da86b3de44465cdfe004aa4 = $attributes; } ?>
<?php $component = App\View\Components\Modal::resolve(['title' => ''.e(__('Continue with')).'','disableModal' => ''.e($app_is_demo).'','disableModalMessage' => ''.e(__('This feature is disabled in Demo version.')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Modal::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
															 <?php $__env->slot('trigger', null, ['class' => 'w-full','variant' => 'ghost-shadow']); ?> 
																<?php echo e(__('Choose plan')); ?>

															 <?php $__env->endSlot(); ?>
															 <?php $__env->slot('modal', null, []); ?> 
																<div class="flex flex-col gap-4">
																	<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $activeGateways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gateway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																		<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($gateway->code == 'revenuecat'): ?>
																			<?php continue; ?>
																		<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
																		<?php ($data = $gatewayControls->gatewayData($gateway->code)); ?>
																		<?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['hoverVariant' => 'secondary','href' => ''.e($app_is_demo ? '#' : route('dashboard.user.payment.startSubscriptionProcess', ['planId' => $planid, 'gatewayCode' => $data['code']])).'','variant' => 'ghost-shadow'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full','onclick' => ''.e($app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '').'']); ?>
																			<div
																				class="m-0 flex h-9 w-full items-center justify-between align-middle">
																				<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($data['whiteLogo'] == 1): ?>
																					<img
																						class="rounded-3xl bg-primary px-3"
																						src="<?php echo e(custom_theme_url($data['img'])); ?>"
																						style="max-height:24px;"
																						alt="<?php echo e($data['title']); ?>"
																					/>
																				<?php else: ?>
																					<img
																						class="rounded-3xl px-3"
																						src="<?php echo e(custom_theme_url($data['img'])); ?>"
																						style="max-height:24px;"
																						alt="<?php echo e($data['title']); ?>"
																					/>
																				<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
																				<?php echo e($data['title']); ?>

																			</div>
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
																	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
																</div>
															 <?php $__env->endSlot(); ?>
														 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale6a555649da86b3de44465cdfe004aa4)): ?>
<?php $attributes = $__attributesOriginale6a555649da86b3de44465cdfe004aa4; ?>
<?php unset($__attributesOriginale6a555649da86b3de44465cdfe004aa4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale6a555649da86b3de44465cdfe004aa4)): ?>
<?php $component = $__componentOriginale6a555649da86b3de44465cdfe004aa4; ?>
<?php unset($__componentOriginale6a555649da86b3de44465cdfe004aa4); ?>
<?php endif; ?>
													<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
												<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
											<?php else: ?>
												<p><?php echo e(__('Please enable a payment gateway')); ?></p>
											<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
										</div>
									<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
								</div>
							</div>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
					</div>
				<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
			</div>

		</div>
	</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
	<script>
		$(document).ready(function () {
			let plan = '<?php echo e(request('plan')); ?>';

			if (plan) {
				$('#gatewayModal_' + plan).modal('show');
			}
		});

		document.addEventListener('alpine:init', () => {
			Alpine.store('plansFilter', {
				active: 'monthly',
				toggle(filter) {
					const f = filter.replace('#', '').toLowerCase();
					if (this.active === f) return;
					this.active = f;
					setTimeout(() => {
						const el = document.getElementById(this.active);
						if (el) {
							el.scrollIntoView({
								behavior: 'smooth'
							});
						}
					}, 0);
				},
				isActive(filter) {
					const f = filter.replace('#', '').toLowerCase();
					return this.active === f;
				},
				tabsClicked() {
					return this.active !== 'monthly';
				}
			});
		});
	</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('panel.layout.app', ['disable_tblr' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u504467222/domains/omini.ai/public_html/resources/views/default/panel/user/finance/subscriptionPlans.blade.php ENDPATH**/ ?>