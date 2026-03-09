<?php

use App\Enums\DayOfWeek;
use App\Models\Booking;
use App\Models\User;
use App\Services\SlotGenerator;
use Carbon\Carbon;
use Livewire\Component;

new class extends Component {
    public ?int $teamMemberId = null;
    public string $selectedDate;
    public array $timeSlots = [];
    public bool $hasSchedule = false;

    public function mount(): void
    {
        $this->selectedDate = now()->format('Y-m-d');

        $firstTeamMember = User::whereHas('schedules')->first();
        if ($firstTeamMember) {
            $this->teamMemberId = $firstTeamMember->id;
            $this->loadSlots();
        }
    }

    public function loadTeamMembers(): array
    {
        return User::whereHas('schedules')->get()->toArray();
    }

    public function updatedTeamMemberId(): void
    {
        $this->loadSlots();
    }

    public function updatedSelectedDate(): void
    {
        $this->loadSlots();
    }

    public function loadSlots(): void
    {
        if (!$this->teamMemberId) {
            $this->timeSlots = [];
            $this->hasSchedule = false;
            return;
        }

        $user = User::find($this->teamMemberId);
        $date = Carbon::parse($this->selectedDate);

        $dayOfWeek = DayOfWeek::from((int)$date->format('N'));

        $schedule = $user->schedules()
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$schedule) {
            $this->timeSlots = [];
            $this->hasSchedule = false;
            return;
        }

        $this->hasSchedule = true;

        $start = $date->copy()->setTimeFromTimeString($schedule->start_time);
        $end = $date->copy()->setTimeFromTimeString($schedule->end_time);
        $lunchStart = $schedule->lunch_start
            ? $date->copy()->setTimeFromTimeString($schedule->lunch_start)
            : null;
        $lunchEnd = $schedule->lunch_end
            ? $date->copy()->setTimeFromTimeString($schedule->lunch_end)
            : null;

        $allSlots = collect();
        $slotStart = $start->copy();

        while ($slotStart->copy()->addMinutes(30)->lte($end)) {
            $slotEnd = $slotStart->copy()->addMinutes(30);

            $inLunch = $lunchStart && $lunchEnd
                && $slotStart->lt($lunchEnd)
                && $slotEnd->gt($lunchStart);

            if (!$inLunch) {
                $allSlots->push($slotStart->copy());
            }

            $slotStart->addMinutes(30);
        }

        $bookings = Booking::with(['user', 'service'])
            ->where('team_member_id', $this->teamMemberId)
            ->whereDate('starts_at', $date)
            ->where('status', 'confirmed')
            ->get();

        $this->timeSlots = $allSlots->map(function ($slot) use ($bookings) {
            $slotEnd = $slot->copy()->addMinutes(30);
            $booking = $bookings->first(function ($b) use ($slot, $slotEnd) {
                $bStart = Carbon::parse($b->starts_at);
                $bEnd = Carbon::parse($b->ends_at);
                return $slot->lt($bEnd) && $slotEnd->gt($bStart);
            });

            return [
                'time' => $slot->format('H:i'),
                'datetime' => $slot->toIso8601String(),
                'booking' => $booking ? [
                    'id' => $booking->id,
                    'customer_name' => $booking->user->name,
                    'customer_phone' => $booking->user->phone,
                    'service_name' => $booking->service?->name,
                    'starts_at' => $booking->starts_at->format('H:i'),
                    'ends_at' => $booking->ends_at->format('H:i'),
                ] : null,
            ];
        })->toArray();
    }

    public function blockSlot(string $datetime): void
    {
        if (!$this->teamMemberId) {
            return;
        }

        $startsAt = Carbon::parse($datetime);
        $endsAt = $startsAt->copy()->addMinutes(30);

        Booking::create([
            'user_id' => auth()->id(),
            'service_id' => null,
            'team_member_id' => $this->teamMemberId,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => 'confirmed',
            'attended' => false,
        ]);

        $this->loadSlots();
    }

    public function unblockSlot(int $bookingId): void
    {
        $booking = Booking::find($bookingId);

        if ($booking && $booking->service_id === null && $booking->user_id === auth()->id()) {
            $booking->delete();
            $this->loadSlots();
        }
    }

    public function nextDay(): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->addDay()->format('Y-m-d');
        $this->loadSlots();
    }

    public function prevDay(): void
    {
        $prevDate = Carbon::parse($this->selectedDate)->subDay();
        if ($prevDate->isBefore(now()->startOfDay())) {
            return;
        }
        $this->selectedDate = $prevDate->format('Y-m-d');
        $this->loadSlots();
    }

    public function isToday(): bool
    {
        return $this->selectedDate === now()->format('Y-m-d');
    }
};
?>

