<?php

use Livewire\Component;

new class extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function login(): void
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! auth()->attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $this->addError('email', 'The provided credentials do not match our records.');
            return;
        }

        session()->regenerate();
        $this->redirect(route('dashboard'));
    }
};
?>

<div class="w-full max-w-md">

    <div class="mb-10 text-center font-bold text-xl">Log in to your account</div>

    @if (request()->query('reset'))
        <flux:callout variant="success" heading="Password reset" text="Your password has been reset. You can now log in." class="mb-6" />
    @endif

    @if ($errors->any())
        <flux:callout variant="danger" heading="Error" text="Please check your credentials and try again." class="mb-6" />
    @endif

    <form wire:submit="login" class="space-y-6">
        <flux:field>
            <flux:label>Email address</flux:label>
            <flux:input
                type="email"
                wire:model="email"
                required
                autofocus
                autocomplete="email"
            />
            <flux:error name="email" />
        </flux:field>

        <flux:field>
            <flux:label>Password</flux:label>
            <flux:input
                type="password"
                wire:model="password"
                required
                autocomplete="current-password"
            />
            <flux:error name="password" />
        </flux:field>

        <div class="flex justify-between items-center">
            <flux:field variant="inline">
                <flux:checkbox wire:model="remember" />
                <flux:label>Remember me</flux:label>
            </flux:field>
            <a href="{{ route('password.request') }}" class="text-sm text-red-500 hover:text-red-600">Forgot password?</a>
        </div>

        <div>
            <button type="submit" class="cursor-pointer w-full block rounded-lg bg-red-500 hover:bg-red-600 text-center py-2 text-white">
                Log in
            </button>
        </div>

        <div class="text-center text-sm">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-red-500 hover:text-red-600 ml-1">
                Create one now
            </a>
        </div>
    </form>
</div>
