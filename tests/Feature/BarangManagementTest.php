<?php

namespace Tests\Feature;

use App\Models\Barang;
use Database\Seeders\LegacyBarangSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarangManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_eloquent_can_create_list_and_find_a_barang_report(): void
    {
        $created = Barang::create($this->reportData());

        $this->assertSame('Dompet', Barang::all()->first()->nama);
        $this->assertSame($created->id, Barang::find($created->id)?->id);
        $this->assertDatabaseHas('barangs', [
            'id' => $created->id,
            'nama' => 'Dompet',
            'status' => 'Hilang',
        ]);
    }

    public function test_nemu_homepage_shows_database_reports_and_can_filter_them(): void
    {
        Barang::create($this->reportData());
        Barang::create($this->reportData([
            'nama' => 'Kunci motor',
            'status' => 'Ditemukan',
            'kategori' => 'Aksesoris',
        ]));

        $this->get('/')
            ->assertOk()
            ->assertSee('NEMU')
            ->assertSee('Dompet')
            ->assertSee('Kunci motor');

        $this->get('/?status=Hilang')
            ->assertOk()
            ->assertSee('Daftar laporan (1)')
            ->assertSee('Dompet')
            ->assertSee('Kunci motor');
    }

    public function test_barang_report_can_be_created_updated_and_deleted(): void
    {
        $this->post(route('laporan.store'), $this->reportData())
            ->assertRedirect(route('home'));

        $barang = Barang::query()->where('nama', 'Dompet')->firstOrFail();

        $this->put(route('laporan.update', $barang), $this->reportData([
            'nama' => 'Dompet hitam',
            'status' => 'Ditemukan',
        ]))->assertRedirect(route('laporan.index'));

        $this->assertDatabaseHas('barangs', [
            'id' => $barang->id,
            'nama' => 'Dompet hitam',
            'status' => 'Ditemukan',
        ]);

        $this->delete(route('laporan.destroy', $barang))
            ->assertRedirect(route('laporan.index'));

        $this->assertDatabaseMissing('barangs', ['id' => $barang->id]);
    }

    public function test_report_forms_set_the_requested_lost_or_found_status(): void
    {
        $this->get(route('laporan.lost.create'))
            ->assertOk()
            ->assertSee('value="Hilang"', false);

        $this->get(route('laporan.found.create'))
            ->assertOk()
            ->assertSee('value="Ditemukan"', false);
    }

    public function test_login_page_is_available_from_the_nemu_navigation(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(route('login'));

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Masuk ke NEMU')
            ->assertSee('NIM UMM')
            ->assertSee('PIC UMM')
            ->assertSee('masih berupa mock');
    }

    public function test_report_requires_valid_contact_and_item_details(): void
    {
        $this->from(route('laporan.lost.create'))
            ->post(route('laporan.store'), [])
            ->assertRedirect(route('laporan.lost.create'))
            ->assertSessionHasErrors([
                'nama',
                'kategori',
                'lokasi',
                'tanggal',
                'status',
                'nama_pelapor',
                'telepon',
            ]);

        $this->assertDatabaseCount('barangs', 0);
    }

    public function test_legacy_reports_are_imported_without_creating_duplicates(): void
    {
        $source = json_decode(
            file_get_contents(base_path('legacy/db.json')),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        $expectedCount = count($source['barang']);

        $this->seed(LegacyBarangSeeder::class);
        $this->seed(LegacyBarangSeeder::class);

        $this->assertDatabaseCount('barangs', $expectedCount);
    }

    /**
     * @return array<string, string>
     */
    private function reportData(array $overrides = []): array
    {
        return array_merge([
            'nama' => 'Dompet',
            'kategori' => 'Lainnya',
            'lokasi' => 'Perpustakaan',
            'tanggal' => '2026-11-15',
            'jam' => '10:30',
            'deskripsi' => 'Dompet warna hitam.',
            'status' => 'Hilang',
            'nama_pelapor' => 'Mahasiswa NEMU',
            'telepon' => '081234567890',
            'penyimpanan' => '',
        ], $overrides);
    }
}
