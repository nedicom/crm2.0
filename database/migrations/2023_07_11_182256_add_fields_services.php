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
        Schema::table('services', function (Blueprint $table) {
            $table->decimal('price', 9, 2)->change();
            $table->double('execution_time', 8 ,2)->nullable();
            $table->string('description', 500)->nullable();
            $table->string('url_disk')->nullable();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->double('duration', 8, 2)->nullable()->change();
            $table->unsignedBigInteger('service_id')->nullable();

            $table->foreign('service_id')->references('id')->on('services')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['execution_time', 'description', 'url_disk']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->double('duration', 8, 2)->change();

            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');
        });
    }
};
