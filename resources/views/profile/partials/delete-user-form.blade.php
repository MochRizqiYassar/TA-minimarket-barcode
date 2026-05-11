
<form method="POST" action="{{ route('profile.destroy') }}">

    @csrf
    @method('DELETE')

    <div class="mb-3">

        <label class="form-label text-danger">
            Password Konfirmasi
        </label>

        <input
            type="password"
            name="password"
            class="form-control"
            placeholder="Masukkan password"
        >

        @error('password', 'userDeletion')
            <small class="text-danger">
                {{ $message }}
            </small>
        @enderror

    </div>

    <button
        type="submit"
        class="btn btn-danger"
        onclick="return confirm('Yakin ingin menghapus account?')"
    >
        Hapus Account
    </button>

</form>
