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
        Schema::create('registration_verifications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unique();
            $table->string('email_token')->nullable();
            $table->string('sms_token')->nullable();
            $table->timestamp('email_token_used_at')->nullable();
            $table->timestamp('sms_token_used_at')->nullable();
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
        Schema::dropIfExists('registration_verifications');
    }
};
