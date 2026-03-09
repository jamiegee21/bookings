@extends('layouts.booking')

@section('content')
    <div>

        <div class="mb-8 text-xl font-bold">Welcome, {{ auth()->user()->first_name }}</div>

        <!-- Full width booking button -->
        <div class="mb-8">
            @if($confirmedBookingsCount >= 3)
                <button disabled class="w-full block rounded-lg text-xl font-bold bg-gray-300 text-gray-500 text-center py-2 cursor-not-allowed">
                    Book Appointment
                </button>
                <p class="text-sm text-red-600 mt-2 text-center">You have the maximum of 3 active bookings</p>
            @else
                <a href="{{ route('book') }}" class="w-full block rounded-lg text-xl font-bold bg-red-500 hover:bg-red-600 text-center py-2 text-white">
                    Book Appointment
                </a>
            @endif
        </div>

        <!-- Dashboard grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <a href="{{ route('appointments') }}" class="flex md:flex-col md:justify-center items-start gap-4 md:gap-0 md:space-y-1 hover:border-rose-500 bg-white rounded-lg p-5 border border-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
                <div class="space-y-1">
                    <div class="text-lg font-semibold">My Appointments</div>
                    <div class="text-sm text-zinc-600">View and manage your bookings</div>
                </div>
            </a>

            <a href="{{ route('loyalty-scheme') }}" class="flex md:flex-col md:justify-center items-start gap-4 md:gap-0 md:space-y-1 hover:border-rose-500 bg-white rounded-lg p-5 border border-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                </svg>
                <div class="space-y-1">
                    <div class="text-lg font-semibold">Loyalty Scheme</div>
                    <div class="text-sm text-zinc-600">Check your status</div>
                </div>
            </a>

            <a href="{{ route('profile-settings') }}" class="flex md:flex-col md:justify-center items-start gap-4 md:gap-0 md:space-y-1 hover:border-rose-500 bg-white rounded-lg p-5 border border-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                <div class="space-y-1">
                    <div class="text-lg font-semibold">Profile Settings</div>
                    <div class="text-sm text-zinc-600">Update your details</div>
                </div>
            </a>

            <a href="{{ route('booking-history') }}" class="flex md:flex-col md:justify-center items-start gap-4 md:gap-0 md:space-y-1 hover:border-rose-500 bg-white rounded-lg p-5 border border-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
                <div class="space-y-1">
                    <div class="text-lg font-semibold">Booking History</div>
                    <div class="text-sm text-zinc-600">See your past appointments</div>
                </div>
            </a>

        </div>
    </div>
@endsection
