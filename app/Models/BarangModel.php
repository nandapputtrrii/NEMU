<?php
declare(strict_types=1);

class BarangModel
{
    private string $dataPath;

    public function __construct(string $dataPath)
    {
        $this->dataPath = $dataPath;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getAll(): array
    {
        $json = file_get_contents($this->dataPath);
        if ($json === false) {
            throw new RuntimeException('File db.json tidak dapat dibaca.');
        }

        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($data) || !isset($data['barang']) || !is_array($data['barang'])) {
            throw new UnexpectedValueException('Format db.json tidak memiliki daftar barang yang valid.');
        }

        return array_values(array_filter($data['barang'], 'is_array'));
    }
}