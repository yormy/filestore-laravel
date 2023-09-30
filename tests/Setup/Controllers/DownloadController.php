<?php

declare(strict_types=1);

namespace Yormy\FilestoreLaravel\Tests\Setup\Controllers;

use Illuminate\Http\Request;
use Yormy\Apiresponse\Facades\ApiResponse;
use Yormy\FilestoreLaravel\Domain\Download\Services\FileServe;
use Yormy\FilestoreLaravel\Domain\Upload\Observers\Events\FileDownloadWrongVariantEvent;
use Yormy\FilestoreLaravel\Exceptions\InvalidValueException;

class DownloadController
{
    public function view(Request $request, string $xid, string $variant = null)
    {
        $this->validateVariantOrAbort($variant);

        $down = FileServe::view($request, $xid);

        return ApiResponse::withData(['file' => $down])->successResponse();
    }

    public function cover(Request $request, string $xid)
    {
        $down = FileServe::viewCover($request, $xid);

        return ApiResponse::withData(['file' => $down])->successResponse();
    }

    public function download(Request $request, $xid, string $variant = null)
    {
        $this->validateVariantOrAbort($variant);

        return FileServe::download($request, $xid, $variant);
    }

    public function pages(Request $request, $xid)
    {
        $down = FileServe::pages($request, $xid);

        return ApiResponse::withData(['files' => $down])->successResponse();
    }

    public function page(Request $request, $xid, int $pageNr)
    {
        $down = FileServe::page($request, $xid, $pageNr);

        return ApiResponse::withData(['file' => $down])->successResponse();
    }

    private function validateVariantOrAbort(?string $variant): void
    {
        if ($variant && ! array_key_exists($variant, config('filestore.variants'))) {
            event(new FileDownloadWrongVariantEvent($variant));
            throw new InvalidValueException('Variant not allowed');
        }
    }
}
