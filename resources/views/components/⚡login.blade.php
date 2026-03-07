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
        $this->redirect(route('home'));
    }
};
?>

<div class="max-w-md mx-auto">

    <flux:heading size="lg" class="mb-6 text-center">Log in to your account</flux:heading>

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

        <div class="flex items-center justify-between">
            <label class="flex items-center">
                <input type="checkbox" wire:model="remember" class="rounded border-zinc-300 text-rose-600 focus:ring-rose-500">
                <span class="ml-2 text-sm text-zinc-600 dark:text-zinc-400">Remember me</span>
            </label>
        </div>

        <div>
            <flux:button type="submit" variant="primary" color="rose" class="w-full">
                Log in
            </flux:button>
        </div>

        <div class="text-center">
            <flux:text size="md">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-rose-500 hover:text-rose-500 ml-1">
                    Register here
                </a>
            </flux:text>
        </div>
    </form>
</div>
