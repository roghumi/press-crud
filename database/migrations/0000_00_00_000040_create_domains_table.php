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
        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->string('name', 256)->unique();
            $table->bigInteger('author_id', false, true);
            $table->json('data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index([
                'created_at',
                'updated_at',
                'deleted_at',
                'author_id',
                'name',
            ], 'full_domains_index');

            $table->foreign('author_id')->references('id')->on(config('press.crud.user.table'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};
