
    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')
        <div class="card-body">
        <div class="form-group">
        <div class="mb-3">
            <label for="current-password" class="form-label">Current Password</label>
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="form-control" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div class="mb-3">
            <label for="New-password" class="form-label">New Password</label>
            <x-text-input id="update_password_password" name="password" type="password" class="form-control" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div class="mb-3">
            <label for="Confirm-password" class="form-label">Confirm Password</label>
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
        <button type="submit" class="btn btn-success" >{{ __('Save') }}</button>

            @if (session('status') === 'password-updated')
                {{ __('Saved.') }}
            @endif
        </div>
        </div>
        </div>
    </form>
