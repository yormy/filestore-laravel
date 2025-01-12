<?php

namespace Yormy\FilestoreLaravel\Tests\Feature\Main;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Yormy\FilestoreLaravel\Domain\Shared\Models\FilestoreFileAccess;
use Yormy\FilestoreLaravel\Domain\Upload\Services\UploadFileService;
use Yormy\FilestoreLaravel\Exceptions\InvalidValueException;
use Yormy\FilestoreLaravel\Tests\Setup\Models\User;
use Yormy\FilestoreLaravel\Tests\TestCase;
use Yormy\FilestoreLaravel\Tests\Traits\AssertDownloadTrait;
use Yormy\FilestoreLaravel\Tests\Traits\AssertEncryptionTrait;
use Yormy\FilestoreLaravel\Tests\Traits\AssertImgTrait;
use Yormy\FilestoreLaravel\Tests\Traits\CleanupTrait;
use Yormy\FilestoreLaravel\Tests\Traits\FileTrait;
use Yormy\FilestoreLaravel\Tests\Traits\UserTrait;

class FileDownloadTest extends TestCase
{
    use AssertDownloadTrait;
    use AssertImgTrait;
    use AssertEncryptionTrait;
    use CleanupTrait;
    use FileTrait;
    use UserTrait;

    //
    //        $downloadedContent = $response->streamedContent();
    //        $this->assertTrue($response->headers->get('content-type') == 'image/png');
    //        //$response->assertHeader('Content-Disposition', 'attachment; filename=users.txt');
    //        // $this->assertTrue($response->headers->get('content-disposition') == 'attachment; filename=actions-'.date('d-m-Y').'.csv');

    /**
     * @test
     *
     * @group file-download
     */
    public function uploaded_unencrypted_local_stream_correct(): void
    {
        $filename = 'sylvester.png';
        $base64 = 'data:image/png;base64,';
        $file = $this->buildFile($filename);

        $user = User::find(6);
        $xid = UploadFileService::make($file)
            ->sanitize()
            ->forUser($user)
            ->saveToPersistent('myid');

        $this->streamAndAssertCorrect($xid, $base64, $filename);
    }

    /**
     * @test
     *
     * @group file-download
     * @group xxxz
     */
    public function uploaded_encrypted_persistent_stream_correct(): void
    {
        $this->markTestSkipped('assert fails - to implement');
        $filename = 'sylvester.png';
        $base64 = 'data:image/png;base64,';
        $file = $this->buildFile($filename);

        $user = User::find(6);
        $xid = UploadFileService::make($file)
            ->sanitize()
            ->forUser($user)
            ->saveEncryptedToPersistent('myid');

        $this->streamAndAssertCorrect($xid, $base64, $filename);
    }

    /**
     * @test
     *
     * @group file-download
     */
    public function uploaded_unencrypted_local_download_correct(): void
    {
        $filename = 'sylvester.png';
        $file = $this->buildFile($filename);

        $user = User::find(6);
        $xid = UploadFileService::make($file)
            ->sanitize()
            ->forUser($user)
            ->saveToLocal('myid');

        $this->downloadAndAssertCorrect($xid, $filename);
    }

    /**
     * @test
     *
     * @group file-download
     */
    public function uploaded_encrypted_local_download_correct(): void
    {
        $filename = 'sylvester.png';
        $file = $this->buildFile($filename);

        $user = User::find(6);
        $xid = UploadFileService::make($file)
            ->sanitize()
            ->forUser($user)
            ->saveEncryptedToLocal('myid');

        $this->downloadAndAssertCorrect($xid, $filename);
    }

    /**
     * @test
     *
     * @group file-download
     */
    public function uploaded_unencrypted_persistent_download_correct(): void
    {
        $filename = 'sylvester.png';
        $file = $this->buildFile($filename);

        $user = User::find(6);
        $xid = UploadFileService::make($file)
            ->sanitize()
            ->forUser($user)
            ->saveToPersistent('myid');

        $this->downloadAndAssertCorrect($xid, $filename);
    }

    /**
     * @test
     *
     * @group file-download
     */
    public function uploaded_encrypted_persistent_download_correct(): void
    {
        $this->markTestSkipped('assert fails - to implement');
        $filename = 'sylvester.png';
        $file = $this->buildFile($filename);

        $user = User::find(6);
        $xid = UploadFileService::make($file)
            ->sanitize()
            ->forUser($user)
            ->saveEncryptedToPersistent('myid');

        $this->downloadAndAssertCorrect($xid, $filename);
    }

