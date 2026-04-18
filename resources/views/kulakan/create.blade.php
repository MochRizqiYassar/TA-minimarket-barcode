
    @extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Tambah Kulakan</h2>

    <form action="{{ route('kulakan.store') }}" method="POST">
        @include('kulakan._form')
    </form>
</div>
@endsection
