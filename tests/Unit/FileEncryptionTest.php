<?php

namespace Yormy\FilestoreLaravel\Tests\Unit;

use Facades\Yormy\FilestoreLaravel\Domain\Encryption\FileVault;
use Illuminate\Support\Facades\Storage;
use Yormy\FilestoreLaravel\Domain\Encryption\Exceptions\DecryptionFailedException;
use Yormy\FilestoreLaravel\Tests\TestCase;
use Yormy\FilestoreLaravel\Tests\Traits\AssertEncryptionTrait;
use Yormy\FilestoreLaravel\Tests\Traits\CleanupTrait;
use Yormy\FilestoreLaravel\Tests\Traits\EncryptionTrait;
use Yormy\FilestoreLaravel\Tests\Traits\FileTrait;

class FileEncryptionTest extends TestCase
{
    use AssertEncryptionTrait;
    use CleanupTrait;
    use EncryptionTrait;
    use FileTrait;

    /**
     * @test
     *
     * @group file-encryption
     */
    public function File_ReadFile_Ok(): void
    {
        $filename = $this->testDir.'/hello.txt';
        $contents = 'hello World';
        $this->generateFile($filename, $contents);

        $this->assertReadable($filename, $contents);
    }

    /**
     * @test
     *
     * @group file-encryption
     */
    public function FileDisabledEncryption_Encrypt_CanRead(): void
    {
        config(['filestore.encryption.enabled' => false]);

        $filename = $this->testDir.'/hello.txt';
        $contents = 'hello World';
        $this->generateFile($filename, $contents);

        $filenameEncrypted = FileVault::encrypt($filename);

        $this->assertReadable($filenameEncrypted, $contents);
    }

    /**
     * @test
     *
     * @group file-encryption
     */
    public function File_EncryptCopy_EncryptedAndDecryptedFile(): void
    {
        $filename = $this->testDir.'/hello.txt';
        $contents = 'hello World';
        $this->generateFile($filename, $contents);

        $filenameEncrypted = FileVault::encryptCopy($filename);

        $this->assertEncrypted($filenameEncrypted, $contents);

        $this->assertReadable($filename, $contents);
    }

    /**
     * @test
     *
     * @group file-encryption
     */
    public function File_EncryptAsName_Oke(): void
    {
        $filename = $this->testDir.'/hello.txt';
        $contents = 'hello World';
        $this->generateFile($filename, $contents);

        $filenameEncrypted = $this->testDir.'/hello-encrypted-file.txt';
        FileVault::encrypt($filename, $filenameEncrypted);
        $this->assertEncrypted($filenameEncrypted, $contents);
    }

    /**
     * @test
     *
     * @group file-encryption
     */
    public function File_Encrypt_OriginalDeleted(): void
    {
        $filename = $this->testDir.'/hello.txt';
        $contents = 'hello World';
        $this->generateFile($filename, $contents);

        FileVault::encrypt($filename);
        $this->assertFileDoesNotExist(Storage::path($filename));
    }

    /**
     * @test
     *
     * @group file-encryption
     */
    public function EncryptedFile_ReadFile_Failed(): void
    {
        $filename = $this->testDir.'/hello.txt';
        $contents = 'hello World';
        $this->generateFile($filename, $contents);

        $filenameEncrypted = FileVault::encrypt($filename);

        $this->assertEncrypted($filenameEncrypted, $contents);
    }

    /**
     * @test
     *
     * @group file-encryption
     */
    public function EncryptedFile_DecryptAndReadFile_Ok(): void
    {
        $filename = $this->testDir.'/hello.txt';
        $contents = 'hello World';
        $this->generateFile($filename, $contents);

        $filenameEncrypted = FileVault::encrypt($filename);
        $this->assertEncrypted($filenameEncrypted, $contents);

        FileVault::decrypt($filenameEncrypted);
        $this->assertReadable($filename, $contents);
    }

    /**
     * @test
     *
     * @group file-encryption
     */
    public function EncryptedFile_DecryptAsName_Oke(): void
    {
        $filename = $this->testDir.'/hello.txt';
        $contents = 'hello World';
        $this->generateFile($filename, $contents);

        $filenameNewDecryptedName = $this->testDir.'/Hello-decrypted-file.txt';
        $filenameEncrypted = FileVault::encrypt($filename);

        FileVault::decrypt($filenameEncrypted, $filenameNewDecryptedName);

        $this->assertReadable($filenameNewDecryptedName, $contents);
    }

    /**
     * @test
     *
     * @group file-encryption
     */
    public function EncryptedFile_Decrypt_OriginalDeleted(): void
    {
        $filename = $this->testDir.'/hello.txt';
        $contents = 'hello World';
        $this->generateFile($filename, $contents);

        $filenameEncrypted = FileVault::encrypt($filename);
        FileVault::decrypt($filenameEncrypted);

        $this->assertFileDoesNotExist(Storage::path($filenameEncrypted));
    }

    /**
     * @test
     *
     * @group file-encryption
     */
    public function EncryptedFile_DecryptCopy_EncryptedAndDecryptedFile(): void
    {
        $filename = $this->testDir.'/hello.txt';
        $contents = 'hello World';
        $this->generateFile($filename, $contents);

        $filenameEncrypted = FileVault::encrypt($filename);
        $this->assertFileDoesNotExist(Storage::disk('local')->path($filename));
        $this->assertFileExists(Storage::disk('local')->path($filenameEncrypted));

        FileVault::decryptCopy($filenameEncrypted);
        $this->assertFileExists(Storage::disk('local')->path($filename));
        $this->assertFileExists(Storage::disk('local')->path($filenameEncrypted));
    }

    /**
     * @test
     *
     * @group file-encryption
     */
    public function EncryptedFile_DecryptWrongKey_Exception(): void
    {
        $filename = $this->testDir.'/hello.txt';
        $contents = 'hello World';
        $this->generateFile($filename, $contents);

        $key = $this->generateRandomKey();

        $filenameEncrypted = FileVault::key($key)->encrypt($filename);
        $this->assertEncrypted($filenameEncrypted, $contents);

        $key2 = $this->generateRandomKey();

        $this->expectException(DecryptionFailedException::class);
        FileVault::key($key2)->decryptCopy($filenameEncrypted); //????
    }

    /**
     * @test
     *
     * @group file-encryption
     */
    public function EncryptedFile_CustomKeyEncryptDecrypt_Ok(): void
    {
        $filename = $this->testDir.'/hello.txt';
        $contents = 'hello World';
        $this->generateFile($filename, $contents);

        $key = $this->generateRandomKey();

        $filenameEncrypted = FileVault::key($key)->encrypt($filename);
        $this->assertEncrypted($filenameEncrypted, $contents);

        FileVault::key($key)->decrypt($filenameEncrypted);
        $this->assertReadable($filename, $contents);
    }

    /**
     * @test
     *
     * @group file-encryption
     */
    public function EncryptedFile_DecryptStream_Ok(): void
    {
        $filename = $this->testDir.'/hello.txt';
        $contents = 'hello World';
        $this->generateFile($filename, $contents);

        $filenameEncrypted = FileVault::encrypt($filename);

        ob_start();
        FileVault::streamDecrypt($filenameEncrypted);
        $phpOutput = ob_get_contents();
        ob_end_clean();

        // Test to see if the decrypted content is sent to php://output
        $this->assertEquals(
            $contents,
            $phpOutput
        );
    }
}
