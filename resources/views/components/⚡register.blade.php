<?php

use Livewire\Component;
use App\Models\User;

new class extends Component
{
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public ?string $phone = null;

    public function register(): void
    {
        $this->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'string', 'regex:/^07[0-9]{9}$/'],
        ]);

        $user = User::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'password' => \Illuminate\Support\Facades\Hash::make($this->password),
            'phone' => $this->phone,
        ]);

        auth()->login($user);
        $this->redirect(route('dashboard'));
    }
};
?>

<div class="max-w-md mx-auto">
    <div class="font-bold text-xl mb-8 text-center">Create your account</div>

    @if ($errors->any())
        <flux:callout variant="danger" heading="Error" text="Please fix the errors below and try again." class="mb-6" />
    @endif

    <form wire:submit="register" class="space-y-6">
        <div class="grid grid-cols-2 gap-4">
            <flux:field>
                <flux:label>First Name</flux:label>
                <flux:input
                    type="text"
                    wire:model="first_name"
                    required
                    autofocus
                    autocomplete="given-name"
                />
                <flux:error name="first_name" />
            </flux:field>

            <flux:field>
                <flux:label>Last Name</flux:label>
                <flux:input
                    type="text"
                    wire:model="last_name"
                    required
                    autocomplete="family-name"
                />
                <flux:error name="last_name" />
            </flux:field>
        </div>

        <flux:field>
            <flux:label>Email address</flux:label>
            <flux:input
                type="email"
                wire:model="email"
                required
                autocomplete="email"
            />
            <flux:error name="email" />
        </flux:field>

        <flux:field>
            <flux:label>Mobile Number</flux:label>
            <flux:input
                type="tel"
                wire:model="phone"
                required
                autocomplete="tel"
            />
            <flux:error name="phone" />
        </flux:field>

        <flux:field>
            <flux:label>Password</flux:label>
            <flux:input
                type="password"
                wire:model="password"
                required
                autocomplete="new-password"
            />
            <flux:error name="password" />
        </flux:field>

        <flux:field>
            <flux:label>Confirm Password</flux:label>
            <flux:input
                type="password"
                wire:model="password_confirmation"
                required
                autocomplete="new-password"
            />
            <flux:error name="password_confirmation" />
        </flux:field>

        <div>
            <button type="submit" class="transition-colors duration-200 cursor-pointer w-full block rounded-lg bg-red-500 hover:bg-red-600 text-center py-2 text-white">
                Register
            </button>
        </div>

        <div class="text-center">
            <flux:text size="md">
                Already have an account?
                <a href="{{ route('login') }}" class="text-rose-600 hover:text-rose-500 ml-1">
                    Log in here
                </a>
            </flux:text>
        </div>
    </form>
</div>
