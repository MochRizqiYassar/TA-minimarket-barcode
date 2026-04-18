@extends('layouts.admin')

@section('content')
<div class="container">
    <h3>Cetak Barcode Manual</h3>

    <form action="{{ route('barcode.generate') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Kode Barcode</label>
            <input type="text" name="kode" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Jumlah Cetak</label>
            <input type="number" name="jumlah" class="form-control" min="1" required>
        </div>

        <button class="btn btn-primary">Generate PDF</button>
    </form>
</div>
@endsection
