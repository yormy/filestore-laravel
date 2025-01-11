<?php

declare(strict_types=1);

namespace Yormy\FilestoreLaravel\Tests\Setup\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Yormy\FilestoreLaravel\Domain\Upload\Services\UploadFileService;
use Yormy\FilestoreLaravel\Tests\Setup\Models\User;

class UploadController
{
    public function uploadUserEncryption(Request $request)
    {
        $file = $request->file('file');

        $user = auth::user();
        $xid = UploadFileService::make($file)
            ->sanitize()
            ->forUser($user)
            ->userEncryption()
            ->saveEncryptedToLocal('myid');

        return [
            'xids' => [$xid],
        ];
    }

    public function upload(Request $request)
    {
        $file = $request->file('file');

        $user = User::find(6);
        $xid = UploadFileService::make($file)
            ->sanitize()
            ->forUser($user)
            // ->saveEncryptedToLocal('myid', 'key:sadasfar3451235r);
            ->saveEncryptedToLocal('myid');
        // ->saveEncryptedToPersistent('myid');

        return [
            'xids' => [$xid],
        ];
    }

    public function uploadLargeFiles(Request $request)
    {
        $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));
        if (! $receiver->isUploaded()) {
            // file not uploaded
        }

        $fileReceived = $receiver->receive(); // receive file
        if ($fileReceived->isFinished()) { // file uploading is complete / all chunks are uploaded
            $file = $fileReceived->getFile(); // get file
            $extension = $file->getClientOriginalExtension();
            $fileName = str_replace('.'.$extension, '', $file->getClientOriginalName()); // file name without extenstion
            $fileName .= '_'.md5('oooo').'.'.$extension; // a unique file name

            $disk = Storage::disk(config('filesystems.default'));
            $path = $disk->putFileAs('videos', $file, $fileName);

            // delete chunked file
            unlink($file->getPathname());

            return [
                'path' => asset('storage/'.$path),
                'filename' => $fileName,
            ];
        }

        // otherwise return percentage information
        $handler = $fileReceived->handler();

        return [
            'done' => $handler->getPercentageDone(),
            'status' => true,
        ];
    }
}
