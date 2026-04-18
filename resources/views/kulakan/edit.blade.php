
    @extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Edit Kulakan</h2>

    <form action="{{ route('kulakan.update', $kulakan) }}" method="POST">
        @method('PUT')
        @include('kulakan._form')
    </form>
</div>
@endsection

