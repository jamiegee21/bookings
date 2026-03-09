<?php

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use App\Services\SlotGenerator;
use Carbon\Carbon;
use Livewire\Component;

new class extends Component
{
    public ?int $serviceId = null;

    public ?int $userId = null;

    public ?string $selectedDate = null;

    public ?string $selectedSlot = null;

    public ?string $customerPhone = null;


    public function step(): int
    {
        if (! $this->serviceId) {
            return 1;
        }
        if (! $this->userId) {
            return 2;
        }
        if (! $this->selectedDate || ! $this->selectedSlot) {
            return 3;
        }

        return 4;
    }

    public function getService(): ?Service
    {
        return $this->serviceId ? Service::find($this->serviceId) : null;
    }

    public function getTeamMember(): ?User
    {
        return $this->userId ? User::find($this->userId) : null;
    }

    public function getDateOptions(): array
    {
        $options = [];
        for ($i = 0; $i < 14; $i++) {
            $date = now()->addDays($i);
            $options[] = [
                'value' => $date->format('Y-m-d'),
                'label' => $date->isToday() ? 'Today' : ($date->isTomorrow() ? 'Tomorrow' : $date->format('D jS M')),
            ];
        }
        return $options;
    }

    public function getAvailableTimeSlots(): array
    {
        if (! $this->selectedDate || ! $this->userId) {
            return [];
        }
        $user = User::find($this->userId);
        if (! $user) {
            return [];
        }
        $date = Carbon::parse($this->selectedDate);
        $slots = app(SlotGenerator::class)->availableSlots($user, $date);
        return $slots->map(fn (Carbon $slot) => $slot->format('H:i'))->all();
    }

    public function selectService(int $id): void
    {
        $this->serviceId = $id;
        $this->userId = null;
        $this->selectedDate = null;
        $this->selectedSlot = null;
    }

    public function selectTeamMember(int $id): void
    {
        $this->userId = $id;
        $this->selectedDate = now()->format('Y-m-d'); // Default to today
        $this->selectedSlot = null;
    }

    public function selectDate(string $date): void
    {
        $this->selectedDate = $date;
        $this->selectedSlot = null;
    }

    public function updatedSelectedDate($value): void
    {
        if ($value instanceof \Carbon\Carbon) {
            $this->selectedDate = $value->format('Y-m-d');
        }
        $this->selectedSlot = null;
    }

    public function selectSlot(string $time): void
    {
        $this->selectedSlot = $time;
    }

    public function confirmBooking()
    {
        $service = Service::find($this->serviceId);
        $barber = User::find($this->userId);
        if (! $service || ! $barber || ! $this->selectedDate || ! $this->selectedSlot) {
            return;
        }

        $startsAt = Carbon::parse($this->selectedDate.' '.$this->selectedSlot);
        $endsAt = $startsAt->copy()->addMinutes($service->duration_minutes);

        Booking::create([
            'user_id' => auth()->id(),
            'service_id' => $service->id,
            'team_member_id' => $this->userId,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ]);

        session()->flash('booking.confirmed', true);
        return $this->redirect(route('booking-confirmation'), navigate: true);
    }

    public function backToServices(): void
    {
        $this->reset(['serviceId', 'userId', 'selectedDate', 'selectedSlot']);
    }

    public function backToTeamMembers(): void
    {
        $this->reset(['userId', 'selectedDate', 'selectedSlot']);
    }

    public function backToDate(): void
    {
        $this->reset(['selectedDate', 'selectedSlot']);
    }

    public function backToSlots(): void
    {
        $this->reset(['selectedSlot']);
    }
};
?>

