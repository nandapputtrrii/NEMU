<?php
declare(strict_types=1);

class HomeController
{
    private BarangModel $barangModel;

    public function __construct(BarangModel $barangModel)
    {
        $this->barangModel = $barangModel;
    }

    public function index(): void
    {
        $kataKunci = $this->getQueryValue('q');
        $kategoriFilter = $this->getQueryValue('kategori');
        $statusFilter = $this->getQueryValue('status');
        $barangDitemukan = [];
        $hasilPencarian = [];
        $kategoriTersedia = [];
        $ringkasanLaporan = [
            'Semua laporan' => 0,
            'Hilang' => 0,
            'Ditemukan' => 0,
        ];
        $dataError = null;

        try {
            $semuaBarang = $this->barangModel->getAll();
            $ringkasanLaporan['Semua laporan'] = count($semuaBarang);

            usort(
                $semuaBarang,
                static fn (array $a, array $b): int =>
                    strcmp((string) ($b['tanggal'] ?? ''), (string) ($a['tanggal'] ?? ''))
            );

            foreach ($semuaBarang as $barang) {
                $statusBarang = trim((string) ($barang['status'] ?? ''));
                if (strcasecmp($statusBarang, 'ditemukan') === 0) {
                    $barangDitemukan[] = $barang;
                    $ringkasanLaporan['Ditemukan']++;
                } elseif (strcasecmp($statusBarang, 'hilang') === 0) {
                    $ringkasanLaporan['Hilang']++;
                }

                $kategoriBarang = trim((string) ($barang['kategori'] ?? ''));
                if ($kategoriBarang !== '') {
                    $kategoriTersedia[strtolower($kategoriBarang)] = $kategoriBarang;
                }

                $teksBarang = implode(' ', array_map(
                    static fn (string $field): string => (string) ($barang[$field] ?? ''),
                    ['nama', 'kategori', 'lokasi', 'deskripsi', 'status']
                ));
                $cocokKataKunci = $kataKunci === '' || stripos($teksBarang, $kataKunci) !== false;
                $cocokKategori = $kategoriFilter === ''
                    || strcasecmp((string) ($barang['kategori'] ?? ''), $kategoriFilter) === 0;
                $cocokStatus = $statusFilter === ''
                    || strcasecmp((string) ($barang['status'] ?? ''), $statusFilter) === 0;

                if ($cocokKataKunci && $cocokKategori && $cocokStatus) {
                    $hasilPencarian[] = $barang;
                }
            }

            natcasesort($kategoriTersedia);
        } catch (JsonException | RuntimeException $error) {
            error_log('NEMU: gagal memuat db.json: ' . $error->getMessage());
            $dataError = 'Data barang belum dapat ditampilkan. Silakan periksa file db.json.';
        }

        $viewData = compact(
            'kataKunci',
            'kategoriFilter',
            'statusFilter',
            'barangDitemukan',
            'hasilPencarian',
            'kategoriTersedia',
            'ringkasanLaporan',
            'dataError'
        );

        require __DIR__ . '/../Views/home.php';
    }

    private function getQueryValue(string $key): string
    {
        return isset($_GET[$key]) && is_string($_GET[$key]) ? trim($_GET[$key]) : '';
    }
}