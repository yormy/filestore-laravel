<?php

namespace Yormy\FilestoreLaravel\Domain\Encryption;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Yormy\FilestoreLaravel\Domain\Encryption\Exceptions\DecryptionFailedException;
use Yormy\FilestoreLaravel\Domain\Encryption\Exceptions\EncryptionFailedException;

class FileVault
{
    protected string $disk;

    protected string $key;

    protected string $cipher;

    protected LocalFilesystemAdapter|AwsS3V3Adapter $adapter;

    protected string $extension;

    public function __construct()
    {
        $this->disk = config('filestore.vault.disk');
        $this->key = config('filestore.vault.key');
        $this->cipher = config('filestore.vault.cipher');
        $this->extension = config('filestore.vault.extension');
    }

    public function disk(string $disk): self
    {
        $this->disk = $disk;

        return $this;
    }

    public function key(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public static function generateKey(): string
    {
        return random_bytes(config('filestore.vault.cipher') === 'AES-128-CBC' ? 16 : 32);
    }

    public function encrypt(
        string $sourceFile,
        string $destFile = null,
        bool $deleteSource = true,
        string $key = null,
        string $cipher = null
    ): string {
        if (! Storage::disk($this->disk)->exists($sourceFile)) {
            throw new EncryptionFailedException('Filename to encrypt does not exits: '.$sourceFile);
        }

        $this->registerServices();

        if (is_null($destFile)) {
            $destFile = $sourceFile.$this->extension;
        }

        $sourcePath = $this->getFilePath($sourceFile);
        $destPath = $this->getFilePath($destFile);

        $encrypter = $this->fileEncryptorFactory($key, $cipher);

        // If encryption is successful, delete the source file
        if ($encrypter->encrypt($sourcePath, $destPath) && $deleteSource) {
            Storage::disk($this->disk)->delete($sourceFile);
        }

        return $destFile;
    }

    private function fileEncryptorFactory(string $key = null, string $cipher = null): FileEncrypter
    {
        // Create a new encrypter instance
        if (! $key) {
            $key = $this->key;
        }
        if (! $cipher) {
            $cipher = $this->cipher;
        }

        return new FileEncrypter($key, $cipher);
    }

    public function encryptCopy(string $sourceFile, string $destFile = null): string
    {
        return self::encrypt($sourceFile, $destFile, false);
    }

    public function decrypt(
        string $sourceFile,
        string $destFile = null,
        bool $deleteSource = true,
        string $key = null,
        string $cipher = null
    ): string {
        if (! Storage::disk($this->disk)->exists($sourceFile)) {
            throw new DecryptionFailedException('Filename to decrypt does not exits: '.$sourceFile);
        }

        $this->registerServices();

        if (is_null($destFile)) {
            $destFile = Str::endsWith($sourceFile, $this->extension)
                ? Str::replaceLast($this->extension, '', $sourceFile)
                : $sourceFile;
        }

        $filePath = $this->getFilePath($sourceFile);
        $destPath = $this->getFilePath($destFile);

        $encrypter = $this->fileEncryptorFactory($key, $cipher);

        // If decryption is successful, delete the source file
        $filesize = $this->getFilesize($filePath);
        $sourcePath = $this->getSourcePath($filePath);

        if ($encrypter->decryptFile($sourcePath, $destPath, $filesize) && $deleteSource) {
            Storage::disk($this->disk)->delete($sourceFile);
        }

        return $destFile;
    }

    public function reEncrypt(string $sourceKey, string $destinationKey, string $sourceFile, $deleteSource = true): string
    {
        // decrypt
        $decryptedFile = $this->decrypt($sourceFile, null, $deleteSource, $sourceKey);

        // encrypt
        return $this->encrypt($decryptedFile, null, $deleteSource, $destinationKey);
    }

    public function decryptCopy(string $sourceFile, string $destFile = null): string
    {
        return self::decrypt($sourceFile, $destFile, false);
    }

    public function streamDecrypt(string $sourceFile, string $encryptionKey = null): string
    {
        $this->registerServices();

        $filePath = $this->getFilePath($sourceFile);

        if (! $encryptionKey) {
            $encryptionKey = $this->key;
        }
        $encrypter = new FileEncrypter($encryptionKey, $this->cipher);

        $filesize = $this->getFilesize($filePath);
        $sourcePath = $this->getSourcePath($filePath);

        return $encrypter->decryptFile($sourcePath, 'php://output', $filesize);
    }

    private function getFilesize($filePath)
    {
        if (!$this->isLocalFilesystem($this->disk)) {
            $filesize = Storage::disk($this->disk)->size($filePath); // fails local needs s3

        } else {
            $filesize = filesize($filePath);
        }

        return $filesize;
    }

    private function getSourcePath(string $filePath): string
    {
        if (! $this->isLocalFilesystem($this->disk)) {
            $sourcePath = Storage::disk($this->disk)->url($filePath);

        } else {
            $sourcePath = $filePath;
        }

        return $sourcePath;
    }

    private function isLocalFilesystem($disk)
    {
        $filesystem = config('filesystems.disks.' . $disk. '.driver');

        return $filesystem === 'local';
    }


    protected function getFilePath(string $file)
    {
        if ($this->isS3File()) {
            return "s3://{$this->adapter->getBucket()}/{$file}";
        }

        return Storage::disk($this->disk)->path($file);
    }

    protected function isS3File(): bool
    {
        return $this->adapter instanceof AwsS3Adapter;
    }

    protected function setAdapter(): void
    {
        if (isset($this->adapter) && $this->adapter) {
            return;
        }

        $this->adapter = Storage::disk($this->disk)->getAdapter();
    }

    protected function registerServices(): void
    {
        $this->setAdapter();

        if ($this->isS3File()) {
            $client = $this->adapter->getClient();
            $client->registerStreamWrapper();
        }
    }
}
