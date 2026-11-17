<footer class="site-footer relative border-t pb-16 pt-20">
    <div class="container">
        <div class="grid grid-cols-2 gap-8 lg:grid-cols-4">
            <div class="pt-0.5 max-sm:col-span-2 max-sm:mb-8">
                <ul class="flex flex-wrap gap-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Models\SocialMediaAccounts::where('is_active', true)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <a
                                class="inline-flex items-center gap-2"
                                href="<?php echo e($social['link']); ?>"
                            >
                                <span class="w-6 [&_path]:fill-current [&_svg]:h-auto [&_svg]:w-full">
                                    <?php echo $social['icon']; ?>

                                </span>
                            </a>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>
            </div>

            <div>
                <p class="mb-7 text-xs font-semibold -tracking-wide">
                    <?php echo e(__('Integrations')); ?>

                </p>

                <ul class="flex flex-col gap-3 text-base font-normal">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Models\SocialMediaAccounts::where('is_active', true)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <a
                                class="inline-flex items-center gap-2 underline underline-offset-[3px]"
                                href="<?php echo e($social['link']); ?>"
                            >
                                <?php echo $social['title']; ?>

                            </a>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>
            </div>

			<div>
				<p class="mb-7 text-xs font-semibold -tracking-wide">
					<?php echo e(__('Use Cases')); ?>

				</p>

				<?php
					$defaultUseCaseLinks = [
						['link' => '#', 'title' => 'Collect Feedback'],
						['link' => '#', 'title' => 'Product Launches'],
						['link' => '#', 'title' => 'Promotions'],
						['link' => '#', 'title' => 'Limited Offers'],
						['link' => '#', 'title' => 'Reminders'],
						['link' => '#', 'title' => 'Events'],
						['link' => '#', 'title' => 'Bulk Messages'],
					];

					$useCaseLinks = json_decode(setting('footer_use_cases_links', ''), true);
					if (!is_array($useCaseLinks) || empty($useCaseLinks)) {
						$useCaseLinks = $defaultUseCaseLinks;
					}
				?>

				<ul class="flex flex-col gap-3 text-base font-normal">
					<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $useCaseLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<li>
							<a
								class="inline-flex items-center gap-2 underline underline-offset-[3px]"
								href="<?php echo e($link['link'] ?? '#'); ?>"
							>
								<?php echo e(__($link['title'] ?? '')); ?>

							</a>
						</li>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
				</ul>
			</div>

			<div>
				<p class="mb-7 text-xs font-semibold -tracking-wide">
					<?php echo e(__('Resources')); ?>

				</p>

				<?php
					$defaultResourceLinks = [
						['link' => '#', 'title' => 'Terms of Services'],
						['link' => '#', 'title' => 'Support'],
						['link' => '#', 'title' => 'Help Center'],
						['link' => '#', 'title' => 'Support'],
						['link' => '#', 'title' => 'Privacy Policy'],
						['link' => '#', 'title' => 'Blog'],
						['link' => '#', 'title' => 'Status'],
					];

					$resourceLinks = json_decode(setting('footer_resources_links', ''), true);
					if (!is_array($resourceLinks) || empty($resourceLinks)) {
						$resourceLinks = $defaultResourceLinks;
					}
				?>

				<ul class="flex flex-col gap-3 text-base font-normal">
					<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $resourceLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<li>
							<a
								class="inline-flex items-center gap-2 underline underline-offset-[3px]"
								href="<?php echo e($link['link'] ?? '#'); ?>"
							>
								<?php echo e(__($link['title'] ?? '')); ?>

							</a>
						</li>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
				</ul>
			</div>
		</div>

        <div class="mt-16 text-center">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count(explode(',', $settings_two->languages)) > 1): ?>
                <div class="group relative mx-auto mb-8 md:inline-flex">
                    <button
                        class="relative inline-flex items-center gap-3 underline underline-offset-[3px] transition-all before:absolute before:-top-5 before:bottom-full before:w-full group-hover:text-foreground"
                    >
                        <span
                            class="inline-flex size-10 items-center justify-center rounded-full border border-foreground/5 text-foreground/30 transition-all group-hover:text-foreground"
                        >
                            
							<svg class="relative z-1" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" > <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path> <path d="M3.6 9h16.8"></path> <path d="M3.6 15h16.8"></path> <path d="M11.5 3a17 17 0 0 0 0 18"></path> <path d="M12.5 3a17 17 0 0 1 0 18"></path> </svg>
							
                        </span>
                        <?php echo e(__('Languages')); ?>


                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-chevron-down'); ?>
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
                    </button>
                    <div
                        class="pointer-events-none absolute bottom-[calc(100%+1rem)] left-1/2 min-w-[145px] -translate-x-1/2 translate-y-2 rounded-md bg-white text-black opacity-0 shadow-lg transition-all group-hover:pointer-events-auto group-hover:translate-y-0 group-hover:opacity-100">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = LaravelLocalization::getSupportedLocales(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $localeCode => $properties): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($localeCode, explode(',', $settings_two->languages))): ?>
                                <a
                                    class="block border-b border-black border-opacity-5 px-3 py-3 transition-colors last:border-none hover:bg-black hover:bg-opacity-5"
                                    href="<?php echo e(route('language.change', $localeCode)); ?>"
                                    rel="alternate"
                                    hreflang="<?php echo e($localeCode); ?>"
                                ><?php echo e(country2flag(substr($properties['regional'], strrpos($properties['regional'], '_') + 1))); ?>

                                    <?php echo e($properties['native']); ?></a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <p class="mx-auto w-full text-center text-[12px] opacity-80 lg:w-9/12">
                <?php echo e(date('Y') . ' ' . $setting->site_name . '. ' . __($fSetting->footer_copyright)); ?>

            </p>
        </div>
    </div>
</footer>
<?php /**PATH D:\omini_customization\OminiAI\resources\views/marketing-bot/layout/footer.blade.php ENDPATH**/ ?>