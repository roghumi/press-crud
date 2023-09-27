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
        Schema::create('domain_user', function (Blueprint $table) {
            $table->bigInteger('user_id', false, true);
            $table->bigInteger('domain_id', false, true);

            $table->unique(['user_id', 'domain_id']);

            $table->foreign('domain_id')->references('id')->on('domains');
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
