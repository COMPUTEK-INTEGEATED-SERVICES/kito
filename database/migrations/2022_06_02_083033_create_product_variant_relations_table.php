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
            $table->foreign('product_id')->on('products')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreign('variant_id')->on('variants')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreign('sku_id')->on('skus')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('variant_value');
            $table->softDeletes();
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
        Schema::dropIfExists('product_variant_relations');
    }
};
