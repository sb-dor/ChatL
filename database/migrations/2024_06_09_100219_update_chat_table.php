<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("chats", function (Blueprint $table) {
            $table->boolean("video_chat_streaming")->nullable()->after("temporary_chat");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("chats", function (Blueprint $table) {
            $table->dropColumn(['video_chat_streaming']);
        });
    }
};
