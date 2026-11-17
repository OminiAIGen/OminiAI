<?php
    $base_class = 'lqd-modal lqd-modal-' . $type . ' relative';
    $modal_base_class = 'lqd-modal-modal z-[999] flex items-center justify-center overflow-y-auto overscroll-contain px-4';
    $modal_backdrop_base_class = 'lqd-modal-backdrop fixed inset-0 bg-black/50 backdrop-blur-sm';
    $modal_head_base_class = 'lqd-modal-head flex flex-wrap items-center gap-3 border-b px-4 py-2 relative';
    $modal_body_base_class = 'lqd-modal-body p-10';
    $modal_content_base_class =
        'lqd-modal-content relative z-[100] max-h-[95vh] min-w-[min(100%,540px)] overflow-y-auto overscroll-contain rounded-xl bg-background shadow-2xl shadow-black/10';
    $modal_close_btn_base_class = 'lqd-modal-close size-8 ms-auto inline-flex items-center justify-center rounded-lg transition-all hover:bg-foreground/20';

    if ($type !== 'inline') {
        $modal_base_class .= ' fixed inset-0';
    } else {
        $modal_base_class .= ' hidden fixed max-md:inset-0 md:absolute top-full min-w-[min(calc(100vw-2rem),450px)] mt-3';

        if ($anchor === 'start') {
            $modal_base_class .= ' start-0';
        } else {
            $modal_base_class .= ' end-0';
        }
    }

    if ($type === 'page') {
        $modal_base_class .= ' ';
        $modal_content_base_class .= ' max-h-screen bg-transparent shadow-none w-[clamp(100%,1440px,90vw)]';
        $modal_head_base_class .= ' border-none p-0 w-[clamp(100%,1440px,90vw)]';
        $modal_body_base_class .= ' py-24 px-0';
        $modal_close_btn_base_class .= ' absolute top-11 end-0 lg:end-11 bg-background text-heading-foreground size-14 rounded-full hover:bg-background hover:scale-110';
    }

?>

<div
    <?php echo e($attributes->withoutTwMergeClasses()->twMerge($base_class, $attributes->get('class'))); ?>

    x-data="liquidModal({ disableModal: <?php echo e($disableModal ? 'true' : 'false'); ?>, disableModalMessage: '<?php echo e($disableModalMessage); ?>' })"
    :class="{ 'modal-open': modalOpen }"
>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($trigger)): ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($trigger->attributes['custom'] ?? false): ?>
            <?php echo e($trigger); ?>

        <?php else: ?>
            <?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['href' => ''.e($trigger->attributes['href']).'','type' => ''.e($trigger->attributes['type']).'','variant' => ''.e($trigger->attributes['variant']).'','size' => ''.e($trigger->attributes['size']).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => ''.e($trigger->attributes['class']).'','attributes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($trigger->attributes),'@click.prevent' => 'toggleModal()']); ?>
                <?php echo e($trigger); ?>

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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($modal) && !$disableModal): ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type !== 'inline'): ?>
            <template x-teleport="body">
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <div
            <?php echo e($modal->attributes); ?>

            <?php echo e($attributes->twMergeFor('modal', $modal_base_class)); ?>

            x-show="modalOpen"
            x-transition.opacity
            @keyup.escape="if ( !modalLocked ) { modalOpen = false }"
            :class="{ 'hidden': !modalOpen, 'modal-open': modalOpen }"
        >
            <div <?php echo e($attributes->twMergeFor('modal-backdrop', $modal_backdrop_base_class)); ?>></div>

            <div
                <?php echo e($attributes->twMergeFor('modal-content', $modal_content_base_class)); ?>

                @click.outside="if ( !modalLocked ) { modalOpen = false }"
                x-cloak
                x-show="modalOpen"
                x-transition
            >
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'page'): ?>
                    <div <?php echo e($attributes->twMergeFor('modal-container', 'container px-0')); ?>>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type !== 'inline'): ?>
                    <div <?php echo e($attributes->twMergeFor('modal-head', $modal_head_base_class)); ?>>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($title)): ?>
                            <h4 <?php echo e($attributes->twMergeFor('modal-title', 'm-0')); ?>><?php echo e($title); ?></h4>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($headContent)): ?>
                            <?php echo e($headContent); ?>

                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <button
                            <?php echo e($attributes->twMergeFor('close-btn', $modal_close_btn_base_class)); ?>

                            type="button"
                            @click.prevent.stop="if ( !modalLocked ) { modalOpen = false }"
                        >
                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-x'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['attributes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attributes->twMergeFor('close-btn-icon', 'size-5'))]); ?>
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
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'page'): ?>
            </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div
        <?php echo e($attributes->twMergeFor('modal-body', $modal_body_base_class)); ?>

        <?php if(!$disableFocus): ?> x-trap="modalOpen" <?php endif; ?>
    >
        <div <?php echo e($attributes->twMergeFor('modal-container', 'container p-0')); ?>>
            <?php echo e($modal); ?>

        </div>
    </div>
</div>
</div>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type !== 'inline'): ?>
    </template>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<?php if (! $__env->hasRenderedOnce('b0fc54d8-82c8-4389-9265-27debf7a140f')): $__env->markAsRenderedOnce('b0fc54d8-82c8-4389-9265-27debf7a140f');
$__env->startPush('script'); ?>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('liquidModal', () => ({
                modalLocked: false,
                _modalOpen: false,

                get modalOpen() {
                    return this._modalOpen;
                },
                set modalOpen(value) {
                    if (this.modalLocked) return;

                    this._modalOpen = value;

                    this.$dispatch('modal-toggle', {
                        el: this.$el,
                        open: value
                    });
                },

                toggleModal() {
                    if (this.modalLocked) return;
                    <?php if($disableModal): ?>
                        toastr.info('<?php echo e($disableModalMessage); ?>')
                    <?php else: ?>
                        this.modalOpen = !this.modalOpen
                    <?php endif; ?>
                },

                lockUnlockModal(lock) {
                    this.modalLocked = lock;
                }
            }))
        })
    </script>
<?php $__env->stopPush(); endif; ?>
<?php /**PATH D:\omini_customization\OminiAI\resources\views/default/components/modal.blade.php ENDPATH**/ ?>