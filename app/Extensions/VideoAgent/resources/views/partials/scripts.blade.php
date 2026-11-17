{{--
    Every Alpine component the Video Agent workspace uses, registered once.

    Routes and limits travel in a JSON <script> tag rather than being
    interpolated into each component, so the same factories work on the landing
    page, the session thread and any tool page that opens a picker.

    Note: `@js` is taken by the theme's Asset::script directive in this app, so
    payloads are encoded with json_encode / Js::from directly.
--}}
@pushOnce('script')
    @php
        $videoAgentConfig = [
            'csrf'  => csrf_token(),
            'demo'  => \App\Helpers\Classes\Helper::appIsDemo(),
            'poll'  => max(5, (int) setting('video_agent_poll_interval', config('video-agent.poll.interval_seconds'))) * 1000,
            'limits' => [
                'maxFiles'  => (int) config('video-agent.limits.max_files'),
                'promptMax' => (int) config('video-agent.limits.prompt_max'),
            ],
            'routes' => [
                'index'           => route('dashboard.user.video-agent.index'),
                'check'           => route('dashboard.user.video-agent.check'),
                'avatars'         => route('dashboard.user.video-agent.avatars.index'),
                'avatarsSync'     => route('dashboard.user.video-agent.avatars.sync'),
                'createTwin'      => route('dashboard.user.video-agent.avatars.twins.store'),
                'avatarConsent'   => route('dashboard.user.video-agent.avatars.consent', '__ID__'),
                'avatarRefresh'   => route('dashboard.user.video-agent.avatars.refresh', '__ID__'),
                'voices'          => route('dashboard.user.video-agent.voices.index'),
                'voicesSync'      => route('dashboard.user.video-agent.voices.sync'),
                'styles'          => route('dashboard.user.video-agent.styles.index'),
                'stylesSync'      => route('dashboard.user.video-agent.styles.sync'),
                'library'         => route('dashboard.user.video-agent.library'),
                'templates'       => route('dashboard.user.video-agent.templates.index'),
                'brand'           => route('dashboard.user.video-agent.brand.index'),
                'brandRefresh'    => route('dashboard.user.video-agent.brand.refresh'),
                'brandKits'       => route('dashboard.user.video-agent.brand.kits.store'),
                'brandKit'        => route('dashboard.user.video-agent.brand.kits.update', '__ID__'),
                'brandGlossaries' => route('dashboard.user.video-agent.brand.glossaries.store'),
                'bulkDelete'      => route('dashboard.user.video-agent.videos.bulk-delete'),
                'assets'          => route('dashboard.user.video-agent.assets.store'),
                'script'          => route('dashboard.user.video-agent.script.generate'),
            ],
            'messages' => [
                'loadFailed'         => __('Could not load that. Please try again.'),
                'uploadFailed'       => __('The upload failed.'),
                'confirmDelete'      => __('Delete permanently?'),
                'demo'               => __('This feature is disabled in demo mode.'),
                'avatarVoice'        => __('Avatar default'),
                'twinFileError'      => __('Choose an MP4 or WebM video under 32 MB.'),
                'twinCreateError'    => __('Could not create your Digital Twin. Please try again.'),
                'consentError'       => __('Could not open consent. Please try again.'),
                'cancel'             => __('Cancel'),
                'createTwin'         => __('Create Digital Twin'),
                'uploading'          => __('Uploading…'),
                'confirmDeleteKit'   => __('Delete this brand kit? This can\'t be undone.'),
                'glossaryIncomplete' => __('Every term needs both a spelling and a pronunciation.'),
            ],
        ];
    @endphp

    <script
        id="video-agent-config"
        type="application/json"
    >{!! json_encode($videoAgentConfig, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) !!}</script>

    <script>
        document.addEventListener('alpine:init', () => {
            const config = JSON.parse(document.getElementById('video-agent-config').textContent);

            const modalOf = (id) => {
                const el = document.querySelector('#' + id);

                return el ? Alpine.$data(el) : null;
            };

            const notifyError = (message) => window.toastr && toastr.error(message || config.messages.loadFailed);

            const denyDemoAction = () => {
                if (!config.demo) {
                    return false;
                }

                window.toastr && toastr.info(config.messages.demo);

                return true;
            };

            const jsonHeaders = {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': config.csrf,
            };

            /**
             * Shared by every picker modal: search, filters, and an
             * IntersectionObserver-free infinite scroll via x-intersect.
             */
            Alpine.data('videoAgentPicker', (options) => ({
                kind: options.kind,
                demo: config.demo,
                messages: config.messages,
                modalId: options.modalId,
                endpoint: config.routes[options.endpoint],
                syncEndpoint: options.syncEndpoint ? config.routes[options.syncEndpoint] : null,
                pageSize: options.pageSize || 24,
                filters: Object.assign({}, options.filters || {}),
                items: [],
                meta: {},
                page: 1,
                hasMore: false,
                loading: false,
                loaded: false,
                syncing: false,
                refreshing: false,
                playingId: null,
                showTwinForm: false,
                twinName: '',
                twinFile: null,
                uploadingTwin: false,
                twinError: '',
                consentUrls: {},
                pendingTimer: null,
                refreshingPending: false,

                init() {
                    window.addEventListener('va-open', (event) => {
                        if (event.detail === this.kind) {
                            this.open();
                        }
                    });

                    if (this.kind === 'avatar') {
                        this.pendingTimer = window.setInterval(() => {
                            if (modalOf(this.modalId)?.modalOpen) {
                                this.refreshPending();
                            }
                        }, 15000);
                    }
                },

                destroy() {
                    if (this.pendingTimer) {
                        window.clearInterval(this.pendingTimer);
                    }
                },

                open() {
                    const modal = modalOf(this.modalId);

                    if (modal) {
                        modal.modalOpen = true;
                    }

                    if (!this.loaded && !this.loading) {
                        this.reload();
                    } else if (this.kind === 'avatar') {
                        this.refreshPending();
                    }
                },

                close() {
                    const modal = modalOf(this.modalId);

                    if (modal) {
                        modal.modalOpen = false;
                    }
                },

                isFilter(key, value) {
                    return (this.filters[key] ?? '') === value;
                },

                setFilter(key, value) {
                    this.filters[key] = value;
                    this.reload();
                },

                buildQuery() {
                    const params = new URLSearchParams();

                    params.set('page_size', this.pageSize);
                    params.set('page', this.page);

                    Object.entries(this.filters).forEach(([key, value]) => {
                        if (value !== '' && value !== null && value !== undefined) {
                            params.set(key, value);
                        }
                    });

                    return params.toString();
                },

                async reload() {
                    this.page = 1;
                    this.items = [];
                    await this.fetchPage();
                },

                async loadMore() {
                    if (this.loading || !this.hasMore) {
                        return;
                    }

                    this.page += 1;
                    await this.fetchPage();
                },

                async fetchPage() {
                    this.loading = true;

                    try {
                        const response = await fetch(`${this.endpoint}?${this.buildQuery()}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                        });

                        if (!response.ok) {
                            throw new Error('request failed');
                        }

                        const json = await response.json();
                        const rows = json.data || [];

                        this.items = this.page === 1 ? rows : [...this.items, ...rows];
                        this.hasMore = !!json.has_more;
                        this.syncing = !!json.syncing;
                        this.meta = json;
                        this.loaded = true;

                        if (this.kind === 'avatar') {
                            this.refreshPending();
                        }
                    } catch (error) {
                        notifyError();
                    } finally {
                        this.loading = false;
                    }
                },

                /** Pull the catalog again from HeyGen, then re-read page one. */
                async refresh() {
                    if (denyDemoAction() || !this.syncEndpoint || this.refreshing) {
                        return;
                    }

                    this.refreshing = true;

                    try {
                        const response = await fetch(this.syncEndpoint, {
                            method: 'POST',
                            headers: jsonHeaders,
                        });
                        const json = await response.json();

                        window.toastr && toastr.success(json.message);
                        await this.reload();
                    } catch (error) {
                        notifyError();
                    } finally {
                        this.refreshing = false;
                    }
                },

                choose(item) {
                    if (this.kind === 'avatar' && !item.ready) {
                        return;
                    }

                    window.dispatchEvent(new CustomEvent('va-select', {
                        detail: { kind: this.kind, item },
                    }));
                    this.close();
                },

                clear() {
                    window.dispatchEvent(new CustomEvent('va-select', {
                        detail: { kind: this.kind, item: null },
                    }));
                    this.close();
                },

                async uploadTwin() {
                    if (denyDemoAction() || this.kind !== 'avatar' || this.uploadingTwin) {
                        return;
                    }

                    const file = this.twinFile;

                    if (!file || !['video/mp4', 'video/webm'].includes(file.type) || file.size > 32 * 1024 * 1024) {
                        this.twinError = config.messages.twinFileError;

                        return;
                    }

                    if (!this.twinName.trim()) {
                        this.twinError = config.messages.twinCreateError;

                        return;
                    }

                    this.uploadingTwin = true;
                    this.twinError = '';

                    try {
                        const form = new FormData();
                        form.append('file', file);
                        const assetResponse = await fetch(config.routes.assets, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': config.csrf,
                            },
                            body: form,
                        });
                        const asset = await assetResponse.json();

                        if (!assetResponse.ok || !asset.asset_id) {
                            throw new Error(config.messages.uploadFailed);
                        }

                        const twinResponse = await fetch(config.routes.createTwin, {
                            method: 'POST',
                            headers: jsonHeaders,
                            body: JSON.stringify({ name: this.twinName.trim(), asset_id: asset.asset_id }),
                        });
                        const twin = await twinResponse.json();

                        if (!twinResponse.ok || !twin.avatar) {
                            throw new Error(config.messages.twinCreateError);
                        }

                        this.showTwinForm = false;
                        this.twinName = '';
                        this.twinFile = null;
                        this.$refs.twinFileInput.value = '';
                        this.filters.ownership = 'private';
                        this.filters.avatar_type = 'digital_twin';
                        await this.reload();
                    } catch (error) {
                        this.twinError = error.message || config.messages.twinCreateError;
                    } finally {
                        this.uploadingTwin = false;
                    }
                },

                async openConsent(avatar) {
                    if (denyDemoAction() || this.kind !== 'avatar' || !avatar.needs_consent) {
                        return;
                    }

                    const tab = window.open('', '_blank');

                    if (!tab) {
                        this.twinError = config.messages.consentError;

                        return;
                    }

                    if (this.consentUrls[avatar.id]) {
                        tab.location.href = this.consentUrls[avatar.id];

                        return;
                    }

                    try {
                        const response = await fetch(config.routes.avatarConsent.replace('__ID__', avatar.refresh_id), {
                            method: 'POST',
                            headers: jsonHeaders,
                        });
                        const data = await response.json();

                        if (!response.ok || !data.url) {
                            throw new Error(config.messages.consentError);
                        }

                        this.consentUrls[avatar.id] = data.url;
                        tab.location.href = data.url;
                    } catch (error) {
                        tab.close();
                        this.twinError = config.messages.consentError;
                    }
                },

                async refreshPending() {
                    if (this.demo || this.kind !== 'avatar' || this.refreshingPending) {
                        return;
                    }

                    const pending = this.items.filter((item) => item.refresh_id && !item.ready && !item.failed);

                    if (!pending.length) {
                        return;
                    }

                    this.refreshingPending = true;

                    await Promise.all(pending.map(async (item) => {
                        try {
                            const response = await fetch(config.routes.avatarRefresh.replace('__ID__', item.refresh_id), {
                                method: 'POST',
                                headers: jsonHeaders,
                            });
                            const data = await response.json();

                            if (response.ok && data.avatar) {
                                const index = this.items.findIndex((row) => row.id === item.id);

                                if (index !== -1) {
                                    this.items[index] = data.avatar;
                                }
                            }
                        } catch (error) {
                            // The next refresh can recover from a transient network error.
                        }
                    }));
                    this.refreshingPending = false;
                },

                /** One shared <audio> element, so two previews never overlap. */
                togglePreview(item) {
                    const audio = this.$refs.preview;

                    if (!audio || !item.preview) {
                        return;
                    }

                    if (this.playingId === item.id) {
                        audio.pause();
                        this.playingId = null;

                        return;
                    }

                    audio.src = item.preview;
                    audio.play();
                    this.playingId = item.id;
                },

                /** Hover-to-play video previews on avatar and Look Pack cards. */
                hoverIn(event) {
                    const video = event.currentTarget.querySelector('video');

                    if (video) {
                        video.play().catch(() => {});
                    }
                },

                hoverOut(event) {
                    const video = event.currentTarget.querySelector('video');

                    if (video) {
                        video.pause();
                        video.currentTime = 0;
                    }
                },
            }));

            /**
             * Brand kits and glossaries. Its endpoint returns two lists rather
             * than a paginated one, so it doesn't reuse the picker factory.
             */
            /**
             * HeyGen has no script endpoint, so this calls MagicAI's own word
             * model and charges the user's AI credits like every other AI tool.
             */
            Alpine.data('videoAgentScriptWriter', () => ({
                topic: '',
                generating: false,
                error: '',
                demo: config.demo,
                targetLength: '',

                init() {
                    window.addEventListener('va-open', (event) => {
                        if (event.detail === 'script') {
                            this.open();
                        }
                    });
                },

                open() {
                    const modal = modalOf('va-script-modal');

                    if (modal) {
                        modal.modalOpen = true;
                    }

                    this.error = '';
                    this.readTargetLength();
                    this.$nextTick(() => this.$refs.topic && this.$refs.topic.focus());
                },

                close() {
                    const modal = modalOf('va-script-modal');

                    if (modal) {
                        modal.modalOpen = false;
                    }
                },

                get canGenerate() {
                    return !this.generating && !this.demo && this.topic.trim().length > 0;
                },

                /**
                 * The composer's duration travels with the request, so the
                 * modal names it. The label is read off the select rather than
                 * reformatted from the seconds, so the wording always matches
                 * the pill the user set and needs no second translation.
                 *
                 * Captured on open instead of computed: a getter reading the
                 * DOM has nothing for Alpine to track, so it would go stale.
                 */
                readTargetLength() {
                    const select = document.querySelector('.lqd-va-composer select[name="duration"]');

                    this.targetLength = select && select.value
                        ? select.options[select.selectedIndex].text
                        : '';
                },

                async generate() {
                    if (!this.canGenerate) {
                        return;
                    }

                    this.generating = true;
                    this.error = '';

                    try {
                        const composer = document.querySelector('.lqd-va-composer');
                        const response = await fetch(config.routes.script, {
                            method: 'POST',
                            headers: jsonHeaders,
                            body: JSON.stringify({
                                topic: this.topic.trim(),
                                // Reuse whatever length the composer is set to
                                // so the draft is written to fit it.
                                duration: composer ? (Alpine.$data(composer).duration || null) : null,
                            }),
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            this.error = data.message
                                || Object.values(data.errors || {}).flat()[0]
                                || config.messages.loadFailed;

                            return;
                        }

                        window.dispatchEvent(new CustomEvent('va-script', { detail: data.script }));
                        this.topic = '';
                        this.close();
                    } catch (error) {
                        this.error = config.messages.loadFailed;
                    } finally {
                        this.generating = false;
                    }
                },
            }));

            Alpine.data('videoAgentBrand', () => ({
                demo: config.demo,
                kits: [],
                glossaries: [],
                configured: true,
                loading: false,
                saving: false,
                loaded: false,
                error: '',
                form: { url: '', name: '' },
                renaming: null,
                renameValue: '',
                savingGlossary: false,
                glossaryError: '',
                glossaryForm: { name: '', terms: [{ term: '', pronunciation: '' }] },

                // A glossary is worth nothing without at least one usable pair,
                // so the button stays off until a name and one full row exist.
                get canSaveGlossary() {
                    return this.glossaryForm.name.trim() !== ''
                        && this.glossaryForm.terms.some((term) => term.term.trim() && term.pronunciation.trim());
                },

                init() {
                    window.addEventListener('va-open', (event) => {
                        if (event.detail === 'brand') {
                            this.open();
                        }
                    });
                },

                open() {
                    const modal = modalOf('va-brand-modal');

                    if (modal) {
                        modal.modalOpen = true;
                    }

                    if (!this.loaded && !this.loading) {
                        this.load();
                    }
                },

                close() {
                    const modal = modalOf('va-brand-modal');

                    if (modal) {
                        modal.modalOpen = false;
                    }
                },

                apply(data) {
                    this.kits = data.kits || [];
                    this.glossaries = data.glossaries || [];
                    this.configured = data.configured !== false;
                    this.loaded = true;
                },

                async load(url = null, options = {}) {
                    this.loading = true;

                    try {
                        const response = await fetch(url || config.routes.brand, Object.assign({
                            headers: jsonHeaders,
                        }, options));
                        const data = await response.json();

                        if (!response.ok) {
                            // 422 carries a field-level reason worth keeping on
                            // screen next to the input that caused it.
                            this.error = data.message
                                || Object.values(data.errors || {}).flat()[0]
                                || config.messages.loadFailed;
                            notifyError(this.error);

                            return false;
                        }

                        this.error = '';
                        this.apply(data);

                        return true;
                    } catch (error) {
                        notifyError();

                        return false;
                    } finally {
                        this.loading = false;
                    }
                },

                refresh() {
                    if (denyDemoAction()) {
                        return false;
                    }

                    return this.load(config.routes.brandRefresh, { method: 'POST' });
                },

                async createKit() {
                    if (denyDemoAction() || this.saving || !this.form.url.trim()) {
                        return;
                    }

                    this.saving = true;
                    this.error = '';

                    // Validation failures carry the reason HeyGen or the form
                    // request gave; showing it beats a generic toast when the
                    // URL is unreachable.
                    const ok = await this.load(config.routes.brandKits, {
                        method: 'POST',
                        body: JSON.stringify(this.form),
                    });

                    if (ok) {
                        this.form.url = '';
                        this.form.name = '';
                    }

                    this.saving = false;
                },

                // Rename and delete share one URI and differ only by verb.
                // Both are gated on `owned`: one HeyGen workspace backs every
                // user of this app, so a kit someone else built is read-only.
                kitUrl(kit) {
                    return config.routes.brandKit.replace('__ID__', kit.id);
                },

                renameKit(kit) {
                    if (denyDemoAction()) {
                        return;
                    }

                    this.renaming = kit.id;
                    this.renameValue = kit.name;
                },

                async saveRename(kit) {
                    if (denyDemoAction()) {
                        return;
                    }

                    const name = this.renameValue.trim();

                    if (this.saving || !name || name === kit.name) {
                        this.renaming = null;

                        return;
                    }

                    this.saving = true;

                    if (await this.load(this.kitUrl(kit), { method: 'PATCH', body: JSON.stringify({ name }) })) {
                        this.renaming = null;
                    }

                    this.saving = false;
                },

                async deleteKit(kit) {
                    if (denyDemoAction() || this.saving || !window.confirm(config.messages.confirmDeleteKit)) {
                        return;
                    }

                    this.saving = true;
                    await this.load(this.kitUrl(kit), { method: 'DELETE' });
                    this.saving = false;
                },

                addTerm() {
                    this.glossaryForm.terms.push({ term: '', pronunciation: '' });
                },

                removeTerm(index) {
                    if (this.glossaryForm.terms.length > 1) {
                        this.glossaryForm.terms.splice(index, 1);
                    }
                },

                async createGlossary() {
                    if (denyDemoAction() || this.savingGlossary || !this.canSaveGlossary) {
                        return;
                    }

                    // A row with one side filled is a typo, not an empty row:
                    // say so rather than dropping the half the user wrote.
                    const terms = this.glossaryForm.terms
                        .filter((term) => term.term.trim() || term.pronunciation.trim());

                    if (terms.some((term) => !term.term.trim() || !term.pronunciation.trim())) {
                        this.glossaryError = config.messages.glossaryIncomplete;

                        return;
                    }

                    this.savingGlossary = true;
                    this.glossaryError = '';

                    const ok = await this.load(config.routes.brandGlossaries, {
                        method: 'POST',
                        body: JSON.stringify({ name: this.glossaryForm.name.trim(), terms }),
                    });

                    if (ok) {
                        this.glossaryForm = { name: '', terms: [{ term: '', pronunciation: '' }] };
                    } else {
                        this.glossaryError = this.error;
                    }

                    this.savingGlossary = false;
                },

                choose(kit) {
                    window.dispatchEvent(new CustomEvent('va-select', {
                        detail: { kind: 'brand', item: { id: kit.heygen_id, name: kit.name, logo: kit.logo } },
                    }));
                    this.close();
                },

                clear() {
                    window.dispatchEvent(new CustomEvent('va-select', { detail: { kind: 'brand', item: null } }));
                    this.close();
                },
            }));

            /**
             * The composer: prompt, attachments, and the four selection pills.
             * Selections are mirrored into hidden inputs so the form still POSTs
             * the same fields StoreVideoAgentSessionRequest already validates.
             */
            Alpine.data('videoAgentComposer', (initial) => ({
                prompt: initial.prompt || '',
                mode: initial.mode || '',
                orientation: initial.orientation || '',
                duration: initial.duration || '',
                instructions: initial.instructions || '',
                instructionsOpen: !!(initial.instructions || '').trim(),
                selection: Object.assign({ avatar: null, voice: null, style: null, brand: null }, initial.selection || {}),
                assets: [],
                uploading: false,
                submitting: false,
                uploadError: '',
                demo: config.demo,

                init() {
                    this.$nextTick(() => this.autogrow());

                    window.addEventListener('va-select', (event) => this.applySelection(event.detail));
                    window.addEventListener('va-request-upload', () => {
                        if (!denyDemoAction()) {
                            this.$refs.file && this.$refs.file.click();
                        }
                    });

                    // Script Writer drafts the prompt for you, so it replaces
                    // the textarea outright rather than appending.
                    window.addEventListener('va-script', (event) => {
                        this.prompt = event.detail;
                        this.$nextTick(() => {
                            this.autogrow();
                            this.$refs.prompt && this.$refs.prompt.focus();
                        });
                    });
                },

                toggleInstructions() {
                    this.instructionsOpen = !this.instructionsOpen;

                    if (this.instructionsOpen) {
                        this.$nextTick(() => this.$refs.instructions && this.$refs.instructions.focus());
                    }
                },

                get canSubmit() {
                    return !this.submitting && !this.uploading && !this.demo && this.prompt.trim().length > 0;
                },

                get atFileLimit() {
                    return this.assets.length >= config.limits.maxFiles;
                },

                /**
                 * Each picker returns its own shape, and on a voice `preview`
                 * is the audio sample — it must never reach an <img>. Collapse
                 * every kind onto the one shape the pills render.
                 */
                applySelection({ kind, item }) {
                    this.selection[kind] = item
                        ? { id: item.id, name: item.name || item.id, thumbnail: this.thumbnailFor(kind, item) }
                        : null;

                    // HeyGen ships a default voice with most avatar looks; adopt
                    // it so the user doesn't have to pick twice.
                    if (kind === 'avatar' && item && item.voice_id && !this.selection.voice) {
                        this.selection.voice = {
                            id: item.voice_id,
                            name: config.messages.avatarVoice,
                            thumbnail: null,
                        };
                    }
                },

                /** Only ever an image URL, or null so the pill keeps its icon. */
                thumbnailFor(kind, item) {
                    switch (kind) {
                        case 'avatar':
                            return item.preview_image || null;
                        case 'style':
                            return item.thumbnail || null;
                        case 'brand':
                            return item.logo || null;
                        default:
                            return null;
                    }
                },

                openPicker(kind) {
                    window.dispatchEvent(new CustomEvent('va-open', { detail: kind }));
                },

                pillLabel(kind, fallback) {
                    return this.selection[kind] ? this.selection[kind].name : fallback;
                },

                autogrow() {
                    const el = this.$refs.prompt;

                    if (!el) {
                        return;
                    }

                    el.style.height = 'auto';
                    el.style.height = Math.min(el.scrollHeight, 320) + 'px';
                },

                submit(event) {
                    if (!this.canSubmit) {
                        event.preventDefault();

                        return;
                    }

                    this.submitting = true;
                },

                async uploadFiles(event) {
                    const files = Array.from(event.target.files || []);

                    event.target.value = '';

                    if (denyDemoAction() || !files.length) {
                        return;
                    }

                    this.uploading = true;
                    this.uploadError = '';

                    for (const file of files) {
                        if (this.atFileLimit) {
                            break;
                        }

                        const body = new FormData();

                        body.append('file', file);

                        try {
                            const response = await fetch(config.routes.assets, {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': config.csrf, 'Accept': 'application/json' },
                                body,
                            });
                            const data = await response.json();

                            if (!response.ok) {
                                this.uploadError = data.message || config.messages.uploadFailed;

                                continue;
                            }

                            this.assets.push(data);
                        } catch (error) {
                            this.uploadError = config.messages.uploadFailed;
                        }
                    }

                    this.uploading = false;
                },

                /** Drop the remote asset too, not just the chip. */
                async removeAsset(asset) {
                    this.assets = this.assets.filter((item) => item.asset_id !== asset.asset_id);

                    if (!asset.id) {
                        return;
                    }

                    try {
                        await fetch(`${config.routes.assets}/${asset.id}`, {
                            method: 'DELETE',
                            headers: jsonHeaders,
                        });
                    } catch (error) {
                        // The chip is already gone; a stale remote asset is harmless.
                    }
                },
            }));

            /**
             * The landing page: keeps the recent list fresh without a refresh.
             * `video-agent.check` has existed all along but nothing called it.
             */
            Alpine.data('videoAgentRecent', (initial) => ({
                sessions: initial.sessions || [],
                usage: initial.usage || {},
                timer: null,

                init() {
                    this.timer = setInterval(() => this.poll(), config.poll);
                },

                destroy() {
                    clearInterval(this.timer);
                },

                get hasPending() {
                    return this.sessions.some((session) => !session.terminal);
                },

                async poll() {
                    if (!this.hasPending || document.hidden) {
                        return;
                    }

                    try {
                        const response = await fetch(config.routes.check, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        });

                        if (!response.ok) {
                            return;
                        }

                        const data = await response.json();

                        this.sessions = data.sessions || [];
                        this.usage = data.usage || this.usage;
                    } catch (error) {
                        // Transient network error: the next tick retries.
                    }
                },
            }));

            /**
             * The session thread. HeyGen owns the agent loop, so there is no
             * token stream to relay — polling is the correct transport here.
             */
            Alpine.data('videoAgentThread', (initial) => ({
                uuid: initial.payload.uuid,
                title: initial.payload.title,
                status: initial.payload.status,
                label: initial.payload.label,
                variant: initial.payload.variant,
                needsUser: initial.payload.needs_user,
                canApprove: initial.payload.can_approve,
                terminal: initial.payload.terminal,
                error: initial.payload.error,
                outputPending: initial.payload.output_pending,
                messages: initial.payload.messages,
                scenes: initial.payload.scenes,
                videos: initial.payload.videos,
                sessions: initial.sessions || [],
                urls: initial.urls,
                message: '',
                busy: false,
                timer: null,
                demo: config.demo,

                init() {
                    this.$nextTick(() => {
                        this.autogrow();
                        this.scrollToEnd();
                    });
                    this.start();
                },

                destroy() {
                    clearInterval(this.timer);
                    this.timer = null;
                },

                start() {
                    clearInterval(this.timer);
                    this.timer = null;

                    if (!this.shouldPoll) {
                        return;
                    }

                    this.timer = setInterval(() => this.poll(), config.poll);
                },

                get shouldPoll() {
                    return !this.needsUser && (!this.terminal || (this.status === 'completed' && this.outputPending));
                },

                get working() {
                    return (!this.terminal && !this.needsUser) || (this.status === 'completed' && this.outputPending);
                },

                get workingLabel() {
                    return this.status === 'generating' || (this.status === 'completed' && this.outputPending)
                        ? {{ Js::from(__('Rendering your video…')) }}
                        : {{ Js::from(__('The agent is thinking…')) }};
                },

                get canSend() {
                    return !this.busy && !this.demo && !this.terminal && this.message.trim().length > 0;
                },

                apply(data) {
                    this.title = data.title ?? this.title;
                    this.status = data.status;
                    this.label = data.label;
                    this.variant = data.variant;
                    this.needsUser = data.needs_user;
                    this.canApprove = data.can_approve;
                    this.terminal = data.terminal;
                    this.error = data.error;
                    this.outputPending = data.output_pending;
                    this.messages = data.messages || [];
                    this.scenes = data.scenes || [];
                    this.videos = data.videos || [];

                    if (!this.shouldPoll) {
                        clearInterval(this.timer);
                        this.timer = null;
                    } else if (!this.timer) {
                        this.start();
                    }

                    this.$nextTick(() => this.scrollToEnd());
                },

                scrollToEnd() {
                    const el = this.$refs.thread;

                    if (el) {
                        el.scrollTop = el.scrollHeight;
                    }
                },

                autogrow() {
                    const el = this.$refs.reply;

                    if (!el) {
                        return;
                    }

                    el.style.height = 'auto';
                    el.style.height = Math.min(el.scrollHeight, 200) + 'px';
                },

                async poll() {
                    if (document.hidden) {
                        return;
                    }

                    try {
                        const response = await fetch(this.urls.check, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        });

                        if (!response.ok) {
                            return;
                        }

                        this.apply(await response.json());
                    } catch (error) {
                        // Transient network error: the next tick retries.
                    }
                },

                async send() {
                    if (!this.canSend) {
                        return;
                    }

                    const body = this.message.trim();

                    // Optimistic echo so the thread doesn't sit empty while
                    // HeyGen thinks about it.
                    this.messages.push({ id: 'pending-' + Date.now(), role: 'user', kind: 'message', body, at: null });
                    this.message = '';
                    this.$nextTick(() => {
                        this.autogrow();
                        this.scrollToEnd();
                    });

                    await this.post(this.urls.message, { message: body });
                },

                async approve() {
                    await this.post(this.urls.approve, {});
                },

                async stop() {
                    if (!confirm('{{ __('Stop this session?') }}')) {
                        return;
                    }

                    await this.post(this.urls.stop, {});
                },

                async post(url, payload) {
                    this.busy = true;

                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: jsonHeaders,
                            body: JSON.stringify(payload),
                        });
                        const data = await response.json();

                        if (!response.ok) {
                            notifyError(data.error || data.message);
                        }

                        if (data.status) {
                            this.apply(data);
                        }

                        if (response.ok && data.message) {
                            window.toastr && toastr.success(data.message);
                        }
                    } catch (error) {
                        notifyError();
                    } finally {
                        this.busy = false;
                    }
                },

                async remove(session) {
                    if (!confirm(config.messages.confirmDelete)) {
                        return;
                    }

                    try {
                        await fetch(session.url, { method: 'DELETE', headers: jsonHeaders });

                        if (session.uuid === this.uuid) {
                            window.location.href = config.routes.index;

                            return;
                        }

                        this.sessions = this.sessions.filter((item) => item.uuid !== session.uuid);
                    } catch (error) {
                        notifyError();
                    }
                },
            }));
        });
    </script>
@endPushOnce
