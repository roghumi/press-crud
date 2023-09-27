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
        Schema::create('role_user', function (Blueprint $table) {
            $table->bigInteger('user_id', false, true);
            $table->bigInteger('role_id', false, true);
            $table->integer('z_order', false, true)->default(0);

            $table->unique([
                'user_id',
                'role_id',
            ], 'unique_role_user');

            $table->index([
                'user_id',
                'role_id',
                'z_order',
            ], 'full_role_user_index');

            $table->foreign('role_id')->references('id')->on('roles');
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
