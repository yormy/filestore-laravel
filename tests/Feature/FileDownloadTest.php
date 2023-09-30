<?php

namespace Yormy\FilestoreLaravel\Tests\Feature;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Yormy\FilestoreLaravel\Domain\Shared\Models\MemberFileAccess;
use Yormy\FilestoreLaravel\Domain\Upload\Services\UploadFileService;
use Yormy\FilestoreLaravel\Exceptions\InvalidValueException;
use Yormy\FilestoreLaravel\Tests\TestCase;
use Yormy\FilestoreLaravel\Tests\Traits\AssertDownloadTrait;
use Yormy\FilestoreLaravel\Tests\Traits\AssertEncryptionTrait;
use Yormy\FilestoreLaravel\Tests\Traits\CleanupTrait;
use Yormy\FilestoreLaravel\Tests\Traits\FileTrait;
use Yormy\FilestoreLaravel\Tests\Traits\UserTrait;

class FileDownloadTest extends TestCase
{
    use AssertDownloadTrait;
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
    public function UploadedUnencryptedLocal_Stream_Correct(): void
    {
        $filename = 'sylvester.png';
        $base64 = 'data:image/png;base64,';
        $file = $this->buildFile($filename);

        $xid = UploadFileService::make($file)
            ->sanitize()
            ->memberId(6)
            ->saveToPersistent('myid');

        $this->streamAndAssertCorrect($xid, $base64, $filename);
    }

    /**
     * @test
     *
     * @group file-download
     */
    public function UploadedEncryptedPersistent_Stream_Correct(): void
    {
        $filename = 'sylvester.png';
        $base64 = 'data:image/png;base64,';
        $file = $this->buildFile($filename);

        $xid = UploadFileService::make($file)
            ->sanitize()
            ->memberId(6)
            ->saveEncryptedToPersistent('myid');

        $this->streamAndAssertCorrect($xid, $base64, $filename);
    }

    /**
     * @test
     *
     * @group file-download
     */
    public function UploadedUnencryptedLocal_Download_Correct(): void
    {
        $filename = 'sylvester.png';
        $file = $this->buildFile($filename);

        $xid = UploadFileService::make($file)
            ->sanitize()
            ->memberId(6)
            ->saveToLocal('myid');

        $this->downloadAndAssertCorrect($xid, $filename);
    }

    /**
     * @test
     *
     * @group file-download
     */
    public function UploadedEncryptedLocal_Download_Correct(): void
    {
        $filename = 'sylvester.png';
        $file = $this->buildFile($filename);

        $xid = UploadFileService::make($file)
            ->sanitize()
            ->memberId(6)
            ->saveEncryptedToLocal('myid');

        $this->downloadAndAssertCorrect($xid, $filename);
    }

    /**
     * @test
     *
     * @group file-download
     */
    public function UploadedUnencryptedPersistent_Download_Correct(): void
    {
        $filename = 'sylvester.png';
        $file = $this->buildFile($filename);

        $xid = UploadFileService::make($file)
            ->sanitize()
            ->memberId(6)
            ->saveToPersistent('myid');

        $this->downloadAndAssertCorrect($xid, $filename);
    }

    /**
     * @test
     *
     * @group file-download
     */
    public function UploadedEncryptedPersistent_Download_Correct(): void
    {
        $filename = 'sylvester.png';
        $file = $this->buildFile($filename);

        $xid = UploadFileService::make($file)
            ->sanitize()
            ->memberId(6)
            ->saveEncryptedToPersistent('myid');

        $this->downloadAndAssertCorrect($xid, $filename);
    }

    /**
     * @test
     *
     * @group file-download
     */
    public function Stream_InvalidXid_Exception(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->get(route('file.img.view', ['xid' => '1234567']));
    }

    /**
     * @test
     *
     * @group file-download
     */
    public function Download_InvalidXid_Exception(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->get(route('file.img.download', ['xid' => '1234567']));
    }

    /**
     * @test
     *
     * @group file-download
     */
    public function Download_WrongVariant_Exception(): void
    {
        $filename = 'sylvester.png';
        $file = $this->buildFile($filename);

        $xid = UploadFileService::make($file)
            ->sanitize()
            ->memberId(6)
            ->saveToLocal('myid');

        $this->expectException(InvalidValueException::class);

        $this->get(route('file.img.download', ['xid' => $xid, 'variant' => 'wrong-variant']));
    }

    /**
     * @test
     *
     * @group file-download
     */
    public function HasVariants_Download_Success(): void
    {
        $filename = 'sylvester.png';
        $file = $this->buildFile($filename);

        $xid = UploadFileService::make($file)
            ->sanitize()
            ->memberId(6)
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
    public function File_Download_RegisterDownloaded(): void
    {
        $filename = 'sylvester.png';
        $file = $this->buildFile($filename);

        $xid = UploadFileService::make($file)
            ->sanitize()
            ->memberId(6)
            ->saveToLocal('myid');

        $member = $this->createUser();
        $variant = 'small';
        $response = $this
            ->actingAs($member)
            ->get(route('file.img.download', ['xid' => $xid, 'variant' => $variant]));

        $memberFileAccess = MemberFileAccess::query()->orderBy('id', 'desc')->get()->first();

        $this->assertEquals($member->id, $memberFileAccess->user_id);
        $this->assertTrue(strlen($memberFileAccess->ip) > 2);
        $this->assertTrue($memberFileAccess->as_download);
        $this->assertNull($memberFileAccess->as_view);
    }

    /**
     * @test
     *
     * @group file-register
     */
    public function FilePreventLog_Download_NotLogged(): void
    {
        $startCount = MemberFileAccess::count();
        $filename = 'sylvester.png';
        $file = $this->buildFile($filename);

        $xid = UploadFileService::make($file)
            ->sanitize()
            ->memberId(6)
            ->withoutAccessLog()
            ->saveToLocal('myid');

        $member = $this->createUser();
        $variant = 'small';
        $response = $this
            ->actingAs($member)
            ->get(route('file.img.download', ['xid' => $xid, 'variant' => $variant]));

        $this->assertEquals($startCount, MemberFileAccess::count());
    }

    /**
     * @test
     *
     * @group file-register
     */
    public function File_Download_RegisterView(): void
    {
        $filename = 'sylvester.png';
        $file = $this->buildFile($filename);

        $xid = UploadFileService::make($file)
            ->sanitize()
            ->memberId(6)
            ->saveToLocal('myid');

        $member = $this->createUser();
        $variant = 'small';
        $response = $this
            ->actingAs($member)
            ->get(route('file.img.view', ['xid' => $xid, 'variant' => $variant]));

        $memberFileAccess = MemberFileAccess::query()->orderBy('id', 'desc')->get()->first();

        $this->assertEquals($member->id, $memberFileAccess->user_id);
        $this->assertTrue(strlen($memberFileAccess->ip) > 2);
        $this->assertNull($memberFileAccess->as_download);
        $this->assertTrue($memberFileAccess->as_view);
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
