<?php

use Livewire\Component;
use App\Models\Booking;

new class extends Component
{
    public function getAppointmentsProperty()
    {
        return Booking::where('user_id', auth()->id())
            ->where('status', 'confirmed')
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->get();
    }

    public function cancelAppointment($id)
    {
        $booking = Booking::find($id);

        if ($booking && $booking->user_id === auth()->id()) {
            $booking->update(['status' => 'cancelled']);
            $this->dispatch('toast-show', slots: ['text' => 'Your appointment has been cancelled.'], duration: 5000, dataset: []);
        }
    }
};
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <div class="font-black text-xl mb-8 text-center">My Appointments</div>
    </div>

    @if ($this->appointments->count() > 0)
        <div class="space-y-4">
            @foreach ($this->appointments as $appointment)
                <div wire:key="appointment-{{ $appointment->id }}" class="bg-white rounded-lg border border-gray-300 p-6">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="">
                               <div class="text-xl font-bold mb-3">{{ $appointment->service?->name ?? 'Service' }} - £{{ number_format($appointment->service?->price ?? 0, 2) }}</div>
                                <div class="flex items-center gap-4 text-zinc-600">
                                    <div class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                        </svg>
                                        {{ $appointment->starts_at->format('l j F Y') }}
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                        {{ $appointment->starts_at->format('H:i') }} ({{ $appointment->service?->duration_minutes ?? 0 }} min)
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-3 mt-3">
                                <flux:modal.trigger name="cancel-appointment-{{ $appointment->id }}">
                                    <button class="block rounded-lg border text-sm border-gray-300 hover:border-red-500 text-center py-2 px-4 text-gray-500 cursor-pointer">
                                        Cancel Appointment
                                    </button>
                                </flux:modal.trigger>
                            </div>

                            <flux:modal name="cancel-appointment-{{ $appointment->id }}">
                                <div class="space-y-6">
                                    <div>
                                        <flux:heading size="lg">Cancel Appointment</flux:heading>
                                        <flux:text class="mt-2">Are you sure you want to cancel this appointment? This action cannot be undone.</flux:text>
                                    </div>
                                    <div class="flex gap-3 justify-end">
                                        <flux:modal.close>
                                            <flux:button variant="ghost">No, keep it</flux:button>
                                        </flux:modal.close>
                                        <flux:modal.close>
                                            <flux:button variant="danger" wire:click="cancelAppointment('{{ $appointment->id }}')">Yes, cancel it</flux:button>
                                        </flux:modal.close>
                                    </div>
                                </div>
                            </flux:modal>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="flex flex-col items-center gap-2 p-10 rounded-xl border border-gray-300 bg-white">
            <div class="mb-4">
                <svg class="w-8 h-8" data-slot="icon" fill="none" stroke-width="1.5" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"></path>
                </svg>
            </div>
            <div class="text-xl font-bold mb-1">No upcoming appointments</div>
            <div class="text-gray-600 text-sm mb-6">You don't have any appointments scheduled.</div>
            <a href="{{ route('book') }}" class="cursor-pointer w-full block rounded-lg bg-red-500 hover:bg-red-600 text-center py-2 text-white">
                Make a Booking
            </a>
        </div>
    @endif
</div>
