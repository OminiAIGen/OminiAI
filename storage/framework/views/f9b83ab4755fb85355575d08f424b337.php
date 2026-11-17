<?php use Illuminate\Support\Str; ?>
<div
    class="fixed inset-0 z-[1000] flex items-center justify-center bg-black/50"
    id="mediaManagerModal"
    wire:ignore.self
    x-data="{ show: <?php if ((object) ('showModal') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showModal'->value()); ?>')<?php echo e('showModal'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showModal'); ?>')<?php endif; ?> }"
    x-init="$watch('show', value => {
        if (!value) {
            window.dispatchEvent(new CustomEvent('media-manager-reset-selection'));
            lockUnlockAllModals(false);
        } else {
            window.dispatchEvent(new CustomEvent('media-manager-opened'));
            lockUnlockAllModals(true);
        }
    })"
    x-show="show"
    x-transition.opacity
    x-on:click.self="$wire.closeModal()"
    x-cloak
>
    <div class="container relative">
        <?php if (isset($component)) { $__componentOriginal740c66ff9bbfcb19a96a45ba2fa42d64 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal740c66ff9bbfcb19a96a45ba2fa42d64 = $attributes; } ?>
<?php $component = App\View\Components\Card::resolve(['size' => 'lg'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Card::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'relative flex max-h-[90vh] min-h-96 flex-col overscroll-contain rounded-xl p-5 shadow-xl','class:body' => 'static flex min-h-0 grow flex-col overflow-hidden rounded-lg border p-0']); ?>
            <button
                class="absolute -end-3 -top-3 inline-grid size-8 place-items-center rounded-full bg-card-background shadow-lg shadow-black/5 transition md:-end-4 md:-top-4"
                type="button"
                wire:click="closeModal"
                aria-label="<?php echo e(__('Close')); ?>"
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
<?php $component->withAttributes(['class' => 'size-5']); ?>
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

            
             <?php $__env->slot('head', null, ['class' => 'mb-5 w-full rounded-lg border']); ?> 
                <h2 class="mb-4 text-xl font-semibold"><?php echo app('translator')->get('Content Manager'); ?></h2>

                <div class="flex w-full justify-between text-xs max-md:flex-col max-md:gap-2">
                    <p class="-mb-3 hidden text-xs opacity-80 max-md:block">
                        <?php echo app('translator')->get('Filters'); ?>:
                    </p>
                    <div
                        class="relative md:-ms-3"
                        x-data="{ open: false }"
                        @click.outside="open = false"
                    >
                        <button
                            class="hidden w-full items-center gap-1.5 py-2 text-sm font-medium max-md:flex"
                            @click.prevent="open = !open"
                            type="button"
                        >
                            <?php echo e($activeFilter); ?>

                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-chevron-down'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-4 transition',':class' => '{ \'rotate-180\': open }']); ?>
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
                        
                        <ul
                            class="w-full grow text-sm transition-all max-md:pointer-events-none max-md:invisible max-md:absolute max-md:start-0 max-md:top-full max-md:z-[90] max-md:mt-1 max-md:min-w-[min(195px,100%)] max-md:translate-y-1 max-md:rounded-dropdown max-md:border max-md:border-dropdown-border max-md:bg-dropdown-background max-md:p-2 max-md:opacity-0 max-md:shadow-lg max-md:shadow-black/5 max-md:before:absolute max-md:before:inset-x-0 max-md:before:-top-1 max-md:before:bottom-full md:flex md:flex-wrap md:items-center md:justify-start md:gap-2 xl:gap-5 max-md:[&.open]:pointer-events-auto max-md:[&.open]:visible max-md:[&.open]:translate-y-0 max-md:[&.open]:opacity-100"
                            :class="{ 'open': open }"
                        >
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $filters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li>
                                    <button
                                        wire:click="changeFilter('<?php echo e($filter); ?>'); open = false;"
                                        class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                            'rounded-full px-3 py-2 leading-tight transition-all hover:bg-accent/80 hover:text-accent-foreground max-md:w-full max-md:rounded-md max-md:text-start [&.lqd-is-active]:shadow-md [&.lqd-is-active]:shadow-black/5 [&.lqd-is-active]:bg-accent [&.lqd-is-active]:text-accent-foreground',
                                            'lqd-is-active' => $activeFilter === $filter,
                                        ]); ?>"
                                    >
                                        <?php echo app('translator')->get($filter); ?>
                                    </button>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </ul>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <div class="flex text-nowrap text-sm font-medium text-heading-foreground">
                            <?php if (isset($component)) { $__componentOriginal0feec11b8470b4bf00c37924b86dc0af = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0feec11b8470b4bf00c37924b86dc0af = $attributes; } ?>
<?php $component = App\View\Components\Dropdown\Dropdown::resolve(['teleport' => false,'offsetY' => '1rem','anchor' => 'end'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dropdown.dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Dropdown\Dropdown::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class:dropdown-dropdown' => 'max-lg:end-auto max-lg:start-0']); ?>
                                 <?php $__env->slot('trigger', null, ['class' => 'whitespace-nowrap px-0 py-1','variant' => 'link','size' => 'xs']); ?> 
                                    <?php echo e(__('Sort by')); ?>

                                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-chevron-down'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-4 transition',':class' => '{ \'rotate-180\': open }']); ?>
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

                                 <?php $__env->slot('dropdown', null, ['class' => 'overflow-hidden text-2xs font-medium']); ?> 
                                    <div class="lqd-sort-list flex flex-col">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $sortButtons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $button): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <button
                                                class="<?php echo e($sort === $button['sort'] ? 'bg-foreground/5' : ''); ?> group flex w-full items-center gap-1 px-3 py-2 hover:bg-foreground/5"
                                                wire:click.prevent="changeSort('<?php echo e($button['sort']); ?>')"
                                            >
                                                <?php echo e($button['label']); ?>

                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sort === $button['sort']): ?>
                                                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-caret-down-filled'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => ''.e($sortAscDesc === 'asc' ? 'rotate-180' : '').' size-3 opacity-80']); ?>
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
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </button>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php endif; ?>
                        </div>
                        
                        <div class="relative">
                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-search'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'pointer-events-none absolute start-3 top-1/2 z-10 size-4 -translate-y-1/2 opacity-75','stroke-width' => '1.5']); ?>
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
                            <?php if (isset($component)) { $__componentOriginala97611b31e90fc7dc431a34465dcc851 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala97611b31e90fc7dc431a34465dcc851 = $attributes; } ?>
