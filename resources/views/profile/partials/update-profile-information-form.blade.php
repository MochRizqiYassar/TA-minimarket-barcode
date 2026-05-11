
<form method="POST" action="{{ route('profile.update') }}">
    @csrf
    @method('PATCH')

    <div class="mb-3">
        <label class="form-label">
            Nama
        </label>

        <input
            type="text"
            name="name"
            class="form-control"
            value="{{ old('name', $user->name) }}"
            required
        >

        @error('name')
            <small class="text-danger">
                {{ $message }}
            </small>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">
            Email
        </label>

        <input
            type="email"
            name="email"
            class="form-control"
            value="{{ old('email', $user->email) }}"
            required
        >

        @error('email')
            <small class="text-danger">
                {{ $message }}
            </small>
        @enderror
    </div>

    <button class="btn btn-primary">
        Simpan Perubahan
    </button>

    @if (session('status') === 'profile-updated')
        <span class="text-success ms-3">
            Profile berhasil diperbarui
        </span>
    @endif
</form>
