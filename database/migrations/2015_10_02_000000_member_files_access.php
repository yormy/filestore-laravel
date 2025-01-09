<?php

namespace Yormy\FilestoreLaravel\Database\migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Yormy\FilestoreLaravel\Domain\Shared\Models\MemberFile;

return new class extends Migration
{
    public function up()
    {
        Schema::create('filestore_files_access', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(MemberFile::class);

            $table->integer('user_id')->nullable();
            $table->string('user_type')->nullable();

            $table->string('ip')->nullable();   // need place for encrypted values
            $table->string('useragent')->nullable();

            $table->boolean('as_download')->nullable();
            $table->boolean('as_view')->nullable();

            $table->timestamps();
        });
    }
};
