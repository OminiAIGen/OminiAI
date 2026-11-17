<?php
    $isSharedCreditUser = auth()->check() && auth()->user()->isSharedCreditUser()
        && app(\App\Services\SharedCredit\SharedCreditService::class)->isEnabled()
        && (bool) setting('shared_credit_show_cost_preview', false);
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isSharedCreditUser): ?>
    <div
        <?php echo e($attributes->withoutTwMergeClasses()->twMerge('lqd-cost-preview mt-3 flex justify-end')); ?>

        x-data="costPreview"
        x-cloak
        @generator-changed.window="onGeneratorChanged($event.detail)"
    >
        <div
            class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-3xs transition-all"
            :class="{
                'bg-foreground/[3%]': data && data.has_balance,
                'bg-amber-500/10': data && !data.has_balance && data.allow_negative,
                'bg-red-500/10': data && !data.has_balance && !data.allow_negative,
            }"
            x-show="visible"
            x-transition.opacity.duration.200ms
        >
            
            <template x-if="loading">
                <span class="flex items-center gap-1.5 text-foreground/50">
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-loader-2'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-3.5 animate-spin']); ?>
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
                    <?php echo app('translator')->get('Calculating...'); ?>
                </span>
            </template>

            
            <template x-if="!loading && error">
                <span class="text-red-500" x-text="error"></span>
            </template>

            
            <template x-if="!loading && !error && data">
                <span class="flex items-center gap-2">
                    <span class="flex items-center gap-1 font-medium text-heading-foreground">
                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-bolt'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-3.5 text-primary']); ?>
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
                        <span x-text="Number(data.total_cost).toFixed(1)"></span>
                        <?php echo app('translator')->get('credits'); ?>
                    </span>
                    <span class="size-1 rounded-full bg-foreground/20"></span>
                    <span class="text-foreground/50">
                        <?php echo app('translator')->get('Balance:'); ?>
                        <span
                            class="font-medium"
                            :class="{
                                'text-foreground/70': data.has_balance,
                                'text-amber-500': !data.has_balance && data.allow_negative,
                                'text-red-500': !data.has_balance && !data.allow_negative,
                            }"
                            x-text="Number(data.balance).toFixed(1)"
                        ></span>
                    </span>
                    <template x-if="!data.has_balance && !data.allow_negative">
                        <span class="flex items-center gap-1 font-medium text-red-500">
                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-alert-circle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-3.5']); ?>
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
                            <?php echo app('translator')->get('Insufficient'); ?>
                        </span>
                    </template>
                    <template x-if="!data.has_balance && data.allow_negative">
                        <span class="flex items-center gap-1 font-medium text-amber-500">
                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-alert-triangle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-3.5']); ?>
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
                            <?php echo app('translator')->get('Low balance'); ?>
                        </span>
                    </template>
                </span>
            </template>
        </div>
    </div>

    <?php if (! $__env->hasRenderedOnce('aef45350-7443-4dbd-87d6-94f399bd3627')): $__env->markAsRenderedOnce('aef45350-7443-4dbd-87d6-94f399bd3627');
$__env->startPush('script'); ?>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('costPreview', () => ({
                    loading: false,
                    error: null,
                    data: null,
                    visible: false,
                    _debounceTimer: null,
                    _lastGenerator: null,
                    _lastQuantity: null,
                    _lastAction: null,

                    onGeneratorChanged(detail) {
                        const generator = detail?.generator || detail;
                        const quantity = parseFloat(detail?.quantity) || 1;
                        const action = detail?.action || null;
                        const force = !!detail?._force;
                        this.debouncedFetch(generator, quantity, action, force);
                    },

                    debouncedFetch(generator, quantity, action = null, force = false) {
                        clearTimeout(this._debounceTimer);
                        this._debounceTimer = setTimeout(() => {
                            this.fetchPreview(generator, quantity, action, force);
                        }, 300);
                    },

                    async fetchPreview(generator, quantity = 1, action = null, force = false) {
                        if (!generator) {
                            this.visible = false;
                            return;
                        }

                        if (!force && generator === this._lastGenerator && quantity === this._lastQuantity && action === this._lastAction && this.data) {
                            return;
                        }

                        this._lastGenerator = generator;
                        this._lastQuantity = quantity;
                        this._lastAction = action;
                        this.loading = true;
                        this.error = null;
                        this.visible = true;

                        try {
                            const body = { generator, quantity };
                            if (action) body.action = action;

                            const response = await fetch('<?php echo e(route("shared-credit.cost-preview")); ?>', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify(body),
                            });

                            const result = await response.json();

                            if (!response.ok) {
                                this.error = result.message || '<?php echo e(__("Could not calculate cost.")); ?>';
                                return;
                            }

                            if (!result.is_shared_credit_user) {
                                this.visible = false;
                                return;
                            }

                            this.data = result.data;
                        } catch (e) {
                            this.error = '<?php echo e(__("Could not calculate cost.")); ?>';
                        } finally {
                            this.loading = false;
                        }
                    },
                }));
            });
        </script>
    <?php $__env->stopPush(); endif; ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /home/u504467222/domains/omini.ai/public_html/resources/views/default/components/cost-preview.blade.php ENDPATH**/ ?>