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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('related_to_user_id')->nullable();
            $table->string('message')->nullable();
            $table->string('image_url')->nullable();
            $table->string('video_url')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('deleted_at')->nullable();


            $table->foreign('user_id', "chat_messages_user_foreign_key")
                ->on('users')->references('id')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('related_to_user_id', "chat_messages_related_to_user_foreign_key")
                ->on('users')->references('id')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_messages');
    }
};
