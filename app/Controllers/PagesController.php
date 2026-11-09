<?php
declare(strict_types=1);

class PagesController
{
    /**
     * @var array<string, string>
     */
    private const VIEWS = [
        'login' => 'login.php',
        'nemu' => 'nemu.php',
        'lapor-hilang' => 'lapor-hilang.php',
        'lapor-temuan' => 'lapor-temuan.php',
    ];

    public function show(string $page): void
    {
        if (!isset(self::VIEWS[$page])) {
            http_response_code(404);
            throw new InvalidArgumentException('Halaman tidak ditemukan.');
        }

        require __DIR__ . '/../Views/pages/' . self::VIEWS[$page];
    }
}