    /**
     * @test
     *
     * @group file-download
     */
    public function stream_invalid_xid_exception(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->get(route('file.img.view', ['xid' => '1234567']));
    }

    /**
     * @test
     *
     * @group file-download
     */
    public function download_invalid_xid_exception(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->get(route('file.img.download', ['xid' => '1234567']));
    }

    /**
     * @test
     *
     * @group file-download
     */
    public function download_wrong_variant_exception(): void
    {
        $filename = 'sylvester.png';
        $file = $this->buildFile($filename);

        $user = User::find(6);
        $xid = UploadFileService::make($file)
            ->sanitize()
            ->forUser($user)
            ->saveToLocal('myid');

        $this->expectException(InvalidValueException::class);

        $this->get(route('file.img.download', ['xid' => $xid, 'variant' => 'wrong-variant']));
    }

    /**
     * @test
     *
     * @group file-download
     */
    public function has_variants_download_success(): void
    {
        $filename = 'sylvester.png';
        $file = $this->buildFile($filename);

        $user = User::find(6);
        $xid = UploadFileService::make($file)
            ->sanitize()
            ->forUser($user)
            ->saveToLocal('myid');

        $variant = 'small';
        $response = $this->get(route('file.img.download', ['xid' => $xid, 'variant' => $variant]));

        $downloadedContent = $response->streamedContent();
        $this->assertStreamedFileNotEmpty($downloadedContent);

        $this->assertFileEndsWithVariant($response, $variant);
    }

    /**
     * @test
     *
     * @group file-register
     */
    public function file_download_register_downloaded(): void
    {
        $filename = 'sylvester.png';
        $file = $this->buildFile($filename);

        $user = User::find(6);
        $xid = UploadFileService::make($file)
            ->sanitize()
            ->forUser($user)
            ->saveToLocal('myid');

        $user = $this->createUser();
        $variant = 'small';
        $response = $this
            ->actingAs($user)
            ->get(route('file.img.download', ['xid' => $xid, 'variant' => $variant]));

        $filestoreFileAccess = FilestoreFileAccess::query()->orderBy('id', 'desc')->get()->first();

        $this->assertEquals($user->id, $filestoreFileAccess->user_id);
        $this->assertTrue(strlen($filestoreFileAccess->ip) > 2);
        $this->assertTrue($filestoreFileAccess->as_download);
        $this->assertNull($filestoreFileAccess->as_view);
    }

    /**
     * @test
     *
     * @group file-register
     */
    public function file_prevent_log_download_not_logged(): void
    {
        $startCount = FilestoreFileAccess::count();
        $filename = 'sylvester.png';
        $file = $this->buildFile($filename);

        $user = User::find(6);
        $xid = UploadFileService::make($file)
            ->sanitize()
            ->forUser($user)
            ->withoutAccessLog()
            ->saveToLocal('myid');

        $user = $this->createUser();
        $variant = 'small';
        $response = $this
            ->actingAs($user)
            ->get(route('file.img.download', ['xid' => $xid, 'variant' => $variant]));

        $this->assertEquals($startCount, FilestoreFileAccess::count());
    }

    /**
     * @test
     *
     * @group file-register
     */
    public function file_download_register_view(): void
    {
        $filename = 'sylvester.png';
        $file = $this->buildFile($filename);

        $user = User::find(6);
        $xid = UploadFileService::make($file)
            ->sanitize()
            ->forUser($user)
            ->saveToLocal('myid');

        $user = $this->createUser();
        $variant = 'small';
        $response = $this
            ->actingAs($user)
            ->get(route('file.img.view', ['xid' => $xid, 'variant' => $variant]));

        $filestoreFileAccess = FilestoreFileAccess::query()->orderBy('id', 'desc')->get()->first();

        $this->assertEquals($user->id, $filestoreFileAccess->user_id);
        $this->assertTrue(strlen($filestoreFileAccess->ip) > 2);
        $this->assertNull($filestoreFileAccess->as_download);
        $this->assertTrue($filestoreFileAccess->as_view);
    }

    // --------- helpers ---------
    private function assertStreamedFileNotEmpty(string $stream): void
    {
        $this->assertGreaterThan(10, strlen($stream));
    }

    private function assertFileEndsWithVariant($response, string $variant)
    {
        $contentDisposition = $response->headers->get('Content-Disposition');
        $filenameFromResponse = str_replace('attachment; filename=', '', $contentDisposition);

        $exploded = explode('-', $filenameFromResponse);
        $variantExtension = $exploded[count($exploded) - 1];
        $exploded = explode('.', $variantExtension);

        $this->assertEquals($variant, $exploded[0]);
    }
}
