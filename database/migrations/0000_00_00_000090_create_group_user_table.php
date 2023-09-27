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
        Schema::create('group_user', function (Blueprint $table) {
            $table->bigInteger('user_id', false, true);
            $table->bigInteger('group_id', false, true);
            $table->integer('options', false, true)->default(0);

            $table->unique(['user_id', 'group_id'], 'unique_group_user');

            $table->index([
                'user_id',
                'group_id',
                'options',
            ], 'full_group_user_index');

            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('user_id')->references('id')->on(config('press.crud.user.table'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_user');
    }
};
