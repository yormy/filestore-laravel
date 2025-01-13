<?php

namespace Yormy\FilestoreLaravel\Tests\Feature\Main;

use Illuminate\Database\UniqueConstraintViolationException;
use Yormy\FilestoreLaravel\Domain\Upload\Services\UploadFileService;
use Yormy\FilestoreLaravel\Tests\TestCase;
use Yormy\FilestoreLaravel\Tests\Traits\AssertDownloadTrait;
use Yormy\FilestoreLaravel\Tests\Traits\AssertPdfTrait;
use Yormy\FilestoreLaravel\Tests\Traits\FileTrait;
use Yormy\FilestoreLaravel\Tests\Traits\UserTrait;

class FileCreationTest extends TestCase
{
    use AssertDownloadTrait;
    use AssertPdfTrait;
    use FileTrait;
    use UserTrait;

    /**
     * @test
     *
     * @group pdf
     */
    public function file__get_by_name__success(): void
    {
        $filename = 'jokes.pdf';
        $file = $this->buildFile($filename);

        $name = 'jokey';
        $xid = UploadFileService::make($file)
            ->name($name)
            ->saveToPersistent('myid');

        $this->downloadPdfNameAndAssertCorrect($name, $filename);
    }

    /**
     * @test
     *
     * @group file
     */
    public function file_with_name__upload_same_name__fails(): void
    {
        $filename = 'jokes.pdf';
        $file = $this->buildFile($filename);

        $xid = UploadFileService::make($file)
            ->name('joke')
            ->saveToPersistent('myid');

        $this->expectException(UniqueConstraintViolationException::class);
        $xid = UploadFileService::make($file)
            ->name('joke')
            ->saveToPersistent('myid');
    }

}
