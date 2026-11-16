<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\User;
use Database\Seeders\LegacyBarangSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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
        $this->actingAs(User::factory()->create());

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
        $this->actingAs(User::factory()->create());

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
            ->assertSee('label for="nim">NIM</label>', false)
            ->assertSee('name="password"', false)
            ->assertSee(route('login.store'))
            ->assertSee(route('register'));

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Buat akun')
            ->assertSee('password_confirmation');
    }

    public function test_report_requires_valid_contact_and_item_details(): void
    {
        $this->actingAs(User::factory()->create());

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

    public function test_create_and_update_require_description_and_limit_name_to_100_characters(): void
    {
        $this->actingAs(User::factory()->create());

        $validData = $this->reportData();
        $tooLongName = str_repeat('a', 101);

        $this->from(route('laporan.lost.create'))
            ->post(route('laporan.store'), array_merge($validData, [
                'deskripsi' => '',
            ]))
            ->assertRedirect(route('laporan.lost.create'))
            ->assertSessionHasErrors(['deskripsi']);

        $this->from(route('laporan.lost.create'))
            ->post(route('laporan.store'), array_merge($validData, [
                'nama' => $tooLongName,
            ]))
            ->assertRedirect(route('laporan.lost.create'))
            ->assertSessionHasErrors(['nama']);

        $barang = Barang::create($validData);

        $this->from(route('laporan.edit', $barang))
            ->put(route('laporan.update', $barang), array_merge($validData, [
                'deskripsi' => '',
            ]))
            ->assertRedirect(route('laporan.edit', $barang))
            ->assertSessionHasErrors(['deskripsi']);

        $this->from(route('laporan.edit', $barang))
            ->put(route('laporan.update', $barang), array_merge($validData, [
                'nama' => $tooLongName,
            ]))
            ->assertRedirect(route('laporan.edit', $barang))
            ->assertSessionHasErrors(['nama']);

        $this->assertSame('Dompet', $barang->fresh()->nama);
        $this->assertSame('Dompet warna hitam.', $barang->fresh()->deskripsi);
    }

    public function test_registration_stores_a_hashed_password_and_authenticates_the_user(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Nanda',
            'nim' => '20240001',
            'password' => 'password-aman',
            'password_confirmation' => 'password-aman',
        ])->assertRedirect(route('home'));

        $user = User::query()->where('nim', '20240001')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check('password-aman', $user->password));
        $this->assertNotSame('password-aman', $user->password);
    }

    public function test_login_rejects_unknown_credentials_and_accepts_a_registered_account(): void
    {
        $user = User::factory()->create([
            'nim' => '20240002',
            'password' => 'password-aman',
        ]);

        $this->post(route('login.store'), [
            'nim' => 'nim-tidak-terdaftar',
            'password' => 'sembarang',
        ])->assertSessionHasErrors('nim');

        $this->assertGuest();

        $this->post(route('login.store'), [
            'nim' => '20240002',
            'password' => 'password-aman',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_report_crud_requires_authentication_and_logout_ends_the_session(): void
    {
        $this->get(route('laporan.index'))
            ->assertRedirect(route('login'));

        $this->get(route('laporan.lost.create'))
            ->assertRedirect(route('login'));

        $user = User::factory()->create();
        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest();
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
