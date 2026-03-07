<?php

use Livewire\Component;
use App\Models\User;

new class extends Component
{
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $current_password;
    public $password;
    public $password_confirmation;

    public function mount()
    {
        $user = auth()->user();
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->phone = $user->phone;
    }

    public function updateProfile()
    {
        $user = auth()->user();

        $this->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'regex:/^07[0-9]{9}$/'],
        ]);

        $user->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
        ]);

        session()->flash('profile_updated', true);
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        auth()->user()->update([
            'password' => \Illuminate\Support\Facades\Hash::make($this->password),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);
        session()->flash('password_updated', true);
    }
};
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <div class="font-black text-xl mb-8 text-center">Profile Settings</div>
    </div>

    <div class="space-y-8">
        <!-- Personal Information -->
        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <div class="text-xl font-bold mb-6">Personal Information</div>

            @if (session()->has('profile_updated'))
                <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg">
                    Profile updated successfully!
                </div>
            @endif

            <form wire:submit="updateProfile" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <input
                            type="text"
                            wire:model="first_name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500"
                            required
                        />
                        @error('first_name')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <input
                            type="text"
                            wire:model="last_name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500"
                            required
                        />
                        @error('last_name')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input
                        type="email"
                        wire:model="email"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500"
                        required
                    />
                    @error('email')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Number</label>
                    <input
                        type="tel"
                        wire:model="phone"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500"
                    />
                    @error('phone')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="pt-4">
                    <button type="submit" class="bg-rose-500 hover:bg-rose-600 text-white px-6 py-2 rounded-lg">
                        Update Profile
                    </button>
                </div>
            </form>
        </div>

        <!-- Password Change -->
        <div class="bg-white rounded-lg border border-gray-300 p-6">
            <div class="text-xl font-bold mb-6">Change Password</div>

            @if (session()->has('password_updated'))
                <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg">
                    Password updated successfully!
                </div>
            @endif

            <form wire:submit="updatePassword" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                    <input
                        type="password"
                        wire:model="current_password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500"
                        required
                    />
                    @error('current_password')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input
                        type="password"
                        wire:model="password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500"
                        required
                    />
                    @error('password')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                    <input
                        type="password"
                        wire:model="password_confirmation"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500"
                        required
                    />
                    @error('password_confirmation')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="pt-4">
                    <button type="submit" class="bg-rose-500 hover:bg-rose-600 text-white px-6 py-2 rounded-lg">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
