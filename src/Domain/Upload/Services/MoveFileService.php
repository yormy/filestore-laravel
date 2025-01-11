<?php

namespace Yormy\FilestoreLaravel\Domain\Upload\Services;

use Illuminate\Http\UploadedFile;

class MoveFileService
{
    private string $localFile;

    private bool $sanitize = false;

    private bool $encrypted = true;

    private $user = null;

    private bool $userEncryption = false;

    private $userEncryptionUser;

    public static function make(
        string $localFile,
    ): self {
        $object = new self;

        $object->localFile = $localFile;

        return $object;
    }

    public function sanitize(bool $sanitize = false): self
    {
        $this->sanitize = $sanitize;

        return $this;
    }

    public function encrypted(bool $encrypted = true): self
    {
        $this->encrypted = $encrypted;

        return $this;
    }

    public function forUser($user = null): self
    {
        $this->user = $user;

        return $this;
    }

    public function userEncryption($user): self
    {
        $this->userEncryption = true;
        $this->userEncryptionUser = $user;

        return $this;
    }

    public function moveToPersistent(string $destination): string
    {
        $file = new UploadedFile(
            $this->localFile,
            basename($this->localFile),
        );

        $moveAction = UploadFileService::make($file);

        if ($this->sanitize) {
            $moveAction->sanitize();
        }

        if ($this->user) {
            $moveAction->forUser($this->user);
        }

        if ($this->userEncryption) {
            $moveAction->userEncryption($this->userEncryptionUser);
        }

        if ($this->encrypted) {
            $xid = $moveAction->saveEncryptedToPersistent($destination);
        } else {
            $xid = $moveAction->saveToPersistent($destination);
        }

        unlink($this->localFile);

        return $xid;
    }
}
