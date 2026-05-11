<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/auth.css') }}">
</head>

<body>

</html>
<div id="auth">
    <div class="row h-100">
        <div class="col-lg-5 col-12">
            <div id="auth-left">

                <div class="logo">
                    <img src="{{ asset('assets/images/logo/toko1.png') }}" alt="Toko1"
                        style="width: 210px; height: auto;">
                </div>

                <h1 class="auth-title">Log in</h1>
                <p class="auth-subtitle mb-5">
                    Log in dengan akun yang sudah terdaftar
                </p>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="email" name="email"
                            class="form-control form-control-xl @error('email') is-invalid @enderror"
                            placeholder="Email" value="{{ old('email') }}" required autofocus>

                        <div class="form-control-icon">
                            <i class="bi bi-envelope"></i>
                        </div>

                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="password" name="password"
                            class="form-control form-control-xl @error('password') is-invalid @enderror"
                            placeholder="Password" required>

                        <div class="form-control-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>

                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="form-check form-check-lg d-flex align-items-end mb-4">
                        <input class="form-check-input me-2" type="checkbox" name="remember" id="remember_me">
                        <label class="form-check-label text-gray-600" for="remember_me">
                            Remember me
                        </label>
                    </div>

                    <button class="btn btn-primary btn-block btn-lg shadow-lg mt-3">
                        Log in
                    </button>
                </form>

                <div class="text-center mt-5 text-lg fs-4">
                    @if (Route::has('register'))
                        <p class="text-gray-600">
                            Belum punya akun?
                            <a href="{{ route('register') }}" class="font-bold">Daftar</a>
                        </p>
                    @endif

                    @if (Route::has('password.request'))
                        <p>
                            <a class="font-bold" href="{{ route('password.request') }}">
                                Lupa password?
                            </a>
                        </p>
                    @endif
                </div>

            </div>
        </div>

        <div class="col-lg-7 d-none d-lg-block p-0">

            <div id="auth-right"
                style="
            background-image:
                linear-gradient(
                    rgba(15, 23, 42, 0.55),
                    rgba(37, 99, 235, 0.35)
                ),
                url('{{ asset('assets/images/logo/gambar.png') }}');

            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;

            min-height: 100vh;
            position: relative;
            overflow: hidden;
        ">


            </div>

        </div>
    </div>
</div>
</body>
