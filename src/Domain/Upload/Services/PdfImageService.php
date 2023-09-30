<?php

declare(strict_types=1);

namespace Yormy\FilestoreLaravel\Domain\Upload\Services;

use Illuminate\Support\Facades\Storage;
use Spatie\PdfToImage\Pdf as SpatiePdf;

class PdfImageService
{
    const PATH_PAGES = 'pages';

    public static function pageCount(string $localdisk, string $storagePath): int
    {
        $fullPath = Storage::disk($localdisk)->path($storagePath);

        $pdf = new SpatiePdf($fullPath);

        return $pdf->getNumberOfPages();
    }

    public static function createPreview(string $localdisk, string $storagePath): string
    {
        $fullPath = Storage::disk($localdisk)->path($storagePath);

        $pdf = new SpatiePdf($fullPath);

        $pdf->saveImage($fullPath.'.png');

        return $storagePath.'.png';

    }

    public static function createImagePages(string $localdisk, string $storagePath): array
    {
        $fullPath = Storage::disk($localdisk)->path($storagePath);

        $pdf = new SpatiePdf($fullPath);
        $pageCount = $pdf->getNumberOfPages();

        $pages = [];
        $extension = '.png';
        $i = 1;
        while ($i <= $pageCount) {

            $fullPathPage = self::buildPageFilePath($fullPath, $i);

            $pdf->setPage($i)->saveImage($fullPathPage.$extension);

            $storagePathPage = self::buildPageStoragePath($storagePath, $i);

            $pages[] = $storagePathPage.$extension;
            $i++;
        }

        return $pages;
    }

    private static function buildPageStoragePath(string $storagePath, int $pageNr): string
    {
        $fullPathPages = dirname($storagePath).DIRECTORY_SEPARATOR.self::PATH_PAGES;
        @mkdir($fullPathPages);

        return $fullPathPages.DIRECTORY_SEPARATOR.basename($storagePath).self::createFilename($pageNr);
    }

    private static function buildPageFilePath(string $fullPath, int $pageNr): string
    {
        $fullPathPages = dirname($fullPath).DIRECTORY_SEPARATOR.self::PATH_PAGES;
        @mkdir($fullPathPages);

        return $fullPathPages.DIRECTORY_SEPARATOR.basename($fullPath).self::createFilename($pageNr);
    }

    public static function createFilename(int $pageNr): string
    {
        return '-'.str_pad((string) $pageNr, 4, '0', STR_PAD_LEFT);
    }
}
