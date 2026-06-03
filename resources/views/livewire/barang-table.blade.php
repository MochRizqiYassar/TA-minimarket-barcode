<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div wire:poll.2s>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Barcode</th>
                <th>Nama Barang</th>
                <th>Stok</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>

            @foreach($barangs as $barang)

                <tr>
                    <td>{{ $barang->barcode }}</td>

                    <td>
                        {{ $barang->nama_barang }}
                    </td>

                    <td>
                        {{ $barang->stok }}
                    </td>

                    <td>

                        @if($barang->stok == 0)
                            <span class="badge bg-danger">
                                Habis
                            </span>
                        @else
                            <span class="badge bg-success">
                                Tersedia
                            </span>
                        @endif

                    </td>
                </tr>

            @endforeach

        </tbody>
    </table>

</div>
