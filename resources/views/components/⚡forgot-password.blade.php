<?php

use Livewire\Component;
use Illuminate\Support\Facades\Password;

new class extends Component
{
    public string $email = '';
    public bool $sent = false;

    public function sendResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'email'],
        ]);

        Password::sendResetLink(['email' => $this->email]);

        $this->sent = true;
    }
};
?>

<div class="w-full max-w-md">

    <div class="mb-4 text-center font-bold text-xl">Forgot your password?</div>

    @if ($sent)
        <flux:callout variant="success" heading="Email sent" text="If an account exists for that email address, we've sent a password reset link." class="mb-6" />

        <div class="text-center text-sm">
            <a href="{{ route('login') }}" class="text-red-500 hover:text-red-600">Back to login</a>
        </div>
    @else
        <p class="text-sm text-zinc-500 text-center mb-8">Enter your email address and we'll send you a link to reset your password.</p>

        <form wire:submit="sendResetLink" class="space-y-6">
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

            <div>
                <button type="submit" class="cursor-pointer w-full block rounded-lg bg-red-500 hover:bg-red-600 text-center py-2 text-white">
                    Send reset link
                </button>
            </div>

            <div class="text-center text-sm">
                <a href="{{ route('login') }}" class="text-red-500 hover:text-red-600">Back to login</a>
            </div>
        </form>
    @endif
</div>
