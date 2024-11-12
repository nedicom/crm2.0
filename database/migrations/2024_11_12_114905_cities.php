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
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('city');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedBigInteger('city_id')->nullable();
        });

        Schema::table('dogovors', function (Blueprint $table) {
            $table->unsignedBigInteger('city_id')->nullable();
        });
        Schema::table('clients_models', function (Blueprint $table) {
            $table->unsignedBigInteger('city_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cities');

        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('city_id');
        });
        Schema::table('dogovors', function (Blueprint $table) {
            $table->dropColumn('city_id');
        });
        Schema::table('clients_models', function (Blueprint $table) {
            $table->dropColumn('city_id');
        });
    }
};
