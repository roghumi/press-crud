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
        Schema::create('domain_hierarchy', function (Blueprint $table) {
            $table->bigInteger('parent_id', false, true);
            $table->bigInteger('child_id', false, true);
            $table->integer('depth', false, true);

            $table->unique(['parent_id', 'child_id']);

            $table->index([
                'parent_id',
                'child_id',
                'depth',
            ], 'full_domain_hierarchy_index');

            $table->foreign('parent_id')->references('id')->on('domains');
            $table->foreign('child_id')->references('id')->on('domains');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_hierarchy');
    }
};
