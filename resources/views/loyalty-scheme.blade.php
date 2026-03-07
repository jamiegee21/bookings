@extends('layouts.booking')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <div class="font-black text-xl mb-8 text-center">Loyalty Scheme</div>
        </div>

        <div class="bg-white rounded-lg border border-gray-300 p-8 text-center">
            <div class="mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 mx-auto text-rose-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                </svg>
            </div>

            <div class="text-2xl font-bold text-gray-900 mb-4">Coming Soon!</div>
            <div class="text-gray-600 mb-8 max-w-md mx-auto">
                Our amazing loyalty scheme is currently in development. Get rewards and enjoy exclusive member benefits. Check back soon for updates!
            </div>

            <div class="mt-8">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 bg-rose-500 hover:bg-rose-600 text-white px-6 py-3 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>
@endsection
