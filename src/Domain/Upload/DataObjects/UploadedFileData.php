<?php

declare(strict_types=1);

namespace Yormy\FilestoreLaravel\Domain\Upload\DataObjects;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Yormy\FilestoreLaravel\Domain\Upload\DataObjects\Enums\MimeTypeEnum;

class UploadedFileData
{
    public function __construct(
        private readonly UploadedFile $uploadedFile,
        private readonly int $maxFileSizeKb,
        private readonly array $allowedMimes,
    ) {
        $this->validate();
    }

    public function getFile(): UploadedFile
    {
        return $this->uploadedFile;
    }

    public function toArray(): array
    {
        return [
            'original_filename' => $this->getClientOriginalName(),
            'original_extension' => $this->getClientOriginalExtension(),
            'size_kb' => $this->getSizeInKiloBytes(),
            'mime' => $this->getMimeType(),
        ];
    }

    public function getSanitizedName(): string
    {
        $originalName = $this->getClientOriginalName();

        return preg_replace('/[^A-Za-z0-9-_]/', '', $originalName);
    }

    public function getClientOriginalName(): string
    {
        return $this->uploadedFile->getClientOriginalName();
    }

    public function isImage(): bool
    {
        $mimetypes = [
            MimeTypeEnum::ImageJpeg,
            MimeTypeEnum::ImageGif,
            MimeTypeEnum::ImagePng,
            MimeTypeEnum::ImageBmp,
        ];

        return $this->isInMimeTypes($mimetypes);
    }

    public function canSanitize(): bool
    {
        $mimetypes = [
            MimeTypeEnum::ImageJpeg,
        ];

        return $this->isInMimeTypes($mimetypes);
    }

    public function isPdf(): bool
    {
        $mimetypes = [
            MimeTypeEnum::ApplicationPdf,
        ];

        return $this->isInMimeTypes($mimetypes);
    }

    public function canCreateThumbnail(): bool
    {
        $mimetypes = [
            MimeTypeEnum::ImageJpeg,
            MimeTypeEnum::ImageGif,
            MimeTypeEnum::ImagePng,
            MimeTypeEnum::ImageBmp,
        ];

        return $this->isInMimeTypes($mimetypes);
    }

    private function validate()
    {
        $mimeTypesAllowed = $this->getMimeTypesForDisplay();

        if (! $mimeAllowed = $this->isMimeAllowed()) {
            throw ValidationException::withMessages([
                'file' => __('filestore::validation.error.filetype_not_allowed', [
                    'file' => $this->getClientOriginalName(),
                    'mimes_allowed' => $mimeTypesAllowed,
                ]),
            ]);
        }

        if (! $sizeAllowed = $this->isFileSizeAllowed()) {
            throw ValidationException::withMessages([
                'file' => __('filestore::validation.error.file_too_large', [
                    'max_file_size' => $this->maxFileSizeKb,
                    'file' => $this->getClientOriginalName(),
                ]),
            ]);
        }
    }

    private function isFileSizeAllowed(): bool
    {
        return $this->maxFileSizeKb >= $this->getSizeInKiloBytes();
    }

    private function isMimeAllowed(): bool
    {
        return $this->isInMimeTypes($this->allowedMimes);
    }

    private function getSizeInBytes(): int
    {
        return $this->uploadedFile->getSize();
    }

    private function getMimeTypesForDisplay(): string
    {
        $display = [];
        foreach ($this->allowedMimes as $mimeType) {
            $display[] = $mimeType->getExt();
        }

        return implode(',', $display);
    }

    private function getSizeInKiloBytes(): int
    {
        return (int) ($this->getSizeInBytes() / 1024);
    }

    private function getClientOriginalExtension(): string
    {
        return $this->uploadedFile->getClientOriginalExtension();
    }

    public function getExtension(): string
    {
        // sometimes text files are octead streams / bin files. keep the txt extension
        $mimeExtension = $this->uploadedFile->guessExtension();
        $fileExtension = $this->uploadedFile->getExtension();
        if ($mimeExtension === 'bin' && strtoupper($fileExtension) === 'TXT') {
            return $fileExtension;
        }

        return $this->uploadedFile->guessExtension(); // safe version based on content itself
    }

    private function getMimeType(): string
    {
        $mimetype = $this->uploadedFile->getMimeType(); // safe version based on content itself

        // some versions of plain text have octedstream as mime
        $extension = strtoupper($this->uploadedFile->getExtension());
        if ($mimetype === MimeTypeEnum::ApplicationOctetStream->value && $extension === 'TXT') {
            return MimeTypeEnum::TextPlain->value;
        }

        return $mimetype;
    }

    private function isInMimeTypes(array $mimeTypes): bool
    {
        $allowed = false;
        $mimeType = $this->getMimeType();

        foreach ($mimeTypes as $mime) {
            if ($mimeType === $mime->value) {
                $allowed = true;
            }
        }

        return $allowed;
    }
}
