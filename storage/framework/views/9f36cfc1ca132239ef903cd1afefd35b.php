<div
    class="lqd-tabs-content <?php echo e(!$loop->first ? 'hidden' : ''); ?>"
    id="<?php echo e(\Illuminate\Support\Str::slug($item->menu_title)); ?>"
>
    <div class="grid grid-cols-1 place-items-center gap-7 text-white lg:grid-cols-2 lg:gap-12">
        <figure class="w-full rounded-[20px] bg-white/5 p-3 shadow-xl shadow-black/10">
            <img
                class="w-full rounded-lg"
                width="878"
                height="748"
                src="<?php echo e(custom_theme_url($item->image, true)); ?>"
                alt="<?php echo e(__($item->image_title)); ?>"
            >
        </figure>

        <div class="lg:pe-24">
            <h2 class="mb-6 text-current">
                <?php echo __($item->title); ?>

            </h2>

            <p class="text-lg/6 opacity-60">
                <?php echo __($item->text); ?>

            </p>
        </div>
    </div>
</div>
<?php /**PATH D:\omini_customization\OminiAI\resources\views/marketing-bot/landing-page/generators/item-content.blade.php ENDPATH**/ ?>