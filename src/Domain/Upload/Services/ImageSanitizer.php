<?php

namespace Yormy\FilestoreLaravel\Domain\Upload\Services;

use Imagick;

class ImageSanitizer
{
    protected Imagick $img;

    private string $localFilePath;

    public static function make(string $localFilePath): self
    {
        $object = new self;

        $object->img = new Imagick($localFilePath);

        $object->localFilePath = $localFilePath;

        return $object;

    }

    public function saveAsPng(bool $keepOriginal = false): string
    {
        $this->stripExifHeaders();
        $this->toDowngradedPng();

        $path_info = pathinfo($this->localFilePath);

        $newFilename = $path_info['dirname'].DIRECTORY_SEPARATOR.$path_info['filename'].'.png';
        $this->saveAs($newFilename);

        if (! $keepOriginal) {
            FileDestroyer::destroy($this->localFilePath);
        }

        return $newFilename;
    }

    public function stripExifHeaders()
    {
        $profiles = $this->img->getImageProfiles('icc', true);
        $this->img->stripImage();

        if (! empty($profiles)) {
            $this->img->profileImage('icc', $profiles['icc']);
        }
    }

    public function toDowngradedPng()
    {
        $this->img->setImageFormat('png');
        $this->img->setImageCompressionQuality(90);
    }

    public function saveAs(string $path)
    {
        $this->img->writeImage($path);
        $this->img->clear();
        $this->img->destroy();
    }
}
