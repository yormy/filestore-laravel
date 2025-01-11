<?php

namespace Yormy\FilestoreLaravel\Database\migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $tableClass = config('filestore.models.keys');
        Schema::create((new $tableClass())->getTable(), function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('user_type')->nullable();

            $table->string('key')->nullable();

            $table->timestamps();
        });
    }
};
