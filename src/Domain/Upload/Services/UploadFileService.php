<?php

namespace Yormy\FilestoreLaravel\Domain\Upload\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yormy\FilestoreLaravel\Domain\Encryption\FileVault;
use Yormy\FilestoreLaravel\Domain\Shared\Enums\FileEncryptionExtension;
use Yormy\FilestoreLaravel\Domain\Shared\Models\FilestoreFile;
use Yormy\FilestoreLaravel\Domain\Shared\Repositories\FilestoreFileRepository;
use Yormy\FilestoreLaravel\Domain\Upload\DataObjects\UploadedFileData;
use Yormy\FilestoreLaravel\Domain\Upload\Jobs\MoveFileToPersistentDiskJob;

class UploadFileService
{
    private string $rootPath = 'uploads';

    private string $localDisk;

    private bool $isEncrypted = false;

    private array $allowedMimes = [];

    private ?int $maxFileSizeKb = null;

    private bool $sanitizeImage = false;

    private bool $makeVariants = true;

    private bool $withPdfPages = false;

    private bool $allowPdfEmbedding = true;

    private bool $accessLog = true;

    private bool $userEncryption = false;

    private $userEncryptionUser;

    private UploadedFileData $newUploadedFileNew;

    private $user;

    public static function make(
        UploadedFile $uploadedFile,
        array $allowedMimes = [],
        ?int $maxFileSizeKb = null,
    ): self {
        $object = new self;

        $object->allowedMimes = config('filestore.allowed_mimes');
        if (! empty($allowedMimes)) {
            $object->allowedMimes = $allowedMimes;
        }

        $object->maxFileSizeKb = config('filestore.max_file_size_kb');
        if ($maxFileSizeKb) {
            $object->maxFileSizeKb = $maxFileSizeKb;
        }

        $object->newUploadedFileNew = new UploadedFileData($uploadedFile, $object->maxFileSizeKb, $object->allowedMimes);

        $object->localDisk = config('filestore.storage.local.disk');

        return $object;
    }

    public function withoutVariants(): self
    {
        $this->makeVariants = false;

        return $this;
    }

    public function withPdfPages(): self
    {
        $this->withPdfPages = true;

        return $this;
    }

    public function preventPdfEmbedding(): self
    {
        $this->allowPdfEmbedding = false;

        return $this;
    }

    public function withoutAccessLog(): self
    {
        $this->accessLog = false;

        return $this;
    }

    public function userEncryption($user): self
    {
        $this->userEncryption = true;
        $this->userEncryptionUser = $user;

        return $this;
    }

    public function forUser($user): self
    {
        $this->user = $user;

        return $this;
    }

    public function sanitize(bool $sanitize = true): self
    {
        $this->sanitizeImage = $sanitize;

        return $this;
    }

    public function toArray(string $filepath): array
    {
        $data = $this->newUploadedFileNew->toArray();

        if ($this->user) {
            $data['user_id'] = $this->user->id;
            $data['user_type'] = get_class($this->user);
        }
        $data['disk'] = $this->localDisk;
        $data['is_encrypted'] = $this->isEncrypted;
        $data['path'] = dirname($filepath);
        $data['filename'] = basename($filepath);

        return $data;
    }

    private function createRecord()
    {
        $filestoreFileRepository = new FilestoreFileRepository;
        $fileRecord = $filestoreFileRepository->create([
            'allow_pdf_embedding' => $this->allowPdfEmbedding,
            'access_log' => $this->accessLog,
            'user_encryption' => $this->userEncryption,
        ]);

        return $fileRecord;
    }

    private function updateRecord(FilestoreFile $filestoreFile, string $filename)
    {
        $filestoreFileRepository = new FilestoreFileRepository;

        $data = $this->toArray($filename);

        $filestoreFileRepository->update($filestoreFile, $data);

        return $filestoreFile;
    }

    public function saveToLocal(string $path): string
    {
        $fileRecord = $this->createRecord();
        $savedFiles = $this->save($path, $fileRecord);

        $this->updateRecord($fileRecord, $savedFiles['mainfile']);

        return $fileRecord->xid;
    }

    public function saveToPersistent(string $path): string
    {
        $fileRecord = $this->createRecord();
        $savedFiles = $this->save($path, $fileRecord);
        $this->updateRecord($fileRecord, $savedFiles['mainfile']);

        $this->moveToPersistent($fileRecord, $savedFiles);

        return $fileRecord->xid;
    }

    public function saveEncryptedToLocal(string $path, ?string $encryptionKey = null): string
    {
        $fileRecord = $this->createRecord();
        $savedFiles = $this->saveEncrypted($path, $fileRecord, $encryptionKey);
        $this->updateRecord($fileRecord, $savedFiles['mainfile']);

        return $fileRecord->xid;
    }

    public function saveEncryptedToPersistent(string $path, ?string $encryptionKey = null): string
    {
        $fileRecord = $this->createRecord();
        $savedFiles = $this->saveEncrypted($path, $fileRecord, $encryptionKey);

        $this->updateRecord($fileRecord, $savedFiles['mainfile']);

        $this->moveToPersistent($fileRecord, $savedFiles);

        return $fileRecord->xid;
    }