<div>
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <flux:field label="Team Member">
                    <flux:select wire:model.live="teamMemberId" placeholder="Select a team member">
                        @foreach($this->loadTeamMembers() as $member)
                            <option
                                value="{{ $member['id'] }}">{{ $member['first_name'] }} {{ $member['last_name'] }}</option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>

            <div>
                <flux:field label="Date">
                    <flux:date-picker wire:model.live="selectedDate" selectable-header/>
                </flux:field>
            </div>
        </div>

        <div class="flex gap-2 mt-4">
            <button
                wire:click="prevDay"
                @if($this->isToday()) disabled @endif
                class="flex-1 px-4 py-2 rounded-md transition font-medium {{ $this->isToday() ? 'bg-gray-50 text-gray-400 cursor-not-allowed' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
            >
                ← Previous Day
            </button>
            <button
                wire:click="nextDay"
                class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition font-medium"
            >
                Next Day →
            </button>
        </div>

    </div>

        @if($teamMemberId && $hasSchedule)
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Schedule
                    for {{ \Carbon\Carbon::parse($selectedDate)->format('D, F j, Y') }}</h2>

                <div class="grid grid-cols-1 gap-6">
                    @foreach($timeSlots as $slot)
                        <div
                            class="border rounded-lg p-4 {{ $slot['booking'] ? 'bg-blue-50 border-blue-200' : 'bg-white border-gray-200' }}">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-gray-900">{{ $slot['time'] }}</span>
                                @if($slot['booking'])
                                    @if($slot['booking']['service_name'])
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                Booked
                                            </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                Blocked
                                            </span>
                                    @endif
                                @else
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            Available
                                        </span>
                                @endif
                            </div>

                            @if($slot['booking'])
                                <div class="text-sm space-y-1">
                                    @if($slot['booking']['service_name'])
                                        <p class="font-medium text-gray-900">{{ $slot['booking']['customer_name'] }}</p>
                                        <p class="text-gray-600">{{ $slot['booking']['service_name'] }}</p>
                                        @if($slot['booking']['customer_phone'])
                                            <p class="text-gray-500 text-xs">{{ $slot['booking']['customer_phone'] }}</p>
                                        @endif
                                    @else
                                        <p class="text-gray-600 italic">Time blocked out</p>
                                        <button
                                            wire:click="unblockSlot({{ $slot['booking']['id'] }})"
                                            class="mt-2 w-full text-sm px-2 py-1 bg-red-100 text-red-700 rounded cursor-pointer hover:bg-red-200 transition"
                                        >
                                            Unblock
                                        </button>
                                    @endif
                                </div>
                            @else
                                <button
                                    wire:click="blockSlot('{{ $slot['datetime'] }}')"
                                    class="mt-2 w-full text-sm px-3 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition font-medium cursor-pointer"
                                >
                                    Block Out
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @elseif($teamMemberId)
            <div class="mt-8 text-center p-12 bg-gray-50 rounded-lg">
                <p class="text-gray-500">No schedule found for this team member on the selected date.</p>
            </div>
        @else
            <div class="mt-8 text-center py-12 bg-gray-50 rounded-lg">
                <p class="text-gray-500">Please select a team member to view their schedule.</p>
            </div>
        @endif
    </div>
</div>
