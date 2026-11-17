<?php
	$userId = auth()->id();

	$docs = cache()->get("user:{$userId}:user_docs");
	$docsCount = count($docs);
	$hoursSaved = $docsCount * 3;

	$textDocsCount = $docs->filter(fn($entity) => $entity->generator?->type == 'text')?->count();
	$imageDocsCount = $docs->filter(fn($entity) => $entity->generator?->type == 'image')?->count();
	$audioDocsCount = $docs->filter(fn($entity) => $entity->generator?->type == 'audio')?->count();
	$otherDocsCount = $docsCount - $textDocsCount - $imageDocsCount - $audioDocsCount;

	$sum = $textDocsCount + $imageDocsCount + $audioDocsCount;

	$chatbots = cache()->get("user:{$userId}:user_chatbots");
	$chatbotCount = count($chatbots);
?>

<?php if (isset($component)) { $__componentOriginal740c66ff9bbfcb19a96a45ba2fa42d64 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal740c66ff9bbfcb19a96a45ba2fa42d64 = $attributes; } ?>
<?php $component = App\View\Components\Card::resolve(['size' => 'lg'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Card::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full','id' => 'summary']); ?>
    <div class="flex justify-between max-lg:flex-wrap lg:mb-7">
        <h3 class="items-center text-[17px] leading-6 lg:mb-0">
            <?php echo app('translator')->get('Account Summary'); ?>
        </h3>
        <div class="flex w-full justify-between max-lg:flex-wrap lg:w-3/5">
            <div class="relative flex grow flex-col justify-center lg:ps-12 lg:after:absolute lg:after:right-0 lg:after:h-[80%] lg:after:w-px lg:after:bg-border">
                <p class="text-nowrap text-sm leading-5"><?php echo app('translator')->get('Hours Saved'); ?></p>
                <h2><?php echo e($hoursSaved); ?></h2>
            </div>
            <div class="relative flex grow flex-col justify-center lg:ps-12 lg:after:absolute lg:after:right-0 lg:after:h-[80%] lg:after:w-px lg:after:bg-border">
                <p class="text-nowrap text-sm leading-5"><?php echo app('translator')->get('Documents'); ?></p>
                <h2> <?php echo e($docsCount); ?> </h2>
            </div>
            <div class="flex grow flex-col justify-center lg:ps-12">
                <p class="text-nowrap text-sm leading-5"><?php echo app('translator')->get('Chatbots'); ?></p>
                <h2><?php echo e($chatbotCount); ?></h2>
            </div>
        </div>
    </div>

    <hr>

    <div class="flex flex-col gap-4 sm:py-6">
        <h4 class="text-foreground/80"><?php echo app('translator')->get('Document Overview'); ?></h4>
        <div class="flex flex-nowrap items-center gap-10 max-sm:flex-wrap max-sm:gap-4">
            <div class="flex h-[10px] w-full flex-nowrap gap-0.5 overflow-hidden rounded-lg">
                <span
                    class="<?php echo e($textDocsCount == 0 ? 'hidden' : ''); ?> bg-accent"
                    style="width: <?php echo e($sum == 0 ? '100' : ($textDocsCount / $sum) * 100); ?>%"
                ></span>
                <span
                    class="<?php echo e($imageDocsCount == 0 ? 'hidden' : ''); ?> bg-[#1CA685]"
                    style="width: <?php echo e($sum == 0 ? '100' : ($imageDocsCount / $sum) * 100); ?>%"
                ></span>
                <span
                    class="<?php echo e($audioDocsCount == 0 ? 'hidden' : ''); ?> bg-[#667085]"
                    style="width: <?php echo e($sum == 0 ? '100' : ($audioDocsCount / $sum) * 100); ?>%"
                ></span>
            </div>
            <?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['variant' => 'link','href' => ''.e(route('dashboard.user.openai.documents.all')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                <span class="text-nowrap font-bold text-foreground"><?php echo app('translator')->get('View All'); ?></span>
                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-chevron-right'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-4 rtl:rotate-180']); ?>
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
        <div class="inline-flex flex-wrap gap-7 px-2 pt-1 max-sm:gap-3">
            <div class="inline-flex items-center gap-2">
                <span class="size-2.5 rounded-sm bg-accent"></span>
                <span class="text-sm leading-5 text-heading-foreground"><?php echo app('translator')->get('Text'); ?></span>
                <span class="leading-5 text-foreground/70"><?php echo e($textDocsCount); ?></span>
            </div>
            <div class="inline-flex items-center gap-2">
                <span class="size-2.5 rounded-sm bg-[#1CA685]"></span>
                <span class="text-sm leading-5 text-heading-foreground"><?php echo app('translator')->get('Image'); ?></span>
                <span class="leading-5 text-foreground/70"><?php echo e($imageDocsCount); ?></span>
            </div>
            <div class="inline-flex items-center gap-2">
                <span class="size-2.5 rounded-sm bg-[#667085]"></span>
                <span class="text-sm leading-5 text-heading-foreground"><?php echo app('translator')->get('Audio'); ?></span>
                <span class="leading-5 text-foreground/70"><?php echo e($audioDocsCount); ?></span>
            </div>
            <div class="inline-flex items-center gap-2">
                <span class="size-2.5 rounded-sm bg-[#E6E7E9]"></span>
                <span class="text-sm leading-5 text-heading-foreground"><?php echo app('translator')->get('Other'); ?></span>
                <span class="leading-5 text-foreground/70"><?php echo e($otherDocsCount); ?></span>
            </div>
        </div>
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
<?php /**PATH /home/u504467222/domains/omini.ai/public_html/resources/views/default/panel/user/dashboard/account-summary.blade.php ENDPATH**/ ?>