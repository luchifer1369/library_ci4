<?php

namespace App\Libraries;

class PdfConverter
{
    /**
     * Convert PDF to PNG images per page.
     *
     * @return list<array{page_number: int, image_path: string}>
     */
    public function convertToPng(string $pdfPath, string $outputDir, int $bookId): array
    {
        if (!is_file($pdfPath)) {
            throw new \RuntimeException('File PDF tidak ditemukan: ' . $pdfPath);
        }

        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $ghostscript = $this->findGhostscript();
        if ($ghostscript !== null) {
            return $this->convertWithGhostscript($ghostscript, $pdfPath, $outputDir, $bookId);
        }

        if (extension_loaded('imagick')) {
            return $this->convertWithImagick($pdfPath, $outputDir, $bookId);
        }

        throw new \RuntimeException($this->ghostscriptInstallMessage());
    }

    /**
     * @return list<array{page_number: int, image_path: string}>
     */
    private function convertWithGhostscript(string $gsBinary, string $pdfPath, string $outputDir, int $bookId): array
    {
        $outputPattern = rtrim($outputDir, '/\\') . DIRECTORY_SEPARATOR . "book_{$bookId}_page_%d.png";

        $command = sprintf(
            '"%s" -dNOPAUSE -dBATCH -dSAFER -sDEVICE=png16m -r150 -sOutputFile="%s" "%s" 2>&1',
            $gsBinary,
            $outputPattern,
            $pdfPath
        );

        $output     = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \RuntimeException(
                'Ghostscript gagal mengonversi PDF: ' . implode("\n", $output)
            );
        }

        $files = glob(rtrim($outputDir, '/\\') . DIRECTORY_SEPARATOR . "book_{$bookId}_page_*.png") ?: [];
        natsort($files);

        if ($files === []) {
            throw new \RuntimeException('Konversi selesai tetapi tidak ada gambar halaman yang dihasilkan.');
        }

        $pages      = [];
        $pageNumber = 1;

        foreach ($files as $file) {
            $pages[] = [
                'page_number' => $pageNumber,
                'image_path'  => 'uploads/pages/' . basename($file),
            ];
            $pageNumber++;
        }

        return $pages;
    }

    /**
     * @return list<array{page_number: int, image_path: string}>
     */
    private function convertWithImagick(string $pdfPath, string $outputDir, int $bookId): array
    {
        $imagick = new \Imagick();
        $imagick->setResolution(150, 150);

        try {
            $imagick->readImage($pdfPath);
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'gswin') || str_contains($e->getMessage(), 'Ghostscript')) {
                throw new \RuntimeException($this->ghostscriptInstallMessage(), 0, $e);
            }
            throw $e;
        }

        $imagick->setImageBackgroundColor('white');
        $imagick = $imagick->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);

        $pages      = [];
        $pageNumber = 1;
        $numPages   = $imagick->getNumberImages();

        for ($i = 0; $i < $numPages; $i++) {
            $imagick->setIteratorIndex($i);
            $imagick->setImageFormat('png');

            $filename = "book_{$bookId}_page_{$pageNumber}.png";
            $fullPath = rtrim($outputDir, '/\\') . DIRECTORY_SEPARATOR . $filename;

            $imagick->writeImage($fullPath);

            $pages[] = [
                'page_number' => $pageNumber,
                'image_path'  => 'uploads/pages/' . $filename,
            ];

            $pageNumber++;
        }

        $imagick->clear();
        $imagick->destroy();

        return $pages;
    }

    private function findGhostscript(): ?string
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $patterns = [
                'C:\\Program Files\\gs\\gs*\\bin\\gswin64c.exe',
                'C:\\Program Files (x86)\\gs\\gs*\\bin\\gswin32c.exe',
            ];

            foreach ($patterns as $pattern) {
                $matches = glob($pattern) ?: [];
                rsort($matches);
                foreach ($matches as $path) {
                    if (is_file($path)) {
                        return $path;
                    }
                }
            }

            $fromPath = shell_exec('where gswin64c 2>nul');
            if (is_string($fromPath) && trim($fromPath) !== '') {
                $line = trim(explode("\n", trim($fromPath))[0]);
                if (is_file($line)) {
                    return $line;
                }
            }

            return null;
        }

        $candidates = ['gs', '/usr/bin/gs', '/usr/local/bin/gs'];
        foreach ($candidates as $binary) {
            $path = shell_exec('command -v ' . escapeshellarg($binary) . ' 2>/dev/null');
            if (is_string($path) && trim($path) !== '' && is_file(trim($path))) {
                return trim($path);
            }
        }

        return null;
    }

    private function ghostscriptInstallMessage(): string
    {
        return 'Ghostscript belum terinstall. Imagick membutuhkan Ghostscript untuk membaca PDF. '
            . 'Download & install dari https://ghostscript.com/releases/gsdnld.html (pilih Windows 64-bit), '
            . 'centang "Add Ghostscript to PATH", lalu restart Apache di XAMPP. '
            . 'Atau jalankan di terminal: winget install ArtifexSoftware.GhostScript';
    }
}
