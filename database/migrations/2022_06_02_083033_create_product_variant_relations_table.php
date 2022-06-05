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
        Schema::create('product_variant_relations', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->integer('variant_id');
            $table->integer('sku_id');
            $table->string('variant_value');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('product_variant_relations', function (Blueprint $table) {
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreign('variant_id')
                ->references('id')
                ->on('variants')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreign('sku_id')
                ->references('id')
                ->on('skus')
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
        Schema::dropIfExists('product_variant_relations');
    }
};
