<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('clients_models', function (Blueprint $table) {
            $table->integer('consult')->nullable()->default(41)->after('lawyer');
            $table->integer('attract')->nullable()->default(41)->after('lawyer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients_models', function (Blueprint $table) {
            $table->dropColumn('consult');
            $table->dropColumn('attract');
        });
    }
};
