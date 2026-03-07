<?php

use Livewire\Component;
use App\Models\Booking;

new class extends Component
{
    public function getPastAppointmentsProperty()
    {
        return Booking::where('user_id', auth()->id())
            ->where('status', 'confirmed')
            ->where('starts_at', '<', now())
            ->orderBy('starts_at', 'desc')
            ->get();
    }
};
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <div class="font-black text-xl mb-8 text-center">Booking History</div>
    </div>

    @if ($this->pastAppointments->count() > 0)
        <div class="space-y-4">
            @foreach ($this->pastAppointments as $appointment)
                <div class="bg-white rounded-lg border border-gray-300 p-6">
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

                            @if ($appointment->attended == true)
                                <div class="mt-3 inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 rounded text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    Attended
                                </div>
                            @elseif ($appointment->attended == false)
                                <div class="mt-3 inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-700 rounded text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                    No-show
                                </div>
                            @else
                                <div class="mt-3 inline-flex items-center gap-1 px-2 py-1 bg-gray-100 text-gray-700 rounded text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    Attendance not recorded
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <div class="mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 mx-auto text-zinc-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
            </div>
            <div class="font-black text-xl mb-2">No past appointments</div>
            <div class="text-zinc-600 mb-6">You don't have any past appointments. Book one now!</div>
            <flux:button href="{{ route('book') }}" variant="primary" color="rose">
                Make a Booking
            </flux:button>
        </div>
    @endif
</div>
