<?php

declare(strict_types=1);

namespace App\Extensions\PhoneCallAgent\System\Booking;

use App\Extensions\PhoneCallAgent\System\Booking\Contracts\BookingProviderInterface;
use App\Extensions\PhoneCallAgent\System\Models\ExtPhoneCallAgent;
use App\Extensions\PhoneCallAgent\System\Models\ExtPhoneCallAgentBooking;
use App\Extensions\PhoneCallAgent\System\Services\FollowUpService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class BookingToolHandler
{
    /**
     * A week of a 30-minute event type is hundreds of slots; the model only needs a handful to
     * offer, and dumping every one of them floods the conversation context.
     */
    private const MAX_SLOTS_RETURNED = 12;

    private ?string $timezone = null;

    public function __construct(
        private readonly BookingProviderInterface $provider,
        private readonly ?ExtPhoneCallAgent $agent = null,
        private readonly ?string $callerNumber = null,
    ) {}

    public function handle(string $toolName, array $input): string
    {
        Log::info('[BookingTool] handle', ['tool' => $toolName, 'input' => $input]);

        try {
            $result = match ($toolName) {
                'check_availability' => $this->handleCheckAvailability($input),
                'create_booking'     => $this->handleCreateBooking($input),
                'find_bookings'      => $this->handleFindBookings($input),
                'cancel_booking'     => $this->handleCancelBooking($input),
                'reschedule_booking' => $this->handleReschedule($input),
                default              => 'Unknown booking tool: ' . $toolName,
            };

            Log::info('[BookingTool] result', ['tool' => $toolName, 'result' => $result]);

            return $result;
        } catch (Throwable $e) {
            Log::error('[BookingTool] exception', ['tool' => $toolName, 'error' => $e->getMessage()]);

            return 'Booking action failed: ' . $e->getMessage();
        }
    }

    private function handleCheckAvailability(array $input): string
    {
        $date = trim((string) ($input['date'] ?? ''));

        if ($date === '') {
            return 'Please provide a date to check availability.';
        }

        $timezone = $this->timezone();

        try {
            $parsed = Carbon::parse($date, $timezone);
        } catch (Throwable) {
            return "Could not understand the date \"{$date}\". Ask the caller for the date again.";
        }

        // The caller's date is a business-local day, but both providers want UTC bounds.
        $start = $parsed->isToday() ? Carbon::now($timezone) : $parsed->copy()->startOfDay();
        $end = $parsed->copy()->addDays(6)->endOfDay();

        $slots = $this->provider->checkAvailability(
            $start->copy()->utc()->toIso8601String(),
            $end->copy()->utc()->toIso8601String(),
            $timezone,
        );

        if (empty($slots)) {
            return 'No available slots in the week starting ' . $parsed->format('l, j F Y') . " ({$timezone}).";
        }

        return $this->formatSlots($slots, $timezone);
    }

    /**
     * Render slots as spoken-friendly local times grouped by day, each carrying the exact
     * offset-bearing value create_booking expects. The model reads the plain time aloud and copies
     * the bracketed value verbatim, so it never has to reconstruct an ISO timestamp itself — doing
     * that is what produced instants the calendar then rejected.
     *
     * @param  array<int, string>  $slots
     */
    private function formatSlots(array $slots, string $timezone): string
    {
        $lines = ["All times below are in {$timezone}, the business timezone."];
        $currentDay = null;
        $shown = 0;

        foreach ($slots as $slot) {
            if ($shown >= self::MAX_SLOTS_RETURNED) {
                break;
            }

            try {
                $local = Carbon::parse($slot)->setTimezone($timezone);
            } catch (Throwable) {
                continue;
            }

            $day = $local->format('l, j F Y');

            if ($day !== $currentDay) {
                $currentDay = $day;
                $lines[] = $day . ':';
            }

            $shown++;
            $lines[] = sprintf('  %d. %s  [%s]', $shown, $local->format('g:i A'), $local->toIso8601String());
        }

        if ($shown === 0) {
            return 'No available slots could be read from the calendar. Offer to try another date.';
        }

        $remaining = count($slots) - $shown;

        if ($remaining > 0) {
            $lines[] = "(+{$remaining} more later in the week — ask the caller if none of these work.)";
        }

        $lines[] = 'Read only the plain times aloud (e.g. "nine in the morning"). To book, pass the '
            . 'exact value in square brackets as the `slot` argument to create_booking.';

        return implode("\n", $lines);
    }

    /**
     * The business calendar's timezone — the one every slot is quoted and booked in.
     */
    private function timezone(): string
    {
        return $this->timezone ??= $this->safeTimezone($this->agent?->booking_timezone)
            ?? $this->safeTimezone(config('app.timezone'))
            ?? 'UTC';
    }

    /**
     * Gate every timezone that reaches Carbon or a provider on the IANA list, so a stale DB value or
     * something the model invented ("EST", "GMT+3") can never throw or 400 the calendar.
     */
    private function safeTimezone(?string $timezone): ?string
    {
        $timezone = trim((string) $timezone);

        return $timezone !== '' && in_array($timezone, timezone_identifiers_list(), true) ? $timezone : null;
    }

    /**
     * Resolve whatever the model sent into an absolute UTC instant.
     *
     * A value copied from check_availability carries an offset and is already unambiguous. A value
     * the model reconstructed ("2026-06-03T09:00:00") is a *local* wall-clock time in the business
     * timezone — parsing it as UTC is what shifted bookings and made the calendar reject them.
     */
    private function normalizeSlot(string $slot): ?string
    {
        $slot = trim($slot);

        // A bare date names no time of day, so it is not a bookable slot.
        if (! preg_match('/\d{1,2}:\d{2}/', $slot)) {
            return null;
        }

        $hasOffset = (bool) preg_match('/(Z|[+\-]\d{2}:?\d{2})$/', $slot);

        try {
            $carbon = $hasOffset ? Carbon::parse($slot) : Carbon::parse($slot, $this->timezone());
        } catch (Throwable) {
            return null;
        }

        return $carbon->utc()->format('Y-m-d\TH:i:s\Z');
    }

    private function handleCreateBooking(array $input): string
    {
        $slot = $input['slot'] ?? '';
        $name = $input['name'] ?? '';
        $email = $input['email'] ?? '';

        if ($slot === '' || $email === '' || $name === '') {
            $missing = implode(', ', array_filter([
                $slot === '' ? 'slot' : null,
                $name === '' ? 'name' : null,
                $email === '' ? 'email' : null,
            ]));

            return "Cannot create booking. Missing required fields: {$missing}. Please collect these from the caller first.";
        }

        $normalized = $this->normalizeSlot($slot);

        if ($normalized === null) {
            return "Could not understand the time \"{$slot}\". Call check_availability again and use "
                . 'one of the exact bracketed values it returns.';
        }

        // The caller's timezone is only their display preference — it must never reinterpret a slot
        // that was already offered in the business timezone. An invalid one falls back to the
        // business zone rather than UTC, because calendars reject an unknown attendee timezone.
        $attendeeTimezone = $this->safeTimezone($input['timezone'] ?? null) ?? $this->timezone();
        $phone = $input['phone'] ?? $this->callerNumber;

        $data = $this->provider->createBooking(
            slot: $normalized,
            name: $name,
            email: $email,
            timezone: $attendeeTimezone,
            phone: $phone,
        );

        $uid = $data['uid'] ?? $data['id'] ?? null;

        if ($uid) {
            $this->persistBooking((string) $uid, $normalized, $name, $email, $attendeeTimezone, $phone, $data);

            $booked = $this->parseDate($data['start'] ?? $normalized)?->setTimezone($this->timezone());

            return sprintf(
                'Booking confirmed for %s (%s). UID: %s. A confirmation has been sent to %s.',
                $booked?->format('l, j F Y \a\t g:i A') ?? $normalized,
                $this->timezone(),
                $uid,
                $email,
            );
        }

        return $this->bookingFailure($normalized, $data);
    }

    /**
     * Surface what the calendar actually said so the agent can tell the caller something actionable
     * instead of a generic retry. Bounded, and the model is told to paraphrase rather than read it.
     *
     * @param  array<string, mixed>  $data
     */
    private function bookingFailure(string $slot, array $data): string
    {
        Log::warning('[BookingTool] booking failed', ['slot' => $slot, 'response' => $data]);

        // The providers return the decoded error body on a 4xx, so the reason is in $data.
        $reason = $data['error']['message'] ?? $data['message'] ?? ($data['error'] ?? null);

        if (is_array($reason)) {
            $reason = json_encode($reason);
        }

        $reason = trim((string) $reason);

        if ($reason === '') {
            return 'Booking failed. The slot may have just been taken — offer the caller another time.';
        }

        return 'Booking failed. Reason from the calendar: ' . Str::limit($reason, 300)
            . ' Explain this to the caller in your own words and offer another time.';
    }

    /**
     * Look up the caller's upcoming bookings from the local record so the model can obtain a UID
     * for cancel/reschedule. Callers never know their booking UID, so without this lookup those
     * tools are unreachable in a phone conversation.
     */
    private function handleFindBookings(array $input): string
    {
        if (! $this->agent) {
            return 'Booking lookup is not available right now.';
        }

        $email = trim((string) ($input['email'] ?? ''));

        $query = ExtPhoneCallAgentBooking::query()
            ->where('agent_id', $this->agent->id)
            ->whereIn('status', ['confirmed', 'rescheduled'])
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at');

        if ($email !== '') {
            $query->where('customer_email', $email);
        } elseif ($this->callerNumber) {
            $query->where('customer_phone', $this->callerNumber);
        } else {
            return 'No phone number is available for this call. Ask the caller for the email address used when booking and call find_bookings again with it.';
        }

        $bookings = $query->limit(5)->get();

        if ($bookings->isEmpty()) {
            return $email !== ''
                ? "No upcoming bookings found for {$email}. Ask the caller to confirm the email address used when booking."
                : 'No upcoming bookings found for the number the caller is speaking from. Ask for the email address used when booking and call find_bookings again with it.';
        }

        $timezone = $this->timezone();

        $lines = $bookings->map(function (ExtPhoneCallAgentBooking $booking) use ($timezone): string {
            $when = $booking->starts_at?->copy()->setTimezone($timezone)->format('l, j F Y \a\t g:i A') ?? 'unknown time';

            return "- {$when} ({$timezone}) for {$booking->customer_name} [UID: {$booking->provider_uid}]";
        });

        return "Upcoming bookings:\n" . $lines->implode("\n")
            . "\nConfirm with the caller which booking they mean, then pass its UID to cancel_booking or reschedule_booking.";
    }

    private function handleCancelBooking(array $input): string
    {
        $bookingUid = $input['booking_uid'] ?? '';

        $success = $this->provider->cancelBooking(
            bookingUid: $bookingUid,
            reason: $input['reason'] ?? null,
        );

        if ($success) {
            $this->cancelBookingRecord((string) $bookingUid);
        }

        return $success
            ? 'Booking has been cancelled successfully.'
            : 'Failed to cancel the booking. Please verify the booking UID and try again.';
    }

    private function handleReschedule(array $input): string
    {
        $bookingUid = $input['booking_uid'] ?? '';
        $newSlot = $this->normalizeSlot((string) ($input['new_slot'] ?? ''));

        if ($newSlot === null) {
            return 'Could not understand the new time. Call check_availability again and use one of '
                . 'the exact bracketed values it returns.';
        }

        $data = $this->provider->rescheduleBooking(
            bookingUid: $bookingUid,
            newSlot: $newSlot,
        );

        $uid = $data['uid'] ?? $data['id'] ?? null;

        if ($uid) {
            $this->rescheduleBookingRecord((string) $bookingUid, (string) $uid, $newSlot, $data);

            return "Booking rescheduled successfully. New booking UID: {$uid}.";
        }

        return 'Booking has been rescheduled successfully.';
    }

    /**
     * Persist the booking locally and queue its follow-up calls. Failures here must not turn a successful
     * external booking into an error reported to the caller, so they are logged and swallowed.
     *
     * @param  array<string, mixed>  $data
     */
    private function persistBooking(string $uid, string $slot, string $name, string $email, string $timezone, ?string $phone, array $data): void
    {
        if (! $this->agent) {
            return;
        }

        try {
            $booking = ExtPhoneCallAgentBooking::create([
                'uuid'           => Str::uuid()->toString(),
                'agent_id'       => $this->agent->id,
                'user_id'        => $this->agent->user_id,
                'provider'       => $this->agent->booking_provider?->value,
                'provider_uid'   => $uid,
                'customer_name'  => $name,
                'customer_email' => $email,
                'customer_phone' => $phone,
                'starts_at'      => $this->parseDate($data['start'] ?? $slot),
                'ends_at'        => $this->parseDate($data['end'] ?? null),
                'timezone'       => $timezone,
                'status'         => 'confirmed',
            ]);

            app(FollowUpService::class)->scheduleForBooking($booking);
        } catch (Throwable $e) {
            Log::error('[BookingTool] persist failed', ['uid' => $uid, 'error' => $e->getMessage()]);
        }
    }

    private function cancelBookingRecord(string $uid): void
    {
        if (! $this->agent) {
            return;
        }

        try {
            $booking = $this->findBooking($uid);

            if (! $booking) {
                return;
            }

            $followUp = app(FollowUpService::class);
            $followUp->cancelForBooking($booking);
            $booking->update(['status' => 'cancelled']);
            $followUp->scheduleOnCancel($booking);
        } catch (Throwable $e) {
            Log::error('[BookingTool] cancel record failed', ['uid' => $uid, 'error' => $e->getMessage()]);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function rescheduleBookingRecord(string $oldUid, string $newUid, string $newSlot, array $data): void
    {
        if (! $this->agent) {
            return;
        }

        try {
            $booking = $this->findBooking($oldUid);

            if (! $booking) {
                return;
            }

            $followUp = app(FollowUpService::class);
            $followUp->cancelForBooking($booking);

            $booking->update([
                'provider_uid' => $newUid,
                'starts_at'    => $this->parseDate($data['start'] ?? $newSlot),
                'ends_at'      => $this->parseDate($data['end'] ?? null),
                'status'       => 'rescheduled',
            ]);

            $followUp->scheduleForBooking($booking->refresh());
        } catch (Throwable $e) {
            Log::error('[BookingTool] reschedule record failed', ['uid' => $oldUid, 'error' => $e->getMessage()]);
        }
    }

    private function findBooking(string $uid): ?ExtPhoneCallAgentBooking
    {
        return ExtPhoneCallAgentBooking::query()
            ->where('agent_id', $this->agent->id)
            ->where('provider_uid', $uid)
            ->latest('id')
            ->first();
    }

    private function parseDate(?string $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (Throwable) {
            return null;
        }
    }
}
