<x-mail::message>
Hi {{ $booking->user->name }},

Your booking has been confirmed! Here are the details:

**Service:** {{ $booking->service->name }}

**Duration:** {{ $booking->service->duration_minutes }} minutes

**Price:** £{{ number_format($booking->service->price, 2) }}

**Barber:** {{ $booking->teamMember->name }}

**Date:** {{ $booking->starts_at->format('l, jS F Y') }}

**Time:** {{ $booking->starts_at->format('g:i A') }} - {{ $booking->ends_at->format('g:i A') }}

If you need to make any changes to your booking, please us the button below.

<x-mail::button :url="route('appointments')">
View My Appointments
</x-mail::button>

Thanks,<br>
Fitchy83 Barbers
</x-mail::message>