<?php $component = App\View\Components\Forms\Input::resolve(['id' => 'serach-resources','containerClass' => 'peer','type' => 'text','placeholder' => ''.e(__('Search')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Input::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'min-w-48 border-none bg-heading-foreground/5 ps-10 transition-colors max-lg:rounded-md','wire:model.live.debounce.300ms' => 'searchTerm']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala97611b31e90fc7dc431a34465dcc851)): ?>
<?php $attributes = $__attributesOriginala97611b31e90fc7dc431a34465dcc851; ?>
<?php unset($__attributesOriginala97611b31e90fc7dc431a34465dcc851); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala97611b31e90fc7dc431a34465dcc851)): ?>
<?php $component = $__componentOriginala97611b31e90fc7dc431a34465dcc851; ?>
<?php unset($__componentOriginala97611b31e90fc7dc431a34465dcc851); ?>
<?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($searchTerm)): ?>
                                <button
                                    class="absolute right-2 top-1/2 -translate-y-1/2 transform text-gray-400 hover:text-gray-600"
                                    wire:click="clearSearch"
                                    type="button"
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
<?php $component->withAttributes(['class' => 'size-3']); ?>
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
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            
                            <div
                                class="absolute right-2 top-1/2 -translate-y-1/2 transform"
                                wire:loading
                                wire:target="searchTerm"
                            >
                                <div class="h-3 w-3 animate-spin rounded-full border-b border-blue-500"></div>
                            </div>
                        </div>
                    </div>
                </div>
             <?php $__env->endSlot(); ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeFilter === 'Upload Files'): ?>
                <div class="p-4">
                    
                    <div
                        class="group/drop-area relative w-full rounded-3xl border-2 border-dashed px-5 py-8 transition-colors"
                        x-data="mediaManagerUploadArea"
                        @dragover="handleDragOver($event)"
                        @dragleave="handleDragLeave($event)"
                        @drop="handleDrop($event)"
                        :class="{ 'border-blue-500 bg-blue-50': dragover }"
                    >
                        <div class="mx-auto space-y-4 text-center">
                            <div class="mx-auto mb-4 inline-grid w-12 place-content-center">
                                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-circle-arrow-up'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-12 text-heading-foreground opacity-60','stroke-width' => '1.5']); ?>
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
                            </div>

                            <h4 class="text-base"><?php echo app('translator')->get('Drag and Drop a File'); ?></h4>

                            <div class="mx-auto flex w-[300px] items-center gap-7 text-2xs font-medium text-heading-foreground">
                                <span class="inline-flex h-px grow bg-heading-foreground/5"></span>
                                <?php echo app('translator')->get('or'); ?>
                                <span class="inline-flex h-px grow bg-heading-foreground/5"></span>
                            </div>

                            
                            <div
                                class="w-full px-8"
                                x-show="fileProcessing && (!$wire.uploadingFiles || $wire.uploadingFiles.length === 0)"
                                x-transition.opacity
                            >
                                <div class="rounded-lg border border-blue-200 bg-blue-50 p-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <div class="h-4 w-4 animate-spin rounded-full border-b-2 border-blue-500"></div>
                                        <span class="text-xs text-blue-600"><?php echo app('translator')->get('Processing selected files'); ?>...</span>
                                    </div>
                                    <p class="mt-1 text-center text-xs text-blue-500"><?php echo app('translator')->get('Please wait, validating large files may take a moment.'); ?></p>
                                </div>
                            </div>

                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($uploadingFiles)): ?>
                                <div
                                    class="w-full px-8"
                                    x-data
                                    x-init="$el.closest('[x-data]').fileProcessing = false"
                                >
                                    <ul class="mt-2 space-y-1 rounded-lg bg-gray-50 p-3 text-left text-xs text-gray-600">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $uploadingFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li class="flex items-center justify-between">
                                                <span><?php echo e($file?->getClientOriginalName()); ?></span>
                                                <span class="text-gray-400"><?php echo e(number_format($file?->getSize() / 1024, 1)); ?> KB</span>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </ul>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php
                                $uploadValidationErrors = collect($errors->getMessages())->filter(fn($_, $key) => str_starts_with((string) $key, 'uploadingFiles'))->flatten();
                            ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($uploadValidationErrors->isNotEmpty()): ?>
                                <div class="w-full px-8">
                                    <div class="rounded-lg border border-red-200 bg-red-50 p-3">
                                        <ul class="space-y-1 text-xs text-red-600">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $uploadValidationErrors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li>• <?php echo e($err); ?></li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($uploadErrors)): ?>
                                <div class="w-full px-8">
                                    <div class="rounded-lg border border-red-200 bg-red-50 p-3">
                                        <ul class="space-y-1 text-xs text-red-600">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $uploadErrors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li>• <?php echo e($error); ?></li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isUploading && !empty($uploadProgress)): ?>
                                <div class="w-full px-8">
                                    <div class="rounded-lg border border-blue-200 bg-blue-50 p-3">
                                        <div class="flex items-center gap-2">
                                            <div class="h-4 w-4 animate-spin rounded-full border-b-2 border-blue-500"></div>
                                            <span class="text-xs text-blue-600"><?php echo e($uploadProgress); ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($uploadedFiles) && !$isUploading): ?>
                                <div class="w-full px-8">
                                    <div class="rounded-lg border border-green-200 bg-green-50 p-3">
                                        <p class="text-xs font-medium text-green-600">✓ <?php echo e(count($uploadedFiles)); ?> file(s) uploaded successfully!</p>
                                        <ul class="mt-1 space-y-1 text-xs text-green-600">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $uploadedFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li>• <?php echo e($file['name']); ?></li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            
                            <div>
                                <input
                                    class="hidden"
                                    id="fileInput"
                                    data-exclude-media-manager="true"
                                    type="file"
                                    multiple
                                    wire:model="uploadingFiles"
                                    x-ref="fileInput"
                                    @change="handleFileSelect()"
                                    accept="*"
                                />

                                <?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['type' => 'button','variant' => 'outline'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['onclick' => 'document.getElementById(\'fileInput\').click()','disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isUploading)]); ?>
                                    <?php echo e(count($uploadingFiles) ? __('Add More') : __('Browse Files')); ?>

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

                            <p class="m-0 text-3xs font-medium opacity-60">
                                <?php echo app('translator')->get('Max :max files, :maxSize MB each.', [
                                    'max' => setting('media_max_files', 5),
                                    'maxSize' => setting('media_max_size', 25),
                                ]); ?>
                                <br>
                                <?php echo app('translator')->get('Allowed types: :types', [
                                    'types' => setting('media_allowed_types', 'jpg, png, gif, webp, svg, mp4, avi, mov, wmv, flv, webm, mp3, wav, m4a, pdf, doc, docx, xls, xlsx'),
                                ]); ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeFilter === 'Images'): ?>
                <div
                    class="flex min-h-0 grow flex-col"
                    wire:key="media-section-images"
                    x-data="infiniteScroll"
                    x-init="setSelectionState([], <?php echo e($allowMultipleSelection ? 'true' : 'false'); ?>)"
                    x-on:media-manager-reset-selection.window="selectedIds = []"
                >
                    <div
                        class="min-h-0 grow overflow-y-auto overscroll-contain p-4"
                        x-ref="scrollContainer"
                    >
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($searchTerm)): ?>
                            <div class="mb-4 text-sm text-gray-600">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($images->count() > 0): ?>
                                    <?php echo app('translator')->get('Found :count images for', ['count' => $images->count()]); ?> "<strong><?php echo e($searchTerm); ?></strong>"
                                <?php else: ?>
                                    <?php echo app('translator')->get('No images found for'); ?> "<strong><?php echo e($searchTerm); ?></strong>"
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div
                            class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6"
                            wire:loading.class="opacity-50"
                        >
                            <?php
                                $isDisabledImage = $this->isCardDisabled('image');
                            ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div
                                    class="<?php echo e($isDisabledImage ? 'pointer-events-none opacity-30 bg-gray-50 border-gray-200 cursor-not-allowed filter grayscale' : 'cursor-pointer hover:bg-gray-100 hover:shadow-md hover:border-gray-300'); ?> group relative w-fit overflow-hidden rounded-lg border p-2.5 transition-all duration-200"
                                    wire:key="image-card-<?php echo e($image->id); ?>"
                                    :class="{ 'outline outline-[3px] outline-accent': isSelectedLocal('<?php echo e((string) $image->id); ?>') }"
                                    <?php if(!$isDisabledImage): ?> @click="toggleLocalSelection('<?php echo e((string) $image->id); ?>')" <?php endif; ?>
                                    tabindex="<?php echo e($isDisabledImage ? '-1' : '0'); ?>"
                                    role="button"
                                    <?php if($isDisabledImage): ?> aria-disabled="true" <?php endif; ?>
                                >
                                    <div class="relative overflow-hidden rounded-lg">

                                        <?php
                                            $img = $image->output_url ?? $image->url;
                                            if (Str::contains($img, 'uploads')) {
                                                $img = Str::after($img, '/uploads');
                                                $img = '/uploads' . $img;
                                            }
                                        ?>
                                        <img
                                            class="h-32 w-32 object-cover"
                                            src="<?php echo e(ThumbImage($img, 256, 256, 72)); ?>"
                                            alt="<?php echo e($image->title ?? $image->input); ?>"
                                            loading="lazy"
                                            onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjI0IiBoZWlnaHQ9IjI0IiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0xMiAxNkM4LjY4NjI5IDE2IDYgMTMuMzEzNyA2IDEwQzYgNi42ODYyOSA4LjY4NjI5IDQgMTIgNEMxNS4zMTM3IDQgMTggNi42ODYyOSAxOCAxMEMxOCAxMy4zMTM3IDE1LjMxMzcgMTYgMTIgMTZaIiBmaWxsPSIjOUI5QkEwIi8+CjxwYXRoIGQ9Ik0xMiAxMkMxMC44OTU0IDEyIDEwIDExLjEwNDYgMTAgMTBDMTAgOC44OTU0MyAxMC44OTU0IDggMTIgOEMxMy4xMDQ2IDggMTQgOC44OTU0MyAxNCAxMEMxNCAxMS4xMDQ2IDEzLjEwNDYgMTIgMTIgMTJaIiBmaWxsPSJ3aGl0ZSIvPgo8L3N2Zz4K'"
                                        />
                                        <div
                                            class="pointer-events-none absolute bottom-0 left-0 right-0 truncate bg-background p-1 text-xs text-heading-foreground opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                                            :class="{ 'opacity-100': isSelectedLocal('<?php echo e((string) $image->id); ?>') }"
                                            style="pointer-events:none;"
                                        >
                                            <?php echo e($image->title ?? Str::limit($image->input ?? ($image->filename ?? 'Untitled'), 30)); ?>

                                        </div>

                                        
                                        <div
                                            class="absolute right-1 top-1 rounded bg-background px-1 py-0.5 text-xs text-heading-foreground opacity-0 transition-opacity group-hover:opacity-100">
                                            <?php echo e($image->format_date ?? $image->created_at->format('M d, Y')); ?>

                                        </div>

                                        
                                        <span
                                            class="absolute end-1 top-1.5 flex items-center justify-center rounded-full bg-background p-2 shadow-lg"
                                            x-show="isSelectedLocal('<?php echo e((string) $image->id); ?>')"
                                        >
                                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-check'); ?>
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
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="col-span-6 py-8 text-center text-gray-500">
                                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-photo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mx-auto mb-2 size-12 opacity-50']); ?>
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
                                    <p><?php echo app('translator')->get('No images found.'); ?></p>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($searchTerm)): ?>
                                        <p class="mt-1 text-sm"><?php echo app('translator')->get('Try adjusting your search term.'); ?></p>
                                        <button
                                            class="mt-2 text-sm text-blue-500 underline hover:text-blue-700"
                                            wire:click="clearSearch"
                                        >
                                            <?php echo app('translator')->get('Clear search'); ?>
                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasMoreImages && !$isLoading && $images->count() > 0): ?>
                            <div
                                class="flex justify-center py-4"
                                x-ref="loadTrigger"
                            >
                                <div class="text-sm text-gray-500">
                                    <?php echo app('translator')->get('Scroll for more images...'); ?>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        
                        <div
                            class="flex items-center justify-center py-4"
                            wire:loading
                        >
                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-loader-2'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-6 animate-spin']); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                        </div>
                    </div>

                    <div
                        class="flex justify-end border-t border-gray-200 px-4 pb-4 pt-4"
                        x-show="selectedIds.length > 0"
                        x-cloak
                    >
                        <?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['type' => 'button','size' => 'sm','variant' => 'secondary'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full p-2 opacity-90 hover:opacity-100','x-on:click.prevent' => '$wire.insertSelectedFromClient(\'image\', selectedIds)']); ?>
                            <?php echo app('translator')->get($allowMultipleSelection ? 'Insert Selected Images' : 'Insert Selected Image'); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($allowMultipleSelection): ?>
                                (<span x-text="selectedIds.length"></span>)
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeFilter === 'Videos'): ?>
                <div
                    class="flex min-h-0 grow flex-col"
                    wire:key="media-section-videos"
                    x-data="infiniteScroll"
                    x-init="setSelectionState([], <?php echo e($allowMultipleSelection ? 'true' : 'false'); ?>)"
                    x-on:media-manager-reset-selection.window="selectedIds = []"
                >
                    <div
                        class="min-h-0 grow overflow-y-auto overscroll-contain p-4"
                        x-ref="scrollContainer"
                    >
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($searchTerm)): ?>
                            <div class="mb-4 text-sm text-gray-600">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($videos->count() > 0): ?>
                                    <?php echo app('translator')->get('Found :count videos for', ['count' => $videos->count()]); ?> "<strong><?php echo e($searchTerm); ?></strong>"
                                <?php else: ?>
                                    <?php echo app('translator')->get('No videos found for'); ?> "<strong><?php echo e($searchTerm); ?></strong>"
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div
                            class="grid grid-cols-2 items-center gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6"
                            wire:loading.class="opacity-50"
                        >
                            <?php
                                $isDisabledVideo = $this->isCardDisabled('video');
                            ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $videos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $video): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div
                                    class="<?php echo e($isDisabledVideo ? 'pointer-events-none opacity-30 bg-gray-50 border-gray-200 cursor-not-allowed filter grayscale' : 'cursor-pointer hover:bg-gray-100 hover:shadow-md hover:border-gray-300'); ?> group relative w-fit items-center justify-center overflow-hidden rounded-lg border p-2.5 transition-all duration-200"
                                    wire:key="video-card-<?php echo e($video->id); ?>"
                                    :class="{ 'outline outline-[3px] outline-accent': isSelectedLocal('<?php echo e((string) $video->id); ?>') }"
                                    <?php if(!$isDisabledVideo): ?> @click="toggleLocalSelection('<?php echo e((string) $video->id); ?>')" <?php endif; ?>
                                    tabindex="<?php echo e($isDisabledVideo ? '-1' : '0'); ?>"
                                    role="button"
                                    <?php if($isDisabledVideo): ?> aria-disabled="true" <?php endif; ?>
                                    x-data="{
                                        isPlaying: false,
                                        showControls: false,
                                        videoLoaded: false,
                                        loadVideo() {
                                            if (!this.videoLoaded) {
                                                this.$refs.videoPlayer.src = '<?php echo e($video->output_url); ?>';
                                                this.videoLoaded = true;
                                            }
                                        }
                                    }"
                                >
                                    <div class="relative overflow-hidden rounded-lg">
                                        
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($video->output_url) && $video->output_url): ?>
                                            <div class="relative h-32 w-32">
                                                
                                                <div
                                                    class="absolute inset-0 flex items-center justify-center bg-gray-200"
                                                    x-show="!videoLoaded"
                                                >
                                                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-video'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-8 text-gray-400']); ?>
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
                                                </div>

                                                
                                                <video
                                                    class="h-32 w-32 object-cover"
                                                    muted
                                                    preload="none"
                                                    x-ref="videoPlayer"
                                                    x-show="videoLoaded"
                                                    @mouseenter="showControls = true"
                                                    @mouseleave="showControls = false"
                                                    @click.stop
                                                    @ended="isPlaying = false"
                                                    @play="isPlaying = true"
                                                    @pause="isPlaying = false"
                                                    @loadeddata="$refs.videoPlayer.currentTime = 0"
                                                >
                                                    
                                                </video>
                                            </div>
                                        <?php else: ?>
                                            <div class="flex h-32 w-32 items-center justify-center bg-gray-200">
                                                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-video'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-8 text-gray-400']); ?>
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
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($video->output_url) && $video->output_url): ?>
                                            <div
                                                class="absolute inset-0 flex items-center justify-center transition-opacity"
                                                x-show="showControls || !isPlaying"
                                                x-transition:enter="transition-opacity duration-200"
                                                x-transition:enter-start="opacity-0"
                                                x-transition:enter-end="opacity-100"
                                                x-transition:leave="transition-opacity duration-200"
                                                x-transition:leave-start="opacity-100"
                                                x-transition:leave-end="opacity-0"
                                            >
                                                <div class="rounded-full bg-black/50 p-2">
                                                    <button
                                                        class="flex items-center justify-center text-white transition-colors hover:text-gray-300"
                                                        @click.stop="
                                            if (!videoLoaded) {
                                                loadVideo();
                                                // Wait a bit for video to load before playing
                                                setTimeout(() => {
                                                    $refs.videoPlayer.play();
                                                }, 100);
                                            } else if (isPlaying) {
                                                $refs.videoPlayer.pause();
                                            } else {
                                                $refs.videoPlayer.play();
                                            }
                                        "
                                                    >
                                                        
                                                        <div
                                                            class="h-6 w-6 animate-spin rounded-full border-2 border-white border-t-transparent"
                                                            x-show="videoLoaded && !isPlaying && $refs.videoPlayer && $refs.videoPlayer.readyState < 3"
                                                        ></div>

                                                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-player-play'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-6','x-show' => '!isPlaying && (!videoLoaded || ($refs.videoPlayer && $refs.videoPlayer.readyState >= 3))']); ?>
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
                                                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-player-pause'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-6','x-show' => 'isPlaying']); ?>
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
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        <div
                                            class="pointer-events-none absolute bottom-0 left-0 right-0 z-1 truncate bg-background p-1 text-xs text-heading-foreground opacity-100"
                                            style="pointer-events:none;"
                                        >
                                            <?php echo e($video->title ?? Str::limit($video->input ?? ($video->filename ?? 'Untitled'), 30)); ?>

                                        </div>

                                        
                                        <div
                                            class="absolute right-1 top-1 rounded bg-background px-1 py-0.5 text-xs text-heading-foreground opacity-0 transition-opacity group-hover:opacity-100">
                                            <?php echo e($video->format_date ?? $video->created_at->format('M d, Y')); ?>

                                        </div>

                                        
                                        <span
                                            class="absolute end-1 top-1.5 flex items-center justify-center rounded-full bg-background p-2 shadow-lg"
                                            x-show="isSelectedLocal('<?php echo e((string) $video->id); ?>')"
                                        >
                                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-check'); ?>
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
                                        </span>

                                        <div
                                            class="absolute bottom-8 start-2 flex gap-1 rounded-md bg-background p-1"
                                            x-show="!isSelectedLocal('<?php echo e((string) $video->id); ?>')"
                                        >
                                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-video'); ?>
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
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="col-span-6 py-8 text-center text-gray-500">
                                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-video'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mx-auto mb-2 size-12 opacity-50']); ?>
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
                                    <p><?php echo app('translator')->get('No videos found.'); ?></p>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($searchTerm)): ?>
                                        <p class="mt-1 text-sm"><?php echo app('translator')->get('Try adjusting your search term.'); ?></p>
                                        <button
                                            class="mt-2 text-sm text-blue-500 underline hover:text-blue-700"
                                            wire:click="clearSearch"
                                        >
                                            <?php echo app('translator')->get('Clear search'); ?>
                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasMoreVideos && !$isLoading && $videos->count() > 0): ?>
                            <div
                                class="flex justify-center py-4"
                                x-ref="loadTrigger"
                            >
                                <div class="text-sm text-gray-500">
                                    <?php echo app('translator')->get('Scroll for more videos...'); ?>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        
                        <div
                            class="flex items-center justify-center py-4"
                            wire:loading
                        >
                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-loader-2'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-6 animate-spin']); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                        </div>
                    </div>

                    <div
                        class="flex justify-end border-t border-gray-200 px-4 pb-4 pt-4"
                        x-show="selectedIds.length > 0"
                        x-cloak
                    >
                        <?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['type' => 'button','size' => 'sm','variant' => 'secondary'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full p-2 opacity-90 hover:opacity-100','x-on:click.prevent' => '$wire.insertSelectedFromClient(\'video\', selectedIds)']); ?>
                            <?php echo app('translator')->get($allowMultipleSelection ? 'Insert Selected Videos' : 'Insert Selected Video'); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($allowMultipleSelection): ?>
                                (<span x-text="selectedIds.length"></span>)
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeFilter === 'Other Files'): ?>
                <div
                    class="flex min-h-0 grow flex-col"
                    wire:key="media-section-other-files"
                    x-data="infiniteScroll"
                    x-init="setSelectionState([], <?php echo e($allowMultipleSelection ? 'true' : 'false'); ?>)"
                    x-on:media-manager-reset-selection.window="selectedIds = []"
                >
                    <div
                        class="min-h-0 grow overflow-y-auto overscroll-contain p-4"
                        x-ref="scrollContainer"
                    >
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($searchTerm)): ?>
                            <div class="mb-4 text-sm text-gray-600">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($otherFiles->count() > 0): ?>
                                    <?php echo app('translator')->get('Found :count files for', ['count' => $otherFiles->count()]); ?> "<strong><?php echo e($searchTerm); ?></strong>"
                                <?php else: ?>
                                    <?php echo app('translator')->get('No files found for'); ?> "<strong><?php echo e($searchTerm); ?></strong>"
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div
                            class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3"
                            wire:loading.class="opacity-50"
                        >
                            <?php
                                $isDisabledOther = $this->isCardDisabled('other');
                            ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $otherFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div
                                    class="<?php echo e($isDisabledOther ? 'pointer-events-none opacity-30 bg-gray-50 border-gray-200 cursor-not-allowed filter grayscale' : 'cursor-pointer hover:bg-gray-100 hover:shadow-md hover:border-gray-300'); ?> group relative overflow-hidden rounded-lg border p-3 transition-all duration-200"
                                    wire:key="other-card-<?php echo e($file->id); ?>"
                                    :class="{ 'outline outline-[3px] outline-accent': isSelectedLocal('<?php echo e((string) $file->id); ?>') }"
                                    <?php if(!$isDisabledOther): ?> @click="toggleLocalSelection('<?php echo e((string) $file->id); ?>')" <?php endif; ?>
                                    tabindex="<?php echo e($isDisabledOther ? '-1' : '0'); ?>"
                                    role="button"
                                    <?php if($isDisabledOther): ?> aria-disabled="true" <?php endif; ?>
                                >
                                    <div class="flex items-center space-x-3">
                                        
                                        <div class="flex-shrink-0">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($file->extension === 'pdf'): ?>
                                                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-file-type-pdf'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-8 text-red-500']); ?>
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
                                            <?php elseif(in_array($file->extension, ['doc', 'docx'])): ?>
                                                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-file-type-doc'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-8 text-blue-500']); ?>
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
                                            <?php elseif(in_array($file->extension, ['xls', 'xlsx'])): ?>
                                                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-file-type-xls'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-8 text-green-500']); ?>
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
                                            <?php elseif(in_array($file->extension, ['ppt', 'pptx'])): ?>
                                                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-file-type-ppt'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-8 text-orange-500']); ?>
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
                                            <?php else: ?>
                                                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-file'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-8 text-gray-500']); ?>
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
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>

                                        
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-medium text-gray-900">
                                                <?php echo e(\Illuminate\Support\Str::limit($file->filename, 20, '...')); ?>

                                            </p>
                                            <p class="truncate text-sm text-gray-500">
                                                <?php echo e(strtoupper($file->extension)); ?> • <?php echo e(number_format($file->file_size / 1024, 1)); ?> KB
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                <?php echo e($file->format_date); ?>

                                            </p>
                                        </div>

                                        
                                        <span
                                            class="flex items-center justify-center rounded-full bg-accent p-2 text-white"
                                            x-show="isSelectedLocal('<?php echo e((string) $file->id); ?>')"
                                        >
                                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-check'); ?>
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
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="col-span-3 py-8 text-center text-gray-500">
                                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-files'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mx-auto mb-2 size-12 opacity-50']); ?>
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
                                    <p><?php echo app('translator')->get('No files found.'); ?></p>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($searchTerm)): ?>
                                        <p class="mt-1 text-sm"><?php echo app('translator')->get('Try adjusting your search term.'); ?></p>
                                        <button
                                            class="mt-2 text-sm text-blue-500 underline hover:text-blue-700"
                                            wire:click="clearSearch"
                                        >
                                            <?php echo app('translator')->get('Clear search'); ?>
                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasMoreOtherFiles && !$isLoading && $otherFiles->count() > 0): ?>
                            <div
                                class="flex justify-center py-4"
                                x-ref="loadTrigger"
                            >
                                <div class="text-sm text-gray-500">
                                    <?php echo app('translator')->get('Scroll for more files...'); ?>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        
                        <div
                            class="flex items-center justify-center py-4"
                            wire:loading
                        >
                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-loader-2'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-6 animate-spin']); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                        </div>
                    </div>

                    <div
                        class="flex justify-end border-t border-gray-200 px-4 pb-4 pt-4"
                        x-show="selectedIds.length > 0"
                        x-cloak
                    >
                        <?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['type' => 'button','size' => 'sm','variant' => 'secondary'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full p-2 opacity-90 hover:opacity-100','x-on:click.prevent' => '$wire.insertSelectedFromClient(\'other\', selectedIds)']); ?>
                            <?php echo app('translator')->get('Insert Selected Files'); ?> (<span x-text="selectedIds.length"></span>)
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
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeFilter === 'Google Drive'): ?>
                <div class="p-4">
                    <div class="py-8 text-center text-gray-500">
                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-brand-google-drive'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mx-auto mb-2 size-12 opacity-50']); ?>
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
                        <p><?php echo app('translator')->get('Google Drive integration coming soon...'); ?></p>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeFilter === 'Stock Images'): ?>
                <div
                    class="flex min-h-0 grow flex-col"
                    wire:key="media-section-stock-images"
                    x-data="selectionState([], <?php echo e($allowMultipleSelection ? 'true' : 'false'); ?>)"
                    x-on:media-manager-reset-selection.window="selectedIds = []"
                >
                    <div class="min-h-0 grow overflow-y-auto overscroll-contain p-4">
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($searchTerm)): ?>
                            <div class="mb-4 text-sm text-gray-600">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($stockImages->count() > 0): ?>
                                    <?php echo app('translator')->get('Found :count stock images for', ['count' => $stockImages->count()]); ?> "<strong><?php echo e($searchTerm); ?></strong>"
                                <?php else: ?>
                                    <?php echo app('translator')->get('No stock images found for'); ?> "<strong><?php echo e($searchTerm); ?></strong>"
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div
                            class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6"
                            wire:loading.class="opacity-50"
                        >
                            <?php
                                $isDisabledStockImage = $this->isCardDisabled('image');
                            ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $stockImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stockImage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div
                                    class="<?php echo e($isDisabledStockImage ? 'pointer-events-none opacity-30 bg-gray-50 border-gray-200 cursor-not-allowed filter grayscale' : 'cursor-pointer hover:bg-gray-100 hover:shadow-md hover:border-gray-300'); ?> group relative w-fit overflow-hidden rounded-lg border p-2.5 transition-all duration-200"
                                    wire:key="stock-image-card-<?php echo e($stockImage->id); ?>"
                                    :class="{ 'outline outline-[3px] outline-accent': isSelectedLocal('<?php echo e((string) $stockImage->id); ?>') }"
                                    <?php if(!$isDisabledStockImage): ?> @click="toggleLocalSelection('<?php echo e((string) $stockImage->id); ?>')" <?php endif; ?>
                                    tabindex="<?php echo e($isDisabledStockImage ? '-1' : '0'); ?>"
                                    role="button"
                                    <?php if($isDisabledStockImage): ?> aria-disabled="true" <?php endif; ?>
                                >
                                    <div class="relative overflow-hidden rounded-lg">
                                        <img
                                            class="h-32 w-32 object-cover"
                                            src="<?php echo e($stockImage->thumbnail); ?>"
                                            alt="<?php echo e($stockImage->title); ?>"
                                            loading="lazy"
                                            onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjI0IiBoZWlnaHQ9IjI0IiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0xMiAxNkM4LjY4NjI5IDE2IDYgMTMuMzEzNyA2IDEwQzYgNi42ODYyOSA4LjY4NjI5IDQgMTIgNEMxNS4zMTM3IDQgMTggNi42ODYyOSAxOCAxMEMxOCAxMy4zMTM3IDE1LjMxMzcgMTYgMTIgMTZaIiBmaWxsPSIjOUI5QkEwIi8+CjxwYXRoIGQ9Ik0xMiAxMkMxMC44OTU0IDEyIDEwIDExLjEwNDYgMTAgMTBDMTAgOC44OTU0MyAxMC44OTU0IDggMTIgOEMxMy4xMDQ2IDggMTQgOC44OTU0MyAxNCAxMEMxNCAxMS4xMDQ2IDEzLjEwNDYgMTIgMTIgMTJaIiBmaWxsPSJ3aGl0ZSIvPgo8L3N2Zz4K'"
                                        />

                                        
                                        <div
                                            class="pointer-events-none absolute bottom-0 left-0 right-0 truncate bg-background p-1 text-xs text-heading-foreground opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                                            :class="{ 'opacity-100': isSelectedLocal('<?php echo e((string) $stockImage->id); ?>') }"
                                            style="pointer-events:none;"
                                        >
                                            <?php echo e(Str::limit($stockImage->title ?? 'Untitled', 30)); ?>

                                        </div>

                                        
                                        <span
                                            class="absolute end-1 top-1.5 flex items-center justify-center rounded-full bg-background p-2 shadow-lg"
                                            x-show="isSelectedLocal('<?php echo e((string) $stockImage->id); ?>')"
                                        >
                                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-check'); ?>
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
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="col-span-6 py-8 text-center text-gray-500">
                                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-photo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mx-auto mb-2 size-12 opacity-50']); ?>
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
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($searchTerm)): ?>
                                        <p><?php echo app('translator')->get('Enter a search term to find stock images'); ?></p>
                                    <?php else: ?>
                                        <p><?php echo app('translator')->get('No stock images found for'); ?> "<strong><?php echo e($searchTerm); ?></strong>"</p>
                                        <p class="mt-1 text-sm"><?php echo app('translator')->get('Try adjusting your search term.'); ?></p>
                                        <button
                                            class="mt-2 text-sm text-blue-500 underline hover:text-blue-700"
                                            wire:click="clearSearch"
                                        >
                                            <?php echo app('translator')->get('Clear search'); ?>
                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        
                        <div
                            class="flex items-center justify-center py-4"
                            wire:loading
                        >
                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-loader-2'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-6 animate-spin']); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                        </div>
                    </div>

                    
                    <div
                        class="flex justify-end border-t border-gray-200 px-4 pb-4 pt-4"
                        x-show="selectedIds.length > 0"
                        x-cloak
                    >
                        <?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['type' => 'button','size' => 'sm','variant' => 'secondary'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full p-2 opacity-90 hover:opacity-100','x-on:click.prevent' => '$wire.downloadAndInsertStockImagesFromClient(selectedIds)','wire:loading.attr' => 'disabled','wire:loading.class' => 'opacity-50']); ?>
                            <span
                                wire:loading.remove
                                wire:target="downloadAndInsertStockImagesFromClient"
                            >
                                <?php echo app('translator')->get($allowMultipleSelection ? 'Download & Insert Selected Images' : 'Download & Insert Selected Image'); ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($allowMultipleSelection): ?>
                                    (<span x-text="selectedIds.length"></span>)
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                            <span
                                class="flex items-center"
                                wire:loading
                                wire:target="downloadAndInsertStockImagesFromClient"
                            >
                                <svg
                                    class="-ml-1 mr-2 h-4 w-4 animate-spin text-white"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    ></circle>
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    ></path>
                                </svg>
                                <?php echo app('translator')->get('Downloading...'); ?>
                            </span>
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
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeFilter === 'Stock Videos'): ?>
                <div
                    class="flex min-h-0 grow flex-col"
                    wire:key="media-section-stock-videos"
                    x-data="selectionState([], <?php echo e($allowMultipleSelection ? 'true' : 'false'); ?>)"
                    x-on:media-manager-reset-selection.window="selectedIds = []"
                >
                    <div class="min-h-0 grow overflow-y-auto overscroll-contain p-4">
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($searchTerm)): ?>
                            <div class="mb-4 text-sm text-gray-600">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($stockVideos->count() > 0): ?>
                                    <?php echo app('translator')->get('Found :count stock videos for', ['count' => $stockVideos->count()]); ?> "<strong><?php echo e($searchTerm); ?></strong>"
                                <?php else: ?>
                                    <?php echo app('translator')->get('No stock videos found for'); ?> "<strong><?php echo e($searchTerm); ?></strong>"
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div
                            class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6"
                            wire:loading.class="opacity-50"
                        >
                            <?php
                                $isDisabledStockVideo = $this->isCardDisabled('video');
                            ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $stockVideos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stockVideo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div
                                    class="<?php echo e($isDisabledStockVideo ? 'pointer-events-none opacity-30 bg-gray-50 border-gray-200 cursor-not-allowed filter grayscale' : 'cursor-pointer hover:bg-gray-100 hover:shadow-md hover:border-gray-300'); ?> group relative w-fit overflow-hidden rounded-lg border p-2.5 transition-all duration-200"
                                    wire:key="stock-video-card-<?php echo e($stockVideo->id); ?>"
                                    :class="{ 'outline outline-[3px] outline-accent': isSelectedLocal('<?php echo e((string) $stockVideo->id); ?>') }"
                                    <?php if(!$isDisabledStockVideo): ?> @click="toggleLocalSelection('<?php echo e((string) $stockVideo->id); ?>')" <?php endif; ?>
                                    tabindex="<?php echo e($isDisabledStockVideo ? '-1' : '0'); ?>"
                                    role="button"
                                    <?php if($isDisabledStockVideo): ?> aria-disabled="true" <?php endif; ?>
                                    x-data="{ isPlaying: false, showControls: false, videoLoaded: false }"
                                >
                                    <div class="relative overflow-hidden rounded-lg">
                                        
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($stockVideo->thumbnail): ?>
                                            <div class="relative">
                                                <img
                                                    class="h-32 w-32 object-cover"
                                                    src="<?php echo e($stockVideo->thumbnail); ?>"
                                                    alt="<?php echo e($stockVideo->title); ?>"
                                                    loading="lazy"
                                                    x-show="!isPlaying"
                                                    onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjI0IiBoZWlnaHQ9IjI0IiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0xMiAxNkM4LjY4NjI5IDE2IDYgMTMuMzEzNyA2IDEwQzYgNi42ODYyOSA4LjY4NjI5IDQgMTIgNEMxNS4zMTM3IDQgMTggNi42ODYyOSAxOCAxMEMxOCAxMy4zMTM3IDE1LjMxMzcgMTYgMTIgMTZaIiBmaWxsPSIjOUI5QkEwIi8+CjxwYXRoIGQ9Ik0xMiAxMkMxMC44OTU0IDEyIDEwIDExLjEwNDYgMTAgMTBDMTAgOC44OTU0MyAxMC44OTU0IDggMTIgOEMxMy4xMDQ2IDggMTQgOC44OTU0MyAxNCAxMEMxNCAxMS4xMDQ2IDEzLjEwNDYgMTIgMTIgMTJaIiBmaWxsPSJ3aGl0ZSIvPgo8L3N2Zz4K'"
                                                />

                                                
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($stockVideo->preview_url || $stockVideo->url): ?>
                                                    <video
                                                        class="absolute inset-0 h-32 w-32 object-cover"
                                                        x-show="isPlaying"
                                                        muted
                                                        preload="none"
                                                        x-ref="stockVideoPlayer"
                                                        @mouseenter="showControls = true"
                                                        @mouseleave="showControls = false"
                                                        @click.stop
                                                        @ended="isPlaying = false"
                                                        @play="isPlaying = true"
                                                        @pause="isPlaying = false"
                                                        @loadeddata="videoLoaded = true"
                                                    >
                                                        <source
                                                            src="<?php echo e($stockVideo->preview_url ?? $stockVideo->url); ?>"
                                                            type="video/mp4"
                                                        >
                                                    </video>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="flex h-32 w-32 items-center justify-center bg-gray-200">
                                                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-video'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-8 text-gray-400']); ?>
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
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($stockVideo->preview_url || $stockVideo->url): ?>
                                            <div
                                                class="absolute inset-0 flex items-center justify-center transition-opacity"
                                                x-show="showControls || !isPlaying"
                                                x-transition:enter="transition-opacity duration-200"
                                                x-transition:enter-start="opacity-0"
                                                x-transition:enter-end="opacity-100"
                                                x-transition:leave="transition-opacity duration-200"
                                                x-transition:leave-start="opacity-100"
                                                x-transition:leave-end="opacity-0"
                                                @mouseenter="showControls = true"
                                                @mouseleave="showControls = false"
                                            >
                                                <div class="rounded-full bg-black/50 p-2">
                                                    <button
                                                        class="flex items-center justify-center text-white transition-colors hover:text-gray-300"
                                                        @click.stop="
														if (isPlaying) {
															$refs.stockVideoPlayer.pause();
														} else {
															$refs.stockVideoPlayer.play();
														}
													"
                                                    >
                                                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-player-play'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-6','x-show' => '!isPlaying']); ?>
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
                                                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-player-pause'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-6','x-show' => 'isPlaying']); ?>
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
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($stockVideo->duration > 0): ?>
                                            <div class="absolute bottom-2 left-2 rounded bg-black bg-opacity-70 px-1 py-0.5 text-xs text-white">
                                                <?php echo e(gmdate('i:s', $stockVideo->duration)); ?>

                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        
                                        <div
                                            class="pointer-events-none absolute bottom-0 left-0 right-0 truncate bg-background p-1 text-xs text-heading-foreground opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                                            :class="{ 'opacity-100': isSelectedLocal('<?php echo e((string) $stockVideo->id); ?>') }"
                                            style="pointer-events:none;"
                                        >
                                            <?php echo e(Str::limit($stockVideo->title ?? 'Untitled', 30)); ?>

                                        </div>

                                        
                                        <span
                                            class="absolute end-1 top-1.5 flex items-center justify-center rounded-full bg-background p-2 shadow-lg"
                                            x-show="isSelectedLocal('<?php echo e((string) $stockVideo->id); ?>')"
                                        >
                                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-check'); ?>
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
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="col-span-6 py-8 text-center text-gray-500">
                                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-video'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mx-auto mb-2 size-12 opacity-50']); ?>
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
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($searchTerm)): ?>
                                        <p><?php echo app('translator')->get('Enter a search term to find stock videos'); ?></p>
                                    <?php else: ?>
                                        <p><?php echo app('translator')->get('No stock videos found for'); ?> "<strong><?php echo e($searchTerm); ?></strong>"</p>
                                        <p class="mt-1 text-sm"><?php echo app('translator')->get('Try adjusting your search term.'); ?></p>
                                        <button
                                            class="mt-2 text-sm text-blue-500 underline hover:text-blue-700"
                                            wire:click="clearSearch"
                                        >
                                            <?php echo app('translator')->get('Clear search'); ?>
                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        
                        <div
                            class="flex items-center justify-center py-4"
                            wire:loading
                        >
                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-loader-2'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-6 animate-spin']); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                        </div>
                    </div>

                    
                    <div
                        class="flex justify-end border-t border-gray-200 px-4 pb-4 pt-4"
                        x-show="selectedIds.length > 0"
                        x-cloak
                    >
                        <?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['type' => 'button','size' => 'sm','variant' => 'secondary'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full p-2 opacity-90 hover:opacity-100','x-on:click.prevent' => '$wire.downloadAndInsertStockVideosFromClient(selectedIds)','wire:loading.attr' => 'disabled','wire:loading.class' => 'opacity-50']); ?>
                            <span
                                wire:loading.remove
                                wire:target="downloadAndInsertStockVideosFromClient"
                            >
                                <?php echo app('translator')->get($allowMultipleSelection ? 'Download & Insert Selected Videos' : 'Download & Insert Selected Video'); ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($allowMultipleSelection): ?>
                                    (<span x-text="selectedIds.length"></span>)
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                            <span
                                class="flex items-center"
                                wire:loading
                                wire:target="downloadAndInsertStockVideosFromClient"
                            >
                                <svg
                                    class="-ml-1 mr-2 h-4 w-4 animate-spin text-white"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    ></circle>
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    ></path>
                                </svg>
                                <?php echo app('translator')->get('Downloading...'); ?>
                            </span>
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
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
</div>

<script>
    // Enhanced file upload handling
    document.addEventListener('livewire:init', () => {
        if (window.__mediaManagerModalInitialized) {
            return;
        }
        window.__mediaManagerModalInitialized = true;

        window.addEventListener('livewire-upload-error', () => {
            if (document.getElementById('mediaManagerModal')?.offsetParent === null) return;
            const msg = <?php echo json_encode(__('Upload failed. File may be too large—check PHP upload_max_filesize and post_max_size, and nginx client_max_body_size.'), 512) ?>;
            const el = document.createElement('div');
            el.className = 'fixed top-4 right-4 z-[999999] px-4 py-3 rounded-lg shadow-lg text-white bg-red-500';
            el.textContent = msg;
            document.body.appendChild(el);
            setTimeout(() => el.remove(), 6000);
        });

        Livewire.hook('message.failed', (message, component) => {
            if (document.getElementById('mediaManagerModal')?.offsetParent === null) return;
            const status = message.response?.status ?? message.xhr?.status;
            let msg = <?php echo json_encode(__('Upload or request failed.'), 15, 512) ?>;
            if (status === 413) {
                msg = <?php echo json_encode(__('File too large. Increase upload_max_filesize and post_max_size in PHP, or client_max_body_size in nginx.'), 512) ?>;
            } else if (status) {
                msg = msg + ' (HTTP ' + status + ')';
            }
            const el = document.createElement('div');
            el.className = 'fixed top-4 right-4 z-[999999] px-4 py-3 rounded-lg shadow-lg text-white bg-red-500';
            el.textContent = msg;
            document.body.appendChild(el);
            setTimeout(() => el.remove(), 6000);
        });

        Livewire.on('uploadComplete', (data) => {
            //console.log('Upload completed:', data);
        });

        // Add global file input change listener for immediate feedback
        document.addEventListener('change', function(e) {
            if (e.target.matches('input[type="file"][data-exclude-media-manager]')) {
                const files = Array.from(e.target.files);
                if (files.length > 0) {
                    // Show immediate feedback for file selection
                    const fileNames = files.map(f => f.name).join(', ');
                    //console.log(`Selected files: ${fileNames}`);

                    // Check for large files and show warning
                    const largeFiles = files.filter(f => f.size > 10 * 1024 * 1024); // 10MB+
                    if (largeFiles.length > 0) {
                        // Create a temporary notification for large file processing
                        const tempNotification = document.createElement('div');
                        tempNotification.className = 'fixed top-4 right-4 z-[999999] px-4 py-3 rounded-lg shadow-lg text-white bg-blue-500';
                        tempNotification.innerHTML = `
							<div class="flex items-center gap-2">
								<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
								<span><?php echo app('translator')->get('Processing large files, please wait...'); ?></span>
							</div>
						`;
                        document.body.appendChild(tempNotification);

                        // Remove after file processing should be complete
                        setTimeout(() => {
                            if (tempNotification.parentNode) {
                                tempNotification.remove();
                            }
                        }, 1000);
                    }
                }
            }
        });

        Alpine.data('infiniteScroll', () => ({
            observer: null,
            observerTimeout: null,
            modalOpenedHandler: null,
            loadingMore: false,
            selectedIds: [],
            allowMultiple: false,

            init() {
                if (this.isScrollContainerReady()) {
                    this.scheduleIntersectionObserverSetup();
                }

                this.modalOpenedHandler = () => {
                    this.scheduleIntersectionObserverSetup(100);
                };
                window.addEventListener('media-manager-opened', this.modalOpenedHandler);

                // Listen for Livewire updates and reinitialize observer
                this.$wire.on('searchUpdated', () => {
                    this.scheduleIntersectionObserverSetup(80);
                });

                // Listen for upload completion
                this.$wire.on('uploadComplete', (data) => {
                    // Show success notification
                    if (data.message) {
                        this.showNotification(data.message, 'success');
                    }
                });

                // Listen for file processing events
                this.$wire.on('fileProcessingStarted', () => {
                    //console.log('File processing started');
                });

                this.$wire.on('fileProcessingCompleted', (data) => {
                    //console.log('File processing completed', data);
                    if (data.validFileCount > 0) {
                        this.showNotification(`${data.validFileCount} file(s) ready for upload`, 'info');
                    }
                    if (data.hasErrors) {
                        this.showNotification('Some files had validation errors', 'warning');
                    }
                });

                // Listen for upload progress clearing
                this.$wire.on('clearUploadProgress', () => {
                    setTimeout(() => {
                        this.$wire.uploadProgress = '';
                    }, 3000);
                });
            },

            setSelectionState(selectedIds, allowMultiple = false) {
                this.selectedIds = Array.isArray(selectedIds) ? selectedIds : [];
                this.allowMultiple = !!allowMultiple;
            },

            toggleLocalSelection(id) {
                const normalizedId = String(id);
                const selectedIndex = this.selectedIds.findIndex(item => String(item) === normalizedId);

                if (selectedIndex !== -1) {
                    this.selectedIds.splice(selectedIndex, 1);
                    return;
                }

                if (!this.allowMultiple) {
                    this.selectedIds = [normalizedId];
                    return;
                }

                this.selectedIds.push(normalizedId);
            },

            isSelectedLocal(id) {
                const normalizedId = String(id);
                return this.selectedIds.some(item => String(item) === normalizedId);
            },

            isScrollContainerReady() {
                const scrollContainer = this.$refs.scrollContainer;
                return !!(scrollContainer && scrollContainer.clientHeight > 0 && scrollContainer.offsetParent !== null);
            },

            scheduleIntersectionObserverSetup(delay = 0) {
                if (this.observerTimeout) {
                    clearTimeout(this.observerTimeout);
                }
                this.observerTimeout = setTimeout(() => {
                    if (!this.isScrollContainerReady()) {
                        return;
                    }
                    this.setupIntersectionObserver();
                }, delay);
            },

            setupIntersectionObserver() {
                // Disconnect existing observer
                if (this.observer) {
                    this.observer.disconnect();
                    this.observer = null;
                }

                const loadTrigger = this.$refs.loadTrigger;
                const scrollContainer = this.$refs.scrollContainer;
                if (!loadTrigger || !scrollContainer) {
                    return;
                }

                this.observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (!entry.isIntersecting || this.loadingMore) {
                            return;
                        }

                        this.loadingMore = true;
                        this.observer?.unobserve(loadTrigger);

                        Promise.resolve(this.$wire.loadMore())
                            .finally(() => {
                                this.loadingMore = false;
                                this.scheduleIntersectionObserverSetup(80);
                            });
                    });
                }, {
                    root: scrollContainer,
                    threshold: 0.1,
                    rootMargin: '50px'
                });

                this.observer.observe(loadTrigger);
            },

            showNotification(message, type = 'info') {
                // Enhanced notification system with better styling
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 z-[999999] px-4 py-3 rounded-lg shadow-lg text-white transform transition-all duration-300 translate-x-full opacity-0 ${
					type === 'success' ? 'bg-green-500' :
						type === 'error' ? 'bg-red-500' :
							type === 'warning' ? 'bg-yellow-500' :
								'bg-blue-500'
				}`;
                notification.textContent = message;

                document.body.appendChild(notification);

                // Animate in
                setTimeout(() => {
                    notification.style.transform = 'translateX(0)';
                    notification.style.opacity = '1';
                }, 100);

                // Animate out and remove
                setTimeout(() => {
                    notification.style.transform = 'translateX(100%)';
                    notification.style.opacity = '0';
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.remove();
                        }
                    }, 300);
                }, 4000);
            },

            destroy() {
                if (this.observer) {
                    this.observer.disconnect();
                }
                if (this.observerTimeout) {
                    clearTimeout(this.observerTimeout);
                }
                if (this.modalOpenedHandler) {
                    window.removeEventListener('media-manager-opened', this.modalOpenedHandler);
                }
            }
        }))

        Alpine.data('selectionState', (selectedIds = [], allowMultiple = false) => ({
            selectedIds: Array.isArray(selectedIds) ? selectedIds : [],
            allowMultiple: !!allowMultiple,

            toggleLocalSelection(id) {
                const normalizedId = String(id);
                const selectedIndex = this.selectedIds.findIndex(item => String(item) === normalizedId);

                if (selectedIndex !== -1) {
                    this.selectedIds.splice(selectedIndex, 1);
                    return;
                }

                if (!this.allowMultiple) {
                    this.selectedIds = [normalizedId];
                    return;
                }

                this.selectedIds.push(normalizedId);
            },

            isSelectedLocal(id) {
                const normalizedId = String(id);
                return this.selectedIds.some(item => String(item) === normalizedId);
            }
        }))

        Alpine.data('mediaManagerUploadArea', () => ({
            dragover: false,
            fileProcessing: false,
            handleDrop(e) {
                e.preventDefault();
                this.dragover = false;
                const files = Array.from(e.dataTransfer.files);
                if (!files.length) return;

                // Show processing state immediately
                this.fileProcessing = true;

                // Get reference to hidden file input
                const fileInput = this.$refs.fileInput;
                if (!fileInput) {
                    console.error('File input not found');
                    this.fileProcessing = false;
                    return;
                }

                try {
                    // Create DataTransfer object to simulate input change
                    const dt = new DataTransfer();
                    files.forEach(file => dt.items.add(file));

                    // Set files to the input
                    fileInput.files = dt.files;

                    // Trigger the input change event to make Livewire detect the change
                    fileInput.dispatchEvent(new Event('change', {
                        bubbles: true
                    }));
                } catch (error) {
                    console.error('Error handling file drop:', error);
                    this.fileProcessing = false;
                }
            },
            handleFileSelect() {
                // Show processing state immediately when files are selected
                this.fileProcessing = true;

                // Set a timeout to hide processing state if no files are shown
                setTimeout(() => {
                    // Check if upload is still in progress
                    if (!this.$wire.uploadingFiles || this.$wire.uploadingFiles.length === 0) {
                        this.fileProcessing = false;
                    }
                }, 10000); // 10 seconds timeout
            },
            handleDragOver(e) {
                e.preventDefault();
                this.dragover = true;
            },
            handleDragLeave(e) {
                e.preventDefault();
                // Only set dragover to false if we're actually leaving the drop area
                const rect = this.$el.getBoundingClientRect();
                const x = e.clientX;
                const y = e.clientY;

                if (x < rect.left || x > rect.right || y < rect.top || y > rect.bottom) {
                    this.dragover = false;
                }
            }
        }))
    });
</script>
<?php /**PATH D:\omini_customization\OminiAI\resources\views/livewire/media-manager-modal.blade.php ENDPATH**/ ?>