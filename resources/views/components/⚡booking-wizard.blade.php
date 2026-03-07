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
                <flux:button type="button" wire:click="backToTeamMembers" variant="ghost" size="sm">← Back</flux:button>
            </div>
            <section>
                <div class="font-black text-xl mb-8 text-center">Choose date and time</div>
                <div class="flex gap-2 mb-6 overflow-x-auto pb-2">
                    @foreach ($this->getDateOptions() as $opt)
                        <div
                            wire:click="selectDate('{{ $opt['value'] }}')"
                            class="whitespace-nowrap shrink-0 cursor-pointer flex text-sm justify-between items-center p-3 py-0 rounded border {{ $selectedDate === $opt['value'] ? 'border-rose-500 bg-rose-50' : 'border-gray-300 bg-white hover:border-rose-500' }}"
                        >
                            {{ $opt['label'] }}
                        </div>
                    @endforeach
                    <flux:date-picker
                        wire:model.live="selectedDate"
                        min="today"
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
                            <flux:callout variant="warning" heading="No appointments available" text="There are no available time slots on this date. Please try selecting a different date." class="mb-4" />
                            <flux:text variant="subtle">Try selecting another date from the options above.</flux:text>
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
                <flux:button type="button" wire:click="backToTeamMembers" variant="ghost" size="sm">← Back</flux:button>
            </div>
            <section>
                <flux:heading size="lg" class="mb-3">Confirm your booking</flux:heading>

                <flux:callout class="mb-6" heading="Booking summary">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-2 text-sm sm:grid-cols-[auto_1fr]">
                        <dt class="text-zinc-500 dark:text-white/50">Service</dt>
                        <dd>{{ $this->getService()?->name }} · {{ $this->getService()?->duration_minutes }} min · £{{ number_format($this->getService()?->price ?? 0, 2) }}</dd>
                        <dt class="text-zinc-500 dark:text-white/50">Barber</dt>
                        <dd>{{ $this->getTeamMember()?->name }}</dd>
                        <dt class="text-zinc-500 dark:text-white/50">Date</dt>
                        <dd>{{ $selectedDate ? \Carbon\Carbon::parse($selectedDate)->format('l j F Y') : '' }}</dd>
                        <dt class="text-zinc-500 dark:text-white/50">Time</dt>
                        <dd>{{ $selectedSlot }}</dd>
                        <dt class="text-zinc-500 dark:text-white/50">Your name</dt>
                        <dd>{{ auth()->user()->name }}</dd>
                        <dt class="text-zinc-500 dark:text-white/50">Your email</dt>
                        <dd>{{ auth()->user()->email }}</dd>
                        @if(auth()->user()->phone)
                            <dt class="text-zinc-500 dark:text-white/50">Your phone</dt>
                            <dd>{{ auth()->user()->phone }}</dd>
                        @endif
                    </dl>
                </flux:callout>

                <div class="pt-2">
                    <flux:button type="button" wire:click="confirmBooking" variant="primary" color="amber" class="w-full">
                        Confirm booking
                    </flux:button>
                </div>
            </section>
            @break
    @endswitch
</div>
