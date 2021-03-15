<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions_actions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('prefix')->nullable();
            $table->string('description')->nullable();
            $table->string('full_name');
            $table->foreignId('module_id')->nullable()->constrained('permissions_modules');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('actions');
    }
}
