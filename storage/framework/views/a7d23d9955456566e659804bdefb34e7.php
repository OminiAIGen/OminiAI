<?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['size' => 'none','href' => ''.e(route('dashboard.user.marketing-bot.inbox.index')).'','variant' => 'link'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'relative flex size-6 items-center justify-center hover:bg-transparent max-lg:hidden max-lg:size-10 max-lg:rounded-full max-lg:border max-lg:dark:bg-white/[3%]','title' => ''.e(__('Marketing Inbox')).'','x-data' => '{}']); ?>
    <span
        class="absolute -end-1.5 -top-1.5 hidden h-4 min-w-4 place-items-center rounded-full bg-red-500 px-1 text-3xs font-medium leading-none text-white [&.active]:inline-grid"
        id="inbox-notification"
    >0</span>
    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-mailbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-5 dark:block','stroke-width' => '1.5']); ?>
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

<?php $__env->startPush('script'); ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(config('marketing-bot.notification_enabled', true)): ?>
        <script>
            $(document).ready(function() {
                // Function to fetch the notification count
                function fetchNotificationCount() {
                    $.ajax({
                        url: '<?php echo e(route('dashboard.user.marketing-bot.inbox.notification.count')); ?>',
                        type: 'GET',
                        success: function(data) {
                            let inboxNotification = $('#inbox-notification');

                            if (data.count > 0) {
                                inboxNotification.text(data.count);
                                inboxNotification.addClass('active')
                            } else {
                                inboxNotification.removeClass('active')
                            }
                        },
                        error: function() {
                            console.error('Error fetching notification count');
                        }
                    });
                }

                // Fetch the notification count on page load
                // fetchNotificationCount();

                // Optionally, you can set an interval to refresh the count periodically
                setInterval(fetchNotificationCount, 10000); // Refresh every minute
            });
        </script>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php $__env->stopPush(); ?>
<?php /**PATH /home/u504467222/domains/omini.ai/public_html/app/Extensions/MarketingBot/System/../resources/views/header/inbox-notification.blade.php ENDPATH**/ ?>