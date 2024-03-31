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
        /*Schema::table('leads', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->nullable()->default(null)->after('source');
        });
        Schema::table('clients_models', function (Blueprint $table) {
            $table->unsignedBigInteger('lead_id')->nullable()->default(null)->after('source');
        });
        Schema::table('dogovors', function (Blueprint $table) {
            $table->unsignedBigInteger('lead_id')->nullable()->default(null)->after('name');
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
