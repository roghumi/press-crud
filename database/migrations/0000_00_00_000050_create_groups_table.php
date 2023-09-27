<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 256)->unique();
            $table->bigInteger('author_id', false, true);
            $table->integer('options', false, true)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index([
                'created_at',
                'updated_at',
                'deleted_at',
                'author_id',
                'options',
                'name',
            ], 'full_groups_index');

            $table->foreign('author_id')->references('id')->on(config('press.crud.user.table'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
