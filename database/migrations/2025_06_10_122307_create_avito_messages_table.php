<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAvitoMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('avito_messages', function (Blueprint $table) {
            $table->id();
            $table->string('chat_id')->index();
            $table->text('message');
            $table->string('sender_id')->nullable();
            $table->timestamp('sent_at')->nullable();
            // Если хотите использовать стандартные created_at и updated_at:
            // $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('avito_messages');
    }
}
