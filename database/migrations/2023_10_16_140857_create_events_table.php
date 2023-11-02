<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title', 30);
            $table->string('description', 800)->nullable();
            $table->set('style', [
                'info',
                'success',
                'warning',
                'danger'
            ])->default('info');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->boolean('show_description')->default(false);
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
        Schema::dropIfExists('events');
    }
};
