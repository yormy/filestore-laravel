<?php

declare(strict_types=1);

namespace Yormy\FilestoreLaravel\Domain\Upload\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Yormy\FilestoreLaravel\Domain\Shared\Models\FilestoreFile;

class ThumbnailService
{
    public static function resize($localdisk, $storagePath, $fileRecord)
    {
        $allVariants = [];
        $variantStoragePaths = [];
        foreach (config('filestore.variants') as $name => $specs) {
            $generatedVariant = self::resizeImage($localdisk, $storagePath, $name, $specs);
            $allVariants[] = $generatedVariant;
            $variantStoragePaths[] = dirname($storagePath).DIRECTORY_SEPARATOR.$generatedVariant['filename'];
        }

        self::updateFileRecord($fileRecord, $allVariants);

        return $variantStoragePaths;
    }

    private static function updateFileRecord(FilestoreFile $fileRecord, array $allVariants)
    {
        $currentVariants = [];
        if ($fileRecord->variants) {
            $currentVariants = json_decode($fileRecord->variants, true);
        }

        $newVariants = array_merge($currentVariants, $allVariants);
        $fileRecord->variants = $newVariants;
        $fileRecord->save();
    }

    public static function resizeImage(string $localdisk, string $storagePath, string $sizeName, array $specs)
    {
        $fullPath = Storage::disk($localdisk)->path($storagePath);

        $manager = new ImageManager(new Driver);

        $imageObject = $manager->read($fullPath);
        if (! $imageObject) {
            return;
        }

        $imageObject->resize($specs['width'], $specs['height'], function ($constraint) {
            $constraint->aspectRatio();
        });

        $filename = basename($fullPath);
        $dirname = dirname($fullPath).DIRECTORY_SEPARATOR;
        $dirname .= self::getVariantsDirectory($filename, $sizeName);

        @mkdir($dirname, 0755, true);

        $filename = self::addFilenamePostfix($filename, "-$sizeName");
        $x = $imageObject->save($dirname.$filename);

        $variant = [
            'name' => $sizeName,
            'height' => $specs['height'],
            'width' => $specs['width'],
            'filename' => self::getVariantsDirectory($filename, $sizeName).$filename,
        ];

        return $variant;
    }

    private static function getVariantsDirectory(string $filename, string $sizeName): string
    {
        $variantsDirectoryName = pathinfo($filename, PATHINFO_FILENAME);
        $postfixSize = '-'.$sizeName;
        if (str_ends_with($variantsDirectoryName, $postfixSize)) {
            $variantsDirectoryName = Str::replaceLast($postfixSize, '', $variantsDirectoryName);
        }

        return 'variants'.DIRECTORY_SEPARATOR. $variantsDirectoryName. DIRECTORY_SEPARATOR;
    }

    private static function addFilenamePostfix(string $filename, string $postfix): string
    {
        $pathinfo = pathinfo($filename);

        $newName = '';
        if ($pathinfo['dirname'] && $pathinfo['dirname'] !== '.') {
            $newName = $pathinfo['dirname'].DIRECTORY_SEPARATOR;
        }
        $newName .= $pathinfo['filename'].$postfix.'.'.$pathinfo['extension'];

        return $newName;
    }

    //    private function convertPdfIntoImg()
    //    {
    //        $pdf = new SpatiePdf($fullPath);
    //        $pdf->getNumberOfPages();
    //
    //        $pdf->saveImage($fullPath .'.png');
    //    }
}
