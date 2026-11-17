<?php

declare(strict_types=1);

namespace App\Extensions\PhoneCallAgent\System\Booking;

use App\Extensions\PhoneCallAgent\System\Models\ExtPhoneCallAgent;

/**
 * The booking guidance appended to an agent's own instructions. Both providers share one text; they
 * differ only in how the model is told what "now" is, because the Twilio relay rebuilds its system
 * prompt on every call while the ElevenLabs prompt is stored remotely and only rewritten on save.
 */
class BookingPromptComposer
{
    /**
     * Booking is usable only when enabled AND a provider + event type are configured. The capability
     * blurb and the tool wiring must agree, or the model would be told it can book while having no
     * tools — producing fabricated confirmations.
     */
    public function isReady(ExtPhoneCallAgent $agent): bool
    {
        return (bool) ($agent->booking_enabled && $agent->booking_provider && $agent->booking_event_type_id);
    }

    /**
     * The business calendar's timezone: the one every slot is quoted and booked in.
     */
    public function timezone(ExtPhoneCallAgent $agent): string
    {
        foreach ([$agent->booking_timezone, config('app.timezone')] as $candidate) {
            $candidate = trim((string) $candidate);

            if ($candidate !== '' && in_array($candidate, timezone_identifiers_list(), true)) {
                return $candidate;
            }
        }

        return 'UTC';
    }

    /**
     * Twilio ConversationRelay rebuilds the system prompt for every call, so a literal date is live.
     */
    public function appendForRelay(string $instructions, ExtPhoneCallAgent $agent): string
    {
        $timezone = $this->timezone($agent);

        return $this->append(
            $instructions,
            $agent,
            'Today is ' . now($timezone)->format('l, Y-m-d') . " in {$timezone}.",
        );
    }

    /**
     * The ElevenLabs prompt lives on their side and is only rewritten when the agent is saved, so a
     * literal date would be stale by tomorrow. `{{system__time_utc}}` is a built-in ElevenLabs system
     * dynamic variable, substituted per conversation with nothing to declare or pass at runtime.
     */
    public function appendForElevenLabs(string $instructions, ExtPhoneCallAgent $agent): string
    {
        return $this->append(
            $instructions,
            $agent,
            'The current UTC time is {{system__time_utc}}. Convert it to '
                . $this->timezone($agent) . ' before resolving any relative date.',
        );
    }

    private function append(string $instructions, ExtPhoneCallAgent $agent, string $clock): string
    {
        if (! $this->isReady($agent)) {
            return $instructions;
        }

        return trim($instructions) . "\n\n" . $this->section($agent, $clock);
    }

    private function section(ExtPhoneCallAgent $agent, string $clock): string
    {
        $timezone = $this->timezone($agent);

        return "# Booking Capability\n"
            . "You can check availability and book appointments for the caller. {$clock} Use it to resolve relative or spoken dates and to pick the correct year.\n\n"
            . "The business operates in {$timezone}. check_availability returns times already converted to {$timezone} — quote them to the caller exactly as returned and never do your own timezone maths.\n\n"
            . "This is a spoken phone conversation. The caller will say dates and times in everyday language (for example \"next Tuesday\", \"January 24th\", \"tomorrow afternoon\"). NEVER ask the caller to say or spell a date in YYYY-MM-DD or any digit-by-digit format, and never read date or time formats aloud. Silently interpret what they say, convert it to the YYYY-MM-DD format yourself, and pass that to the check_availability and create_booking tools. If a spoken date is genuinely ambiguous, ask one short, natural clarifying question (e.g. \"Do you mean this coming Tuesday?\") — never mention formats.\n\n"
            . "The ONLY information required to make a booking is: a specific available time slot, the caller's full name, their email address, and their timezone. Do NOT ask for anything else — no service type, no duration, no meeting type, no reason for the appointment, and no phone number unless the caller volunteers it. Never invent extra required fields.\n\n"
            . "Booking flow — follow these steps and actually call the tools; never just say you will book without calling create_booking:\n"
            . "1. Work out the caller's preferred date and call check_availability.\n"
            . '2. Read the returned slots back in plain spoken time (e.g. "nine in the morning"), never an ISO timestamp or a timezone name. When the caller picks one, pass the exact value shown in square brackets for that slot as the `slot` argument — do not retype or reformat it.' . "\n"
            . "3. Collect the caller's full name, email address, and timezone — only the ones you don't already have.\n"
            . "4. Confirm the chosen time, name, and email back in plain English ONCE, then immediately call create_booking. Do not keep re-confirming or asking for more details.\n"
            . 'After create_booking succeeds, read the booked date and time back in plain English. When the caller wants to cancel or reschedule, call find_bookings first — it matches the number they are calling from; if nothing is found, ask for the email address used when booking and call it again with that email. Confirm which booking they mean, then call cancel_booking or reschedule_booking with that booking\'s UID. Never ask the caller for a booking reference or UID.';
    }
}
