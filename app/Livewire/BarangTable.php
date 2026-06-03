<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Barang;

class BarangTable extends Component
{
    public function render()
    {
        return view('livewire.barang-table', [
            'barangs' => Barang::with('kategori','tipeBarang')
                ->latest()
                ->get()
        ]);
    }
}
