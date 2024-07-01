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
        Schema::table('rooms', function (Blueprint $table) {
            $table->unsignedBigInteger('chat_id')->nullable()->after('id');


            $table->foreign('chat_id', 'chats_table_foreign_id')
                ->references("id")
                ->on("chats")
                ->onUpdate("cascade")
                ->onDelete("set null");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("rooms", function (Blueprint $table) {
            $table->dropForeign('chats_table_foreign_id');
            $table->dropColumn(['chat_id']);
        });
    }
};
