<?php

namespace Yormy\FilestoreLaravel\Tests\Feature\Main;

use Illuminate\Routing\Exceptions\StreamedResponseException;
use Yormy\FilestoreLaravel\Tests\TestCase;
use Yormy\FilestoreLaravel\Tests\Traits\AssertDownloadTrait;
use Yormy\FilestoreLaravel\Tests\Traits\FileTrait;
use Yormy\FilestoreLaravel\Tests\Traits\UserTrait;

class FileUserUploadTest extends TestCase
{
    use AssertDownloadTrait;
    use FileTrait;
    use UserTrait;

    /**
     * @test
     *
     * @group file-user-upload
     */
    public function UploadSystemEncryption_DecryptAsOtherUser_Success(): void
    {
        $member = $this->createUser();

        $filename = 'sylvester.png';
        $file = $this->buildFile($filename);

        $response = $this
            ->actingAs($member)
            ->json('POST', route('api.upload', []), [
                'file' => $file,
            ]);

        $content = $response->getContent();
        $xids = json_decode($content, true)['xids'];
        $xid = $xids[0];

        $memberNew = $this->createUser();
        $this->downloadAndAssertCorrectAsMember($xid, $filename, $memberNew);
    }

    /**
     * @test
     *
     * @group file-user-upload
     * @group xxx
     */
    public function UploadUser_DecryptSameUser_Success(): void
    {
        $member = $this->createUser();

        $filename = 'sylvester.png';
        $file = $this->buildFile($filename);

        $response = $this
            ->actingAs($member)
            ->json('POST', route('api.upload-user-encryption', []), [
                'file' => $file,
            ]);

        // this should double encrypt and double decrypt


        $content = $response->getContent();
        $xids = json_decode($content, true)['xids'];
        $xid = $xids[0];

        $this->downloadAndAssertCorrectAsMember($xid, $filename, $member);
    }

    /**
     * @test
     *
     * @group file-user-upload
     * @group xxx
     */
    public function UploadUser_DecryptOtherUser_Failed(): void
    {
        $member = $this->createUser();

        $filename = 'sylvester.png';
        $file = $this->buildFile($filename);

        $response = $this
            ->actingAs($member)
            ->json('POST', route('api.upload-user-encryption', []), [
                'file' => $file,
            ]);

        $content = $response->getContent();
        $xids = json_decode($content, true)['xids'];
        $xid = $xids[0];

        $memberNew = $this->createUser();
        $this->expectException(StreamedResponseException::class); // although caught, it is still a risky test, seems to be an issue in phpunit
        $this->downloadAndAssertCorrectAsMember($xid, $filename, $memberNew);

        $this->assertTrue(true);
    }
}
