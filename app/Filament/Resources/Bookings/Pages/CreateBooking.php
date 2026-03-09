<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Filament\Resources\Bookings\BookingResource;
use App\Mail\BookingConfirmation;
use App\Models\Booking;
use Illuminate\Support\Facades\Mail;
use Filament\Resources\Pages\CreateRecord;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

    protected function afterCreate(): void
    {
        /** @var Booking $booking */
        $booking = $this->record;
        $booking->load(['user', 'service', 'teamMember']);

        Mail::to($booking->user->email)->send(new BookingConfirmation($booking));
    }
}
