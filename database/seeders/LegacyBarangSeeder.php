<?php

namespace Database\Seeders;

use App\Models\Barang;
use Illuminate\Database\Seeder;
use JsonException;
use RuntimeException;

class LegacyBarangSeeder extends Seeder
{
    /**
     * @throws JsonException
     */
    public function run(): void
    {
        $path = base_path('legacy/db.json');
        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException("Tidak dapat membaca data laporan lama: {$path}");
        }

        $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($data['barang'] ?? null)) {
            throw new RuntimeException('Format legacy/db.json tidak memiliki daftar barang yang valid.');
        }

        foreach ($data['barang'] as $row) {
            if (! is_array($row) || ! isset($row['id'], $row['nama'], $row['status'])) {
                throw new RuntimeException('Ada laporan lama dengan data wajib yang tidak lengkap.');
            }

            Barang::firstOrCreate(
                ['legacy_id' => (string) $row['id']],
                [
                    'nama' => (string) $row['nama'],
                    'kategori' => (string) ($row['kategori'] ?? 'Lainnya'),
                    'lokasi' => (string) ($row['lokasi'] ?? 'Tidak diketahui'),
                    'tanggal' => (string) ($row['tanggal'] ?? now()->toDateString()),
                    'jam' => ($row['jam'] ?? '') !== '' ? $row['jam'] : null,
                    'deskripsi' => ($row['deskripsi'] ?? '') !== '' ? $row['deskripsi'] : null,
                    'status' => (string) $row['status'],
                    'nama_pelapor' => (string) ($row['namaPelapor'] ?? 'Pelapor'),
                    'telepon' => (string) ($row['telepon'] ?? ''),
                    'foto' => ($row['foto'] ?? '') !== '' ? $row['foto'] : null,
                    'penyimpanan' => ($row['penyimpanan'] ?? '') !== '' ? $row['penyimpanan'] : null,
                ]
            );
        }
    }
}
