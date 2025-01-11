<?php

namespace Yormy\FilestoreLaravel\Tests\Unit;

use Yormy\FilestoreLaravel\Domain\Encryption\FileVault;
use Yormy\FilestoreLaravel\Domain\Encryption\Exceptions\DecryptionFailedException;
use Yormy\FilestoreLaravel\Tests\TestCase;
use Yormy\FilestoreLaravel\Tests\Traits\AssertEncryptionTrait;
use Yormy\FilestoreLaravel\Tests\Traits\CleanupTrait;
use Yormy\FilestoreLaravel\Tests\Traits\EncryptionTrait;
use Yormy\FilestoreLaravel\Tests\Traits\FileTrait;

class FileReEncryptTest extends TestCase
{
    use AssertEncryptionTrait;
    use CleanupTrait;
    use EncryptionTrait;
    use FileTrait;

    /**
     * @test
     *
     * @group file-encrypt
     */
    public function EncryptedFile_Reencrypt_DecryptWithSourceKey_Exception(): void
    {
        $filename = $this->testDir.'/hello.txt';
        $contents = 'hello World';
        $this->generateFile($filename, $contents);

        $keySource = $this->generateRandomKey();
        $keyNew = $this->generateRandomKey();

        $filenameEncrypted = (new FileVault())->key($keySource)->encrypt($filename);
        $this->assertEncrypted($filenameEncrypted, $contents);

        $filenameReencrypted = (new FileVault())->reEncrypt($keySource, $keyNew, $filenameEncrypted);
        $this->assertEncrypted($filenameReencrypted, $contents);

        $this->expectException(DecryptionFailedException::class);
        (new FileVault())->key($keySource)->decrypt($filenameEncrypted);
    }

    /**
     * @test
     *
     * @group file-encrypt
     */
    public function EncryptedFile_Reencrypt_DecryptWithDestinationKey_Success(): void
    {
        $filename = $this->testDir.'/hello.txt';
        $contents = 'hello World';
        $this->generateFile($filename, $contents);

        $keySource = $this->generateRandomKey();
        $keyNew = $this->generateRandomKey();

        $filenameEncrypted = (new FileVault())->key($keySource)->encrypt($filename);
        $this->assertEncrypted($filenameEncrypted, $contents);

        $filenameReencrypted = (new FileVault())->reEncrypt($keySource, $keyNew, $filenameEncrypted);
        $this->assertEncrypted($filenameReencrypted, $contents);

        $filenameDecrypted = (new FileVault())->key($keyNew)->decrypt($filenameEncrypted);
        $this->assertReadable($filenameDecrypted, $contents);
    }
}
