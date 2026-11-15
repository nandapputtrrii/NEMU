<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;

class BarangController extends Controller
{
    public function home(Request $request): View
    {
        $query = Barang::query();
        $this->applyFilters($query, $request);

        $barangDitemukan = Barang::query()
            ->where('status', 'Ditemukan')
            ->latest('tanggal')
            ->take(3)
            ->get();

        return view('home', [
            'hasilPencarian' => $query->latest('tanggal')->get(),
            'barangDitemukan' => $barangDitemukan,
            'kategoriTersedia' => Barang::query()
                ->whereNotNull('kategori')
                ->distinct()
                ->orderBy('kategori')
                ->pluck('kategori'),
            'kataKunci' => $request->string('q')->trim()->toString(),
            'kategoriFilter' => $request->string('kategori')->trim()->toString(),
            'statusFilter' => $request->string('status')->trim()->toString(),
            'ringkasanLaporan' => [
                'Semua laporan' => Barang::query()->count(),
                'Hilang' => Barang::query()->where('status', 'Hilang')->count(),
                'Ditemukan' => Barang::query()->where('status', 'Ditemukan')->count(),
            ],
        ]);
    }

    public function index(): View
    {
        return view('barangs.index', [
            'barangs' => Barang::query()->latest('tanggal')->get(),
        ]);
    }

    public function createLost(): View
    {
        return $this->create('Hilang');
    }

    public function createFound(): View
    {
        return $this->create('Ditemukan');
    }

    public function create(string $status = 'Hilang'): View
    {
        abort_unless(in_array($status, ['Hilang', 'Ditemukan'], true), 404);

        return view('barangs.create', compact('status'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);

        if ($request->hasFile('foto')) {
            $photoPath = $request->file('foto')->store('barang', 'public');
            if ($photoPath === false) {
                throw new RuntimeException('Foto laporan gagal disimpan.');
            }
            $validated['foto'] = $photoPath;
        }

        Barang::create($validated);

        return redirect()
            ->route('home')
            ->with('success', 'Laporan barang berhasil dikirim.');
    }

    public function edit(Barang $barang): View
    {
        return view('barangs.edit', compact('barang'));
    }

    public function update(Request $request, Barang $barang): RedirectResponse
    {
        $validated = $this->validatedData($request);

        if ($request->hasFile('foto')) {
            $photoPath = $request->file('foto')->store('barang', 'public');
            if ($photoPath === false) {
                throw new RuntimeException('Foto laporan gagal disimpan.');
            }

            if ($barang->foto && ! str_starts_with($barang->foto, 'data:image/')) {
                Storage::disk('public')->delete($barang->foto);
            }

            $validated['foto'] = $photoPath;
        }

        $barang->update($validated);

        return redirect()
            ->route('laporan.index')
            ->with('success', 'Laporan barang berhasil diperbarui.');
    }

    public function destroy(Barang $barang): RedirectResponse
    {
        if ($barang->foto && ! str_starts_with($barang->foto, 'data:image/')) {
            Storage::disk('public')->delete($barang->foto);
        }

        $barang->delete();

        return redirect()
            ->route('laporan.index')
            ->with('success', 'Laporan barang berhasil dihapus.');
    }

    private function applyFilters(Builder $query, Request $request): void
    {
        $kataKunci = $request->string('q')->trim()->toString();
        $kategori = $request->string('kategori')->trim()->toString();
        $status = $request->string('status')->trim()->toString();

        if ($kataKunci !== '') {
            $query->where(function (Builder $builder) use ($kataKunci): void {
                $builder->where('nama', 'like', "%{$kataKunci}%")
                    ->orWhere('kategori', 'like', "%{$kataKunci}%")
                    ->orWhere('lokasi', 'like', "%{$kataKunci}%")
                    ->orWhere('deskripsi', 'like', "%{$kataKunci}%");
            });
        }

        if ($kategori !== '') {
            $query->where('kategori', $kategori);
        }

        if ($status !== '') {
            $query->where('status', $status);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'lokasi' => ['required', 'string', 'max:255'],
            'tanggal' => ['required', 'date'],
            'jam' => ['nullable', 'date_format:H:i'],
            'deskripsi' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', 'in:Hilang,Ditemukan'],
            'nama_pelapor' => ['required', 'string', 'max:255'],
            'telepon' => ['required', 'string', 'regex:/^(?:\+?62|0)8[0-9]{7,12}$/'],
            'penyimpanan' => ['nullable', 'string', 'max:255'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:5120'],
        ]);
    }
}
