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
        Schema::table("chat_messages", function (Blueprint $table) {
            $table->string('chat_message_uuid')->after('related_to_user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("chat_messages", function (Blueprint $table) {
            $table->dropColumn(['chat_message_uuid']);
        });
    }
};
