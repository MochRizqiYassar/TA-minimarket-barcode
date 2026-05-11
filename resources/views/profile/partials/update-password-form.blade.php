
<form method="POST" action="{{ route('password.update') }}">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">
            Password Lama
        </label>

        <input
            type="password"
            name="current_password"
            class="form-control"
        >

        @error('current_password', 'updatePassword')
            <small class="text-danger">
                {{ $message }}
            </small>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">
            Password Baru
        </label>

        <input
            type="password"
            name="password"
            class="form-control"
        >

        @error('password', 'updatePassword')
            <small class="text-danger">
                {{ $message }}
            </small>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">
            Konfirmasi Password
        </label>

        <input
            type="password"
            name="password_confirmation"
            class="form-control"
        >
    </div>

    <button class="btn btn-warning">
        Update Password
    </button>

    @if (session('status') === 'password-updated')
        <span class="text-success ms-3">
            Password berhasil diupdate
        </span>
    @endif
</form>
