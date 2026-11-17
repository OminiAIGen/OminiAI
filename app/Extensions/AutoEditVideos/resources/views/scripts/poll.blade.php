@if ($pending->isNotEmpty())
    @push('script')
        <script>
            // Rendley publishes no webhooks, so open edits are polled and the
            // server-rendered row is swapped in — the browser never has to know
            // how a status badge is built.
            (function () {
                const checkUrl = {{ Illuminate\Support\Js::from(route('dashboard.user.auto-edit-videos.check')) }};
                let pending = {{ Illuminate\Support\Js::from($pending) }};
                let timer = null;

                async function poll() {
                    if (pending.length === 0) {
                        clearInterval(timer);

                        return;
                    }

                    try {
                        const query = pending.map((uuid) => 'uuids[]=' + encodeURIComponent(uuid)).join('&');
                        const response = await fetch(checkUrl + '?' + query, {
                            headers: { 'Accept': 'application/json' },
                        });

                        if (!response.ok) {
                            return;
                        }

                        const payload = await response.json();

                        payload.data.forEach((item) => {
                            const element = document.getElementById(item.divId);

                            if (element) {
                                element.innerHTML = item.html;

                                if (window.Alpine) {
                                    window.Alpine.initTree(element);
                                }
                            }

                            if (item.done) {
                                pending = pending.filter((uuid) => !item.divId.endsWith(uuid));
                            }
                        });
                    } catch (error) {
                        // A transient network error must not stop the poll loop.
                    }
                }

                timer = setInterval(poll, 5000);
            })();
        </script>
    @endpush
@endif
