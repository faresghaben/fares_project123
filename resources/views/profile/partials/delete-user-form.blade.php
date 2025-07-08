<section class="mt-4">
    <header>
        <h2 class="h4">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-2 text-muted">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    {{-- الزر الذي يفتح النافذة المنبثقة، تم استبدال x-danger-button بزر Bootstrap --}}
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
        {{ __('Delete Account') }}
    </button>

    {{-- تم استبدال x-modal بكود Bootstrap Modal مباشر --}}
    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="{{ route('profile.destroy') }}" class="p-4"> {{-- أضفت p-4 لبعض المسافات الداخلية --}}
                    @csrf
                    @method('delete')

                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmUserDeletionModalLabel">{{ __('Are you sure you want to delete your account?') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted">
                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                        </p>

                        <div class="mb-3 mt-4">
                            {{-- تم استبدال x-input-label بـ label عادي --}}
                            <label for="password_delete" class="form-label visually-hidden">{{ __('Password') }}</label>
                            {{-- تم استبدال x-text-input بـ input عادي --}}
                            <input
                                id="password_delete"
                                name="password"
                                type="password"
                                class="form-control"
                                placeholder="{{ __('Password') }}"
                            />
                            {{-- تم استبدال x-input-error بعرض الأخطاء يدوياً لـ Bootstrap --}}
                            @error('password', 'userDeletion')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{-- تم استبدال x-secondary-button بزر Bootstrap --}}
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        {{-- تم استبدال x-danger-button بزر Bootstrap --}}
                        <button type="submit" class="btn btn-danger">{{ __('Delete Account') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>