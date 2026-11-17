@extends('panel.layout.settings')
@section('title', __('Rendley API Settings'))
@section('titlebar_actions', '')
@section('titlebar_subtitle', __('Configure the Rendley API key used to automatically cut, caption and render user footage.'))

@section('settings')
    <form
        method="post"
        action="{{ route('dashboard.admin.auto-edit-videos.update') }}"
        x-data="autoEditVideosSettings"
    >
        @csrf

        <x-card
            class="mb-4"
            size="md"
        >
            <div class="mb-6">
                <label
                    class="form-label"
                    for="auto_edit_videos_api_key"
                >
                    {{ __('Rendley API Key') }}
                </label>
                <input
                    class="form-control"
                    id="auto_edit_videos_api_key"
                    type="password"
                    name="auto_edit_videos_api_key"
                    autocomplete="off"
                    value="{{ $app_is_demo ?? false ? '*********************' : (string) setting('auto_edit_videos_api_key', '') }}"
                    placeholder="{{ __('Paste your Rendley API key') }}"
                />

                <div class="mt-2.5 flex flex-wrap items-center gap-3">
                    <x-button
                        type="button"
                        variant="outline"
                        size="sm"
                        @click="testConnection()"
                        ::disabled="testing"
                    >
                        <span x-show="!testing">{{ __('Test connection') }}</span>
                        <span
                            x-cloak
                            x-show="testing"
                        >{{ __('Testing…') }}</span>
                    </x-button>

                    <p
                        class="text-2xs [&.is-error]:text-rose-500 [&.is-ok]:text-emerald-500"
                        x-cloak
                        x-show="testMessage"
                        x-text="testMessage"
                        :class="testOk ? 'is-ok' : 'is-error'"
                    ></p>
                </div>

                <p class="text-label mt-2.5 text-4xs leading-relaxed">
                    {!! __('Create a key under API Keys in the <a class="underline" href="https://docs.rendley.com/api/getting-started/" target="_blank" rel="noreferrer">Rendley dashboard</a>. The key is stored privately and never exposed to end users.') !!}
                </p>
            </div>

            <div class="mb-6">
                <label
                    class="form-label"
                    for="auto_edit_videos_public_base_url"
                >
                    {{ __('Public media URL') }}
                </label>
                <input
                    class="form-control"
                    id="auto_edit_videos_public_base_url"
                    type="url"
                    name="auto_edit_videos_public_base_url"
                    autocomplete="off"
                    value="{{ (string) setting('auto_edit_videos_public_base_url', '') }}"
                    placeholder="{{ __('https://cdn.example.com/uploads') }}"
                />
                <x-alert
                    class="mt-2.5"
                    variant="lg"
                >
                    <p>
                        {{ __('Rendley downloads every uploaded file over the public internet. Leave this empty when this site is publicly reachable. Set it to a public mirror or CDN prefix when it is not — a local .test or intranet address will never work, and uploads will be refused before they reach Rendley.') }}
                    </p>
                </x-alert>
            </div>

            <div>
                <label
                    class="form-label"
                    for="auto_edit_videos_agent_url"
                >
                    {{ __('Auto edit endpoint') }}
                </label>
                <input
                    class="form-control"
                    id="auto_edit_videos_agent_url"
                    type="url"
                    name="auto_edit_videos_agent_url"
                    autocomplete="off"
                    value="{{ (string) setting('auto_edit_videos_agent_url', '') }}"
                    placeholder="{{ config('auto-edit-videos.api.agent_url') }}"
                />
                <p class="text-label mt-2.5 text-4xs leading-relaxed">
                    {{ __('Leave empty to use the default. The auto edit agent runs on mcp.rendley.com — the rest of the API (exports, jobs, account) runs on api.rendley.com. Override this only if Rendley moves the agent.') }}
                </p>
            </div>
        </x-card>

        <x-button
            class="w-full"
            type="submit"
        >
            {{ __('Save') }}
        </x-button>
    </form>
@endsection

@push('script')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('autoEditVideosSettings', () => ({
                testing: false,
                testOk: false,
                testMessage: '',

                async testConnection() {
                    this.testing = true;
                    this.testMessage = '';

                    try {
                        const response = await fetch({{ Illuminate\Support\Js::from(route('dashboard.admin.auto-edit-videos.test')) }}, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                                'Accept': 'application/json',
                            },
                        });

                        const data = await response.json();

                        this.testOk = response.ok;
                        this.testMessage = data.message ?? '';
                    } catch (error) {
                        this.testOk = false;
                        this.testMessage = {{ Illuminate\Support\Js::from(__('Could not reach the server.')) }};
                    } finally {
                        this.testing = false;
                    }
                },
            }));
        });
    </script>
@endpush
