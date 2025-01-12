<?php

declare(strict_types=1);

namespace Yormy\FilestoreLaravel\Tests\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait FileTrait
{
    private string $originalFilePath = __DIR__.'/../Setup/Files/';

    protected string $testDir = 'encryption';

    protected function generateFile(string $filename, string $contents)
    {
        return Storage::disk('local')->put($filename, $contents);
    }

    protected function getOriginalFilepath(string $filename): string
    {
        return $this->originalFilePath.$filename;
    }

    protected function getFileContent(string $filename)
    {
        return file_get_contents($this->getOriginalFilepath($filename));
    }

    private function buildFile(string $filename)
    {
        return new UploadedFile($this->getOriginalFilepath($filename), $filename);
    }

    protected function getUploadedFilePath(string $filePath): string
    {
        return __DIR__.'/../Setup/Storage/'.$filePath;
    }

    protected function storeDownloadLocal($downloadedContent, string $originalFilename): string
    {
        $filePath = 'downloads/'.$originalFilename;

        Storage::disk('local')->put($filePath, $downloadedContent);

        return __DIR__.'/../Setup/Storage/encryption/'.$filePath;
    }

    private function createFile(int $sizeInKb = 1024)
    {
        $contents = '';

        $size = 1024 * $sizeInKb;
        while ($size > 0) {
            $data = Str::random(10).' ';
            $contents .= $data;
            $size -= strlen($data);
        }

        $filename = "generated_$sizeInKb.txt";
        Storage::disk('local')->put($filename, $contents);

        return Storage::disk('local')->path($filename);
    }

    private function createUploadedFile(int $sizeInKb = 1024)
    {
        $contents = '';

        $size = 1024 * $sizeInKb;
        $data = '1234567 ';
        while ($size > 0) {
            $contents .= $data;
            $size -= strlen($data);
        }

        $filename = "generated_$sizeInKb.txt";
        Storage::disk('local')->put($filename, $contents);
        $filename = Storage::disk('local')->path($filename);

        // why does this create a octedstream instead of plain text
        return new UploadedFile($filename, $filename);

        //        $size = 1024 * $sizeInKb;
        //
        //        $data = "1234567 ";
        //        $filename = __DIR__ .'/../Setup/Storage/encryption/'. "generated_$sizeInKb.txt";
        //        $fh = fopen($filename, 'w');
        //        while ($size > 0) {
        //            fwrite($fh,$data);
        //            $size -= strlen($data);
        //        }
        //        fclose($fh);
        //
        //        return new UploadedFile($filename, $filename);
    }

    private function writeFile(int $sizeInKb = 1024)
    {
        $size = 1024 * 1024 * $sizeInKb;

        $data = '1234567 ';
        $filename = __DIR__.'/../Setup/Storage/'."generated_$sizeInKb.txt";
        $fh = fopen($filename, 'w');
        while ($size > 0) {
            fwrite($fh, $data);
            $size -= strlen($data);
        }
        fclose($fh);

        return new UploadedFile($filename, $filename);
    }

    private function getLocalFilename($filename): string
    {
        $localPath = $this->getOriginalFilepath($filename);

        // make a copy to work with, and can test deletion
        $filename = 'localcopy-'.$filename;
        Storage::disk('local')->put($filename, file_get_contents($localPath));

        return Storage::disk('local')->path($filename);
    }
}
