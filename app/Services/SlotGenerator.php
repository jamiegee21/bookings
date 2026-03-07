<?php

namespace App\Services;

use App\Enums\DayOfWeek;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SlotGenerator
{
    private const SLOT_INTERVAL_MINUTES = 30;

    /**
     * Get available 30-minute slot start times for a staff member on a date.
     * Excludes lunch break and already-booked times. Last slot ends at member's end_time.
     *
     * @return Collection<int, Carbon>
     */
    public function availableSlots(User $user, Carbon $date): Collection
    {
        $schedule = $user->schedules()
            ->where('day_of_week', $this->carbonToDayOfWeek($date))
            ->first();

        if (! $schedule) {
            return collect();
        }

        $start = $date->copy()->setTimeFromTimeString($schedule->start_time);
        $end = $date->copy()->setTimeFromTimeString($schedule->end_time);
        $lunchStart = $schedule->lunch_start
            ? $date->copy()->setTimeFromTimeString($schedule->lunch_start)
            : null;
        $lunchEnd = $schedule->lunch_end
            ? $date->copy()->setTimeFromTimeString($schedule->lunch_end)
            : null;

        $slots = collect();
        $slotStart = $start->copy();

        while ($slotStart->copy()->addMinutes(self::SLOT_INTERVAL_MINUTES)->lte($end)) {
            $slotEnd = $slotStart->copy()->addMinutes(self::SLOT_INTERVAL_MINUTES);

            $inLunch = $lunchStart && $lunchEnd
                && $slotStart->lt($lunchEnd)
                && $slotEnd->gt($lunchStart);

            if (! $inLunch) {
                $slots->push($slotStart->copy());
            }

            $slotStart->addMinutes(self::SLOT_INTERVAL_MINUTES);
        }

        $bookedRanges = $user->bookings()
            ->whereDate('starts_at', $date)
            ->get(['starts_at', 'ends_at']);

        return $slots->filter(function (Carbon $slot) use ($bookedRanges) {
            $slotEnd = $slot->copy()->addMinutes(self::SLOT_INTERVAL_MINUTES);
            foreach ($bookedRanges as $booking) {
                $bStart = Carbon::parse($booking->starts_at);
                $bEnd = Carbon::parse($booking->ends_at);
                if ($slot->lt($bEnd) && $slotEnd->gt($bStart)) {
                    return false;
                }
            }
            return true;
        })->values();
    }

    private function carbonToDayOfWeek(Carbon $date): DayOfWeek
    {
        $iso = (int) $date->format('N');
        return DayOfWeek::from($iso);
    }
}
