<?php

use Livewire\Component;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

new class extends Component
{
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->query('email', '');
    }

    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function ($user, $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PasswordReset) {
            $this->redirect(route('login') . '?reset=1');
        } else {
            $this->addError('email', __($status));
        }
    }
};
?>

<div class="w-full max-w-md">

    <div class="mb-4 text-center font-bold text-xl">Reset your password</div>

    <p class="text-sm text-zinc-500 text-center mb-8">Enter your new password below.</p>

    @if ($errors->any())
        <flux:callout variant="danger" heading="Error" text="Please check the form and try again." class="mb-6" />
    @endif

    <form wire:submit="resetPassword" class="space-y-6">
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
            <flux:label>New password</flux:label>
            <flux:input
                type="password"
                wire:model="password"
                required
                autofocus
                autocomplete="new-password"
            />
            <flux:error name="password" />
        </flux:field>

        <flux:field>
            <flux:label>Confirm new password</flux:label>
            <flux:input
                type="password"
                wire:model="password_confirmation"
                required
                autocomplete="new-password"
            />
            <flux:error name="password_confirmation" />
        </flux:field>

        <div>
            <button type="submit" class="cursor-pointer w-full block rounded-lg bg-red-500 hover:bg-red-600 text-center py-2 text-white">
                Reset password
            </button>
        </div>
    </form>
</div>