    private function moveToPersistent(FilestoreFile $fileRecord, array $encryptedFilenames)
    {
        $filesToMove[] = $encryptedFilenames['mainfile'];
        if (isset($encryptedFilenames['variants'])) {
            $filesToMove = array_merge($filesToMove, $encryptedFilenames['variants']);
        }

        foreach ($filesToMove as $fileToMove) {
            dispatch(new MoveFileToPersistentDiskJob($fileRecord, $fileToMove));
        }
    }

    private function saveDimensions(string $storageFilename, FilestoreFile $fileRecord): void
    {
        $fullPath = Storage::disk($this->localDisk)->path($storageFilename);

        $data = getimagesize($fullPath);
        if (isset($data[0]) && isset($data[1])) {
            $fileRecord->width = $data[0];
            $fileRecord->height = $data[1];
            $fileRecord->save();
        }
    }

    private function save(string $path, FilestoreFile $fileRecord): array
    {
        $storePath = $this->rootPath.DIRECTORY_SEPARATOR.$path;

        $storePath .= DIRECTORY_SEPARATOR;

        $mainfile = Storage::disk($this->localDisk)->putFileAs($storePath, $this->newUploadedFileNew->getFile(), $this->generateFileName());

        $this->saveDimensions($mainfile, $fileRecord);

        if ($this->sanitizeImage) {
            if ($this->newUploadedFileNew->canSanitize()) {
                $fullPath = Storage::disk($this->localDisk)->path($mainfile);
                ImageSanitizer::make($fullPath)->saveAsPng();

                $path_info = pathinfo($mainfile);
                $mainfile = $path_info['dirname'].DIRECTORY_SEPARATOR.$path_info['filename'].'.png';
            }
        }

        $variantStoragePath = [];
        if ($this->newUploadedFileNew->isPdf()) {
            $variantStoragePath = $this->processPdf($fileRecord, $mainfile);
        }

        if ($this->newUploadedFileNew->canCreateThumbnail() && $this->makeVariants) {
            $variantStoragePath = ThumbnailService::resize($this->localDisk, $mainfile, $fileRecord);
        }

        return [
            'mainfile' => $mainfile,
            'variants' => $variantStoragePath,
        ];
    }

    private function processPdf($fileRecord, string $mainfile): array
    {
        $fileRecord->total_pages = PdfImageService::pageCount($this->localDisk, $mainfile);
        $previewImage = PdfImageService::createPreview($this->localDisk, $mainfile);

        (new FilestoreFileRepository)->setPreviewFilename($fileRecord, $previewImage);

        $this->saveDimensions($previewImage, $fileRecord);

        $pageImages = [];
        if ($this->withPdfPages) {
            $pageImages = PdfImageService::createImagePages($this->localDisk, $mainfile);
        }

        if ($this->makeVariants) {
            $variantStoragePath = ThumbnailService::resize($this->localDisk, $previewImage, $fileRecord);
            $variantStoragePath[] = $previewImage;
            $variantStoragePath = array_merge($variantStoragePath, $pageImages);
        }

        return $variantStoragePath;
    }


    public function saveEncrypted(string $path, FilestoreFile $fileRecord, ?string $encryptionKey = null): array
    {
        $this->isEncrypted = true;

        $unencryptedFiles = $this->save($path, $fileRecord);

        $encryptedFiles = [];
        $unencryptedFile = $unencryptedFiles['mainfile'];
        $encryptedFiles['mainfile'] = $this->customEncrypt($encryptionKey, $unencryptedFile);

        foreach ($unencryptedFiles['variants'] as $key => $unencryptedFile) {
            $encryptedFiles['variants'][$key] = $this->customEncrypt($encryptionKey, $unencryptedFile);
        }

        return $encryptedFiles;
    }

    private function getKey(?string $encryptionKey): string
    {
        if ($encryptionKey) {
            return $encryptionKey;
        }

        $encryptionKey = config('filestore.vault.key');

        if ($this->userEncryption) {
            $userKeyResolverClass = config('filestore.resolvers.user_key_resolver');
            $userKeyResolver = new $userKeyResolverClass;

            $userKey = $userKeyResolver->get($this->userEncryptionUser);
            $encryptionKey = $userKey;
        }

        return $encryptionKey;
    }

    private function customEncrypt($encryptionKey, $unencryptedFile): string
    {
        // ENCRYPTION PASS 1 : either with system or supplied key
        if (! $encryptionKey) {
            $encryptionKey = config('filestore.vault.key');
        }
        $encryptedFile = (new FileVault)->key($encryptionKey)->encrypt($unencryptedFile);

        // ENCRYPTION PASS 2 : user encryption key
        if ($this->userEncryption) {

            $userKeyResolverClass = config('filestore.resolvers.user_key_resolver');
            $userKeyResolver = new $userKeyResolverClass;

            $userKey = $userKeyResolver->get($this->userEncryptionUser);

            $encryptionPass1 = $encryptedFile;
            $encryptedFile = (new FileVault)->key($userKey)->encrypt(
                sourceFile: $encryptionPass1,
                extension: FileEncryptionExtension::SYSTEMUSER
            );
        }

        return $encryptedFile;
    }

    private function generateFileName(): string
    {
        $random = Str::random(30);

        return $this->newUploadedFileNew->getSanitizedName().'-'.$random.'.'.$this->newUploadedFileNew->getExtension();
    }
}
