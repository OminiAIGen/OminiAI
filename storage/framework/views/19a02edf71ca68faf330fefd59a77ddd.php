<?php
    $survey =  $app_is_demo ? null : \App\Extensions\OnboardingPro\System\Models\Survey::query()->where('status', true)->first();
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->type === \App\Enums\Roles::USER && $survey): ?>
        <?php
            $display = \App\Extensions\OnboardingPro\System\Models\SurveyUser::query()
                ->where('user_id', auth()->user()->id)
                ->where('survey_id', $survey->id)
                ->first();
        ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$display): ?>
            <div
                class="survey-container fixed bottom-12 left-1/2 z-50 w-96 -translate-x-1/2 transform rounded-lg bg-background p-5 shadow-xl"
                id="survey-extension"
                x-data="{ open: <?php echo e(session('showSurvey') ? 'true' : 'false'); ?> }"
                x-show="open"
                x-cloak
                style="background-color: <?php echo e($survey->background_color); ?>"
            >

                <button
                    class="close-button absolute right-2 top-0 text-2xl text-white mix-blend-difference focus:outline-none"
                    @click="open = false; <?php echo e(session()->forget('showSurvey')); ?>"
                    aria-label="Close"
                >
                    &times;
                </button>
                <div class="survey-content">
                    <p
                        class="survey-question mb-4 text-center text-lg font-semibold"
                        style="color: <?php echo e($survey->text_color); ?>"
                    >
                        <?php echo e($survey->description); ?>

                    </p>
                    <div class="survey-options flex justify-center gap-4">
                        <button
                            class="emoji-option inline-grid size-8 place-content-center rounded-full border-none bg-transparent text-2xl transition-all hover:scale-110 focus:outline-none"
                            onclick="sendRequest(1, <?php echo e($survey->id); ?>)"
                        >😬</button>
                        <button
                            class="emoji-option inline-grid size-8 place-content-center rounded-full border-none bg-transparent text-2xl transition-all hover:scale-110 focus:outline-none"
                            onclick="sendRequest(2, <?php echo e($survey->id); ?>)"
                        >😟</button>
                        <button
                            class="emoji-option inline-grid size-8 place-content-center rounded-full border-none bg-transparent text-2xl transition-all hover:scale-110 focus:outline-none"
                            onclick="sendRequest(3, <?php echo e($survey->id); ?>)"
                        >😐</button>
                        <button
                            class="emoji-option inline-grid size-8 place-content-center rounded-full border-none bg-transparent text-2xl transition-all hover:scale-110 focus:outline-none"
                            onclick="sendRequest(4, <?php echo e($survey->id); ?>)"
                        >🙂</button>
                        <button
                            class="emoji-option inline-grid size-8 place-content-center rounded-full border-none bg-transparent text-2xl transition-all hover:scale-110 focus:outline-none"
                            onclick="sendRequest(5, <?php echo e($survey->id); ?>)"
                        >🤩</button>
                    </div>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<script>
    function sendRequest(point, surveyId) {
        const url = `<?php echo e(route('dashboard.admin.onboarding-pro.survey.display', ['point' => ':point', 'id' => ':id'])); ?>`.replace(':id', surveyId).replace(':point', point);

        fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
            })
            .then(data => {
                const alertElement = document.getElementById('survey-extension');
                if (alertElement) {
                    alertElement.style.display = 'none';
                }
            })
            .catch(error => {});
    }
</script>
<?php /**PATH D:\omini_customization\OminiAI\app\Extensions\OnboardingPro\System/../resources/views/survey.blade.php ENDPATH**/ ?>