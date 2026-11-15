<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $fillable = [
        'legacy_id',
        'nama',
        'kategori',
        'lokasi',
        'tanggal',
        'jam',
        'deskripsi',
        'status',
        'nama_pelapor',
        'telepon',
        'foto',
        'penyimpanan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }
}
