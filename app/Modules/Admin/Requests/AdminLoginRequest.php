<?php

namespace App\Modules\Admin\Requests;

use App\Modules\Admin\Models\Admin;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AdminLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'alpha_dash', 'max:50'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Validate the request's credentials and return the admin without logging them in.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateCredentials(): Admin
    {
        $this->ensureIsNotRateLimited();

        $provider = Auth::guard('admin')->getProvider();

        /** @var Admin|null $admin */
        $admin = $provider->retrieveByCredentials($this->only('username', 'password'));

        if (! $admin || ! $provider->validateCredentials($admin, $this->only('password'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'username' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        return $admin;
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate-limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return 'admin-login|'.$this->string('username')->lower()->value().'|'.$this->ip();
    }
}
