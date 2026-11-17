<?php if (isset($component)) { $__componentOriginal0feec11b8470b4bf00c37924b86dc0af = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0feec11b8470b4bf00c37924b86dc0af = $attributes; } ?>
<?php $component = App\View\Components\Dropdown\Dropdown::resolve(['anchor' => 'start','offsetY' => '20px','teleport' => false] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dropdown.dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Dropdown\Dropdown::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ai-tools-dropdown','class:dropdown' => 'rounded-3xl']); ?>
     <?php $__env->slot('trigger', null, ['class' => 'before:-star[12.5%] relative z-10 size-9 p-0 text-heading-foreground before:absolute before:inset-0 before:-top-[12.5%] before:h-[150%] before:w-[150%]','variant' => 'link','title' => ''.e(__('AI Tools')).'']); ?> 
        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-grid-dots'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-6']); ?>
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
     <?php $__env->endSlot(); ?>

     <?php $__env->slot('dropdown', null, ['class' => 'max-h-[450px] min-w-80 overflow-y-auto']); ?> 
        <?php
            $items = app(\App\Services\Common\MenuService::class)->generate();
            $isAdmin = \Auth::user()?->isAdmin();


            $ai_tools_list = [
                'dashboard',
                'documents',
                'ai_writer',
                'ai_editor',
                'ai_detector_extension',
                'scheduled_posts_extension',
                'ai_plagiarism_extension',
                'ai_image_generator',
                'ai_social_media_extension',
                'photo_studio_extension',
                'ai_article_wizard',
                'ai_web_chat_extension',
                'seo_tool_extension',
                'ai_pdf',
                // 'chat_settings_extension',
                'ai_vision',
                'ai_rewriter',
                'ai_chat_image',
                'ai_chat_all',
                'ai_code_generator',
                'ai_youtube',
                'ai_rss',
                'ai_speech_to_text',
                'ai_voiceover',
                'ai_voiceover_clone',
                'ai_social_media_extension',
            ];
            $ai_tools = [];

            foreach ($items as $item) {
                if (data_get($item, 'type') === 'item' && in_array(data_get($item, 'key'), $ai_tools_list)) {
                    $ai_tools[] = $item;
                }
            }
        ?>



        <h3 class="m-0 border-b border-border py-5 text-center">
            <?php echo e(__('AI Tools')); ?>

        </h3>

        <ul class="ai-tools-list grid grid-cols-3 gap-2 p-3">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $ai_tools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ai_tool): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if ($__env->exists('default.components.navbar.partials.types.' . $ai_tool['type'], ['item' => $ai_tool])) echo $__env->make('default.components.navbar.partials.types.' . $ai_tool['type'], ['item' => $ai_tool], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </ul>
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
<?php endif; ?>
<?php /**PATH /home/u504467222/domains/omini.ai/public_html/app/Extensions/FocusMode/System/../resources/views/dropdown.blade.php ENDPATH**/ ?>