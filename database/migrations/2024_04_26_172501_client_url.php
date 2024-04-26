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
        Schema::table('clients_models', function (Blueprint $table) {
            $table->string('url')->nullable()->after('source');
            $table->string('casettype')->default('Не выбрано')->after('source');
        });
        Schema::table('leads', function (Blueprint $table) {
            $table->string('casettype')->default('Не выбрано')->after('source');
        });
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
