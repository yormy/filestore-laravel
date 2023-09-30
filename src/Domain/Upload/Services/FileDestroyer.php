<?php

declare(strict_types=1);

namespace Yormy\FilestoreLaravel\Domain\Upload\Services;

class FileDestroyer
{
    public static function destroy(string $localFilename)
    {
        $size = filesize($localFilename);

        // step 1: delete the file
        unlink($localFilename);

        // step 2: overwrite the file with new data
        $fp = fopen($localFilename, 'w+');
        fwrite($fp, str_repeat('x', $size), $size);

        // step 3: create a smaller file and overwrite to break fat alignments
        $smallerSize = (int) round($size * 0.8);
        $fp = fopen($localFilename, 'w+');
        fwrite($fp, str_repeat('t', $smallerSize), $smallerSize);

        // step 4: create a larger file and overwrite to break fat alignments
        $largerSize = (int) round($size * 1.2);
        $fp = fopen($localFilename, 'w+');
        fwrite($fp, str_repeat('t', $largerSize), $largerSize);

        // step 5: delete the dummy file
        unlink($localFilename);
    }
}