<div>
    @if (session('booking.confirmed'))
        <flux:callout variant="success" heading="Booking confirmed" text="We've saved your appointment. You can book another below." icon="check-circle" class="mb-6" />
    @endif

    @switch($this->step())
        @case(1)
            <section>
                <div class="font-black text-xl mb-8 text-center">Choose a service</div>
                <div class="space-y-3">
                    @foreach (\App\Models\Service::orderBy('id')->get() as $service)

                        <div wire:click="selectService({{ $service->id }})" class="flex justify-between items-center p-4 rounded-xl border border-gray-300 bg-white cursor-pointer hover:border-rose-500">
                            <div class="flex flex-col space-y-1">
                                <span class="text-xl font-bold">{{ $service->name }}</span>
                                <span>{{ $service->duration_minutes }} min</span>
                            </div>
                            <div class="font-bold text-xl">
                                £{{ number_format($service->price, 2) }}
                            </div>
                        </div>

                    @endforeach
                </div>
            </section>
            @break

        @case(2)
            <div class="mb-4">
                <flux:button type="button" wire:click="backToServices" size="sm">← Back</flux:button>
            </div>
            <section>

                <div class="font-black text-xl mb-8 text-center">Choose your barber</div>
                <div class="space-y-3">
                    @foreach ($this->getService()?->users ?? [] as $member)

                        <div wire:click="selectTeamMember({{ $member->id }})" class="flex justify-between items-center p-4 rounded-xl border border-gray-300 bg-white cursor-pointer hover:border-rose-500">
                            <div class="flex flex-col space-y-1">
                                <span class="text-xl font-bold"> {{ $member->name }}</span>
                                <span>Head Barber</span>
                            </div>
                        </div>

                    @endforeach
                </div>

            </section>
            @break

        @case(3)
            <div class="mb-4">
                <flux:button type="button" wire:click="backToTeamMembers" size="sm">← Back</flux:button>
            </div>
            <section>
                <div class="font-black text-xl mb-8 text-center">Choose date and time</div>
                <div class="relative">
                    <div class="flex gap-2 mb-6 overflow-x-auto pb-2 snap-x snap-mandatory scroll-smooth">
                        @foreach ($this->getDateOptions() as $opt)
                            <div
                                wire:click="selectDate('{{ $opt['value'] }}')"
                                class="whitespace-nowrap shrink-0 cursor-pointer flex text-sm justify-between items-center p-3 py-0 rounded border snap-start {{ $selectedDate === $opt['value'] ? 'border-rose-500 bg-rose-50' : 'border-gray-300 bg-white hover:border-rose-500' }}"
                            >
                                {{ $opt['label'] }}
                            </div>
                        @endforeach
                    <flux:date-picker
                        wire:model.live="selectedDate"
                        min="today"
                        max="{{ now()->addDays(30)->format('Y-m-d') }}"
                        with-today
                        selectable-header
                        placeholder="Other date"
                    >
                        <x-slot name="trigger">
                            <flux:button type="button" variant="outline" size="sm" icon="calendar-days" title="Pick another date" class="whitespace-nowrap flex-shrink-0">
                                Other date
                            </flux:button>
                        </x-slot>
                    </flux:date-picker>
                </div>

                @if ($selectedDate)
                    <div class="pt-6">
                        <div class="font-black text-xl mb-8 text-center">Available times</div>
                        @php $slots = $this->getAvailableTimeSlots(); @endphp
                        @if (count($slots) === 0)
                            <div class="flex flex-col items-center gap-2 p-10 rounded-xl border border-gray-300 bg-white">
                                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div class="text-xl font-bold mb-1">No slots available</div>
                                <div class="text-gray-600 text-sm text-center">There are no available time slots on this date. Please try selecting a different date.</div>
                            </div>
                        @else
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                @foreach ($slots as $time)
                                    <div
                                        wire:click="selectSlot('{{ $time }}')"
                                        class="cursor-pointer p-2 rounded-lg text-center border border-gray-300 bg-white hover:border-rose-500"
                                    >
                                        {{ $time }}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </section>
            @break

        @case(4)
            <div class="mb-4">
                <flux:button type="button" wire:click="backToSlots" size="sm">← Back</flux:button>
            </div>
            <section>
                <div class="font-black text-xl mb-8 text-center">Booking summary</div>

                <div class="p-4 rounded-xl border border-gray-300 bg-white mb-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div>
                                <div class="text-sm text-gray-500 mb-1">Service</div>
                                <div class="font-semibold">{{ $this->getService()?->name }} - {{ $this->getService()?->duration_minutes }} min</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500 mb-1">Price</div>
                                <div class="font-semibold">£{{ number_format($this->getService()?->price ?? 0, 2) }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500 mb-1">Barber</div>
                                <div class="font-semibold">{{ $this->getTeamMember()?->name }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500 mb-1">Date</div>
                                <div class="font-semibold">{{ $selectedDate ? \Carbon\Carbon::parse($selectedDate)->format('l j F Y') : '' }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500 mb-1">Time</div>
                                <div class="font-semibold">{{ $selectedSlot }}</div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <div class="text-sm text-gray-500 mb-1">Your name</div>
                                <div class="font-semibold">{{ auth()->user()->name }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500 mb-1">Your email</div>
                                <div class="font-semibold">{{ auth()->user()->email }}</div>
                            </div>
                            @if(auth()->user()->phone)
                                <div>
                                    <div class="text-sm text-gray-500 mb-1">Your phone</div>
                                    <div class="font-semibold">{{ auth()->user()->phone }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" wire:click="confirmBooking" class="cursor-pointer w-full block rounded-lg bg-red-500 hover:bg-red-600 text-center py-2 text-white">
                        Confirm booking
                    </button>
                </div>
            </section>
            @break
    @endswitch
</div>
