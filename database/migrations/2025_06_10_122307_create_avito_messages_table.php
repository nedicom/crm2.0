<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAvitoMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('avito_chats', function (Blueprint $table) {
            $table->id();
            $table->string('chat_id')->index();
            $table->text('gpt_prompt');
            $table->boolean('is_gpt_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('avito_chats');
    }
}
