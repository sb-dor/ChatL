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
        Schema::table('chat_participants', function (Blueprint $table) {
            $table->unsignedBigInteger('status_id')->after('user_id')->nullable();

            $table->foreign('status_id', "chat_participant_status_id_foreign_key")
                ->on('chat_user_status')->references('id')
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
        Schema::table('chat_participants', function (Blueprint $table) {
            $table->dropForeign('chat_participant_status_id_foreign_key');
            $table->dropColumn(['status_id']);
        });
    }
};
