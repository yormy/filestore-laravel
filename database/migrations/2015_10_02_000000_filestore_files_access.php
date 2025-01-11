<?php

namespace Yormy\FilestoreLaravel\Database\migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Yormy\FilestoreLaravel\Domain\Shared\Models\FilestoreFile;

return new class extends Migration
{
    public function up()
    {
        $tableClass = config('filestore.models.access');
        Schema::create((new $tableClass())->getTable(), function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(FilestoreFile::class);

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
