<?php

namespace Yormy\FilestoreLaravel\Database\migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $table = config('filestore.tables.files');
        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->string('xid')->unique();
            $table->string('user_id')->nullable();
            $table->string('user_type')->nullable();

            $table->string('original_filename')->nullable();
            $table->string('original_extension')->nullable();
            $table->integer('height')->nullable();
            $table->integer('width')->nullable();
            $table->integer('size_kb')->nullable();
            $table->integer('total_pages')->nullable();
            $table->string('disk')->nullable();
            $table->string('path')->nullable();
            $table->string('filename')->nullable();
            $table->string('mime')->nullable();
            $table->boolean('is_encrypted')->nullable();
            $table->json('variants')->nullable();
            $table->boolean('allow_pdf_embedding')->default(false);
            $table->boolean('access_log')->default(false);
            $table->boolean('user_encryption')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }
};
