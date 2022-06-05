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
        Schema::create('variant_values', function (Blueprint $table) {
            $table->id();
            $table->integer('variant_id');
            $table->string('value');
            $table->timestamps();
        });

        Schema::table('variant_values', function (Blueprint $table) {
            $table->foreign('variant_id')
                ->references('id')
                ->on('variants')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('variant_values');
    }
};
