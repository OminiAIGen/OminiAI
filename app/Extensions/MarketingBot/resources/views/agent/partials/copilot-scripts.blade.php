@push('script')
    <script src="{{ custom_theme_url('/assets/libs/markdownit/markdown-it.min.js') }}"></script>
    <script>
        {{-- Renders the campaign body the way WhatsApp/Telegram will show it to recipients.
             Deliberately NOT markdown-it: the channels use single-asterisk *bold*, which
             markdown-it would read as emphasis. Escapes first, so model output stays inert. --}}
        function formatChannelPreview(text) {
            const div = document.createElement('div');
            div.textContent = text ?? '';

            return div.innerHTML
                .replace(/```([\s\S]+?)```/g, '<code>$1</code>')
                .replace(/(^|[\s(])\*(\S(?:[^*\n]*\S)?)\*(?=[\s.,!?)]|$)/g, '$1<strong>$2</strong>')
                .replace(/(^|[\s(])_(\S(?:[^_\n]*\S)?)_(?=[\s.,!?)]|$)/g, '$1<em>$2</em>')
                .replace(/(^|[\s(])~(\S(?:[^~\n]*\S)?)~(?=[\s.,!?)]|$)/g, '$1<s>$2</s>');
        }

        {{-- Plain factory (not Alpine.data) so it can be spread into the shared x-data alongside campaignData(). --}}
        function campaignCopilot() {
            return {
                copilotOpen: true,
                copilotLoading: false,
                copilotInput: '',
                copilotMessages: [],
                totalRecipients: 0,

                get agentConfig() {
                    return window.__marketingAgent ?? {};
                },

                agentUrl(suffix) {
                    return `/dashboard/user/marketing-bot/agent/${this.agentConfig.campaignId}/${suffix}`;
                },

                initCopilot() {
                    const cfg = this.agentConfig;

                    this.refreshRecipients();

                    if (cfg.campaignId) {
                        this.loadCopilotHistory().then(() => {
                            if (cfg.initMessage && this.copilotMessages.length === 0) {
                                this.copilotInput = cfg.initMessage;
                                this.$nextTick(() => this.sendCopilotMessage());
                            }
                        });
                    }
                },

                async loadCopilotHistory() {
                    try {
                        const res = await fetch(this.agentUrl('history'), {
                            headers: { 'Accept': 'application/json' },
                        });
                        const data = await res.json();
                        this.copilotMessages = (data.data ?? []).map(m => ({
                            id: m.id,
                            role: m.role,
                            content: m.content,
                            toolCalls: m.toolCalls ?? [],
                            steps: [],
                        }));
                    } catch (e) {
                        // ignore — start with an empty conversation
                    }
                },

                currentState() {
                    return {
                        name: this.name,
                        content: this.content,
                        contacts: this.contacts,
                        segments: this.segments ?? [],
                        scheduled_at: this.scheduledAt,
                        ai_reply: this.aiReply ?? false,
                        instruction: this.instruction ?? '',
                    };
                },

                async sendCopilotMessage() {
                    const msg = this.copilotInput.trim();
                    if (!msg || this.copilotLoading) return;

                    // No draft yet (manual create page) — bootstrap a draft and reopen in the builder.
                    if (!this.agentConfig.campaignId) {
                        this.startNewCampaign(msg);
                        return;
                    }

                    this.copilotInput = '';
                    this.copilotMessages.push({ id: Date.now(), role: 'user', content: msg });
                    this.copilotLoading = true;

                    this.copilotMessages.push({ id: Date.now() + 1, role: 'assistant', content: '', toolCalls: [], steps: [] });
                    const assistantIdx = this.copilotMessages.length - 1;
                    const updateAssistant = (patch) => {
                        this.copilotMessages[assistantIdx] = Object.assign({}, this.copilotMessages[assistantIdx], patch);
                    };
                    const getSteps = () => this.copilotMessages[assistantIdx].steps;

                    try {
                        const response = await fetch(this.agentUrl('chat'), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'text/event-stream',
                            },
                            body: JSON.stringify({ message: msg, state: this.currentState() }),
                        });

                        const reader = response.body.getReader();
                        const decoder = new TextDecoder();

                        while (true) {
                            const { done, value } = await reader.read();
                            if (done) break;

                            const lines = decoder.decode(value).split('\n').filter(l => l.startsWith('data: '));
                            for (const line of lines) {
                                try {
                                    const event = JSON.parse(line.slice(6));
                                    const steps = [...getSteps()];

                                    if (event.type === 'thinking_start') {
                                        steps.push({ type: 'thinking', status: 'pending', startedAt: Date.now(), durationMs: null });
                                        updateAssistant({ steps });
                                    } else if (event.type === 'thinking_end') {
                                        const idx = steps.map(s => s.type).lastIndexOf('thinking');
                                        if (idx !== -1) {
                                            steps[idx] = { ...steps[idx], status: 'done', durationMs: event.payload?.duration_ms ?? (steps[idx].startedAt ? Date.now() - steps[idx].startedAt : null) };
                                            updateAssistant({ steps });
                                        }
                                    } else if (event.type === 'text_delta') {
                                        const text = event.payload?.text ?? '';
                                        const last = steps[steps.length - 1];
                                        if (last && last.type === 'text') {
                                            steps[steps.length - 1] = { ...last, content: last.content + text };
                                        } else {
                                            steps.push({ type: 'text', content: text });
                                        }
                                        updateAssistant({ steps, content: this.copilotMessages[assistantIdx].content + text });
                                    } else if (event.type === 'tool_call_start') {
                                        steps.push({ type: 'tool_call', name: event.payload.name, id: event.payload.id, args: {}, status: 'pending', expanded: false });
                                        updateAssistant({ steps });
                                    } else if (event.type === 'tool_call_end') {
                                        const tcIdx = steps.findIndex(s => s.type === 'tool_call' && s.id === event.payload.id);
                                        if (tcIdx !== -1) {
                                            steps[tcIdx] = { ...steps[tcIdx], args: event.payload.args ?? {}, status: event.payload.status ?? 'success' };
                                        }
                                        updateAssistant({ steps });
                                        this.applyCopilotMutation({ name: event.payload.name, args: event.payload.args ?? {} });
                                    } else if (event.type === 'action_buttons') {
                                        steps.push({ type: 'action_buttons', buttons: event.payload.buttons ?? [] });
                                        updateAssistant({ steps });
                                    } else if (event.type === 'error') {
                                        const errMsg = event.payload?.message ?? '{{ __('An error occurred. Please try again.') }}';
                                        steps.push({ type: 'text', content: errMsg });
                                        updateAssistant({ steps, content: errMsg });
                                    }
                                } catch (e) {}
                            }
                        }
                    } catch (e) {
                        updateAssistant({ content: '{{ __('An error occurred. Please try again.') }}' });
                    } finally {
                        this.copilotLoading = false;
                    }
                },

                applyCopilotMutation(toolCall) {
                    const args = toolCall.args ?? {};

                    if (toolCall.name === 'set_campaign_field') {
                        if (['name', 'content', 'instruction'].includes(args.field) && args.field in this) {
                            this[args.field] = args.value ?? '';
                        }
                    } else if (toolCall.name === 'set_audience') {
                        if (Array.isArray(args.contacts)) {
                            this.contacts = args.contacts.map(String);
                        }
                        if (Array.isArray(args.segments) && 'segments' in this) {
                            this.segments = args.segments.map(String);
                        }
                        if (typeof args.recipients === 'number') {
                            this.totalRecipients = args.recipients;
                        } else {
                            this.refreshRecipients();
                        }
                    } else if (toolCall.name === 'set_schedule') {
                        if (args.mode === 'scheduled' && args.scheduled_at) {
                            this.isScheduled = true;
                            this.scheduledAt = this.toDatetimeLocal(args.scheduled_at);
                        } else {
                            this.isScheduled = false;
                        }
                    } else if (toolCall.name === 'set_ai_reply') {
                        if ('aiReply' in this) {
                            this.aiReply = !!args.ai_reply;
                        }
                        if ('instruction' in this && typeof args.instruction === 'string') {
                            this.instruction = args.instruction;
                        }
                    }
                },

                handleCopilotAction(action, args) {
                    if (action === 'send_now') {
                        this.isScheduled = false;
                        this.$nextTick(() => this.submitCampaign());
                    } else if (action === 'schedule') {
                        this.isScheduled = true;
                        this.$refs.scheduledAtInput?.focus();
                    } else if (action === 'open_audience') {
                        document.querySelector('[name="contacts[]"]')?.focus();
                    }
                },

                startNewCampaign(message) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/dashboard/user/marketing-bot/agent/start';
                    form.innerHTML = `
                        <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                        <input type="hidden" name="channel" value="${this.agentConfig.channel}">
                        <input type="hidden" name="message">
                    `;
                    form.querySelector('input[name="message"]').value = message;
                    document.body.appendChild(form);
                    form.submit();
                },

                submitCampaign() {
                    this.$root.querySelector('form[data-campaign-form]')?.requestSubmit();
                },

                postNow(e) {
                    if (!this.contacts || this.contacts.length === 0) {
                        e?.preventDefault();
                        toastr.error('{{ __('Please select at least one contact list.') }}');
                        return false;
                    }
                    return true;
                },

                async saveAsDraft() {
                    if (!this.agentConfig.campaignId) {
                        return this.submitCampaign();
                    }
                    try {
                        const res = await fetch(this.agentUrl('save-draft'), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'X-HTTP-Method-Override': 'PUT',
                            },
                            body: JSON.stringify(this.currentState()),
                        });
                        const data = await res.json();
                        (data.status === 'success' ? toastr.success : toastr.error)(data.message);
                    } catch (e) {
                        toastr.error('{{ __('An error occurred. Please try again.') }}');
                    }
                },

                async refreshRecipients() {
                    if (!this.agentConfig.campaignId) return;
                    try {
                        const res = await fetch(this.agentUrl('recipients'), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ contacts: this.contacts ?? [], segments: this.segments ?? [] }),
                        });
                        const data = await res.json();
                        this.totalRecipients = data.count ?? 0;
                    } catch (e) {}
                },

                toDatetimeLocal(value) {
                    const d = new Date(value.replace(' ', 'T'));
                    if (isNaN(d.getTime())) return value;
                    const pad = (n) => String(n).padStart(2, '0');
                    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
                },

                toolCallLabel(step) {
                    switch (step.name) {
                        case 'set_campaign_field':
                            return step.args?.field === 'content' ? '{{ __('Campaign copy written') }}'
                                : step.args?.field === 'name' ? '{{ __('Campaign title set') }}'
                                : '{{ __('AI reply instruction set') }}';
                        case 'set_audience':
                            return '{{ __('Audience selected') }}';
                        case 'set_schedule':
                            return step.args?.mode === 'scheduled' ? '{{ __('Schedule set') }}' : '{{ __('Sending immediately') }}';
                        case 'set_ai_reply':
                            return '{{ __('AI auto-reply configured') }}';
                        default:
                            return (step.name || '').replace(/_/g, ' ');
                    }
                },

                renderMarkdown(content) {
                    if (!content) return '';

                    if (window.markdownit) {
                        {{-- html: false — this feeds x-html and the model output is untrusted. --}}
                        this._md ??= window.markdownit({ html: false, breaks: true, linkify: true });

                        return this._md.render(content);
                    }

                    const escaped = document.createElement('span');
                    escaped.textContent = content;

                    return escaped.innerHTML;
                },

                formatThinkingDuration(ms) {
                    if (ms === null || ms === undefined) return '';
                    if (ms < 1000) return `${ms}ms`;
                    const s = ms / 1000;
                    return s < 60 ? `${s.toFixed(s < 10 ? 1 : 0)}s` : `${Math.floor(s / 60)}m ${Math.round(s % 60)}s`;
                },
            };
        }

        {{-- Merge campaignData() + campaignCopilot() while PRESERVING getters (e.g. previewText).
             A plain object spread would invoke getters once and freeze them into static values,
             breaking the live message preview. --}}
        function marketingAgentData() {
            const merged = {};
            Object.defineProperties(merged, Object.getOwnPropertyDescriptors(campaignCopilot()));
            Object.defineProperties(merged, Object.getOwnPropertyDescriptors(campaignData()));
            return merged;
        }
    </script>
@endpush
