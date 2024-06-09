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
        Schema::table('chat_participants', function (Blueprint $table) {
            $table->boolean('in_video_stream')->nullable()->after('muted');
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
            $table->dropColumn(['in_video_stream']);
        });
    }
};
