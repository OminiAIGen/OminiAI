<?php

declare(strict_types=1);

namespace App\Extensions\PhoneCallAgent\System\Booking\Contracts;

interface BookingProviderInterface
{
    /**
     * List open slots between two UTC instants. `$timezone` is the business calendar's IANA zone;
     * providers that can localize their response should return slots carrying that offset.
     *
     * @return array<int, string>
     */
    public function checkAvailability(string $startDate, string $endDate, string $timezone = 'UTC'): array;

    public function createBooking(string $slot, string $name, string $email, string $timezone, ?string $phone = null): array;

    public function cancelBooking(string $bookingUid, ?string $reason = null): bool;

    public function rescheduleBooking(string $bookingUid, string $newSlot): array;
}
