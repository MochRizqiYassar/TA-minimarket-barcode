@extends('layouts.admin')

@section('content')

<div class="page-heading">
    <h3>Profile</h3>
</div>

<div class="page-content">
    <section class="row">

        {{-- UPDATE PROFILE --}}
        <div class="col-12 mb-4">
            <div class="card">

                <div class="card-header">
                    <h4>Informasi Profile</h4>
                </div>

                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>

            </div>
        </div>

        {{-- UPDATE PASSWORD --}}
        <div class="col-12 mb-4">
            <div class="card">

                <div class="card-header">
                    <h4>Update Password</h4>
                </div>

                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>

            </div>
        </div>

        {{-- DELETE ACCOUNT --}}
        <div class="col-12">
            <div class="card border border-danger">

                <div class="card-header bg-danger text-white">
                    <h4 class="text-white mb-0">Hapus Account</h4>
                </div>

                <div class="card-body">
                    @include('profile.partials.delete-user-form')
                </div>

            </div>
        </div>

    </section>
</div>

@endsection
```
