<?php

return [
    'version' => 1.0,

    'outbound' => [
        /*
         * A row claimed as "queued" that never reached the provider (no started_at) is stranded:
         * the worker died before dialling, so nothing will ever finalize it. Fail it after this many
         * minutes so it stops showing as "In Progress" forever.
         */
        'stuck_undialed_minutes' => 15,

        /*
         * A row that did dial but never received a provider status callback. Deliberately long so a
         * genuinely live call is never killed mid-conversation.
         */
        'stuck_dialed_minutes' => 60,
    ],
];
