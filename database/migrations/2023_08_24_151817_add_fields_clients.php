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
        Schema::table('clients_models', function (Blueprint $table) {
            $table->enum('rating', ['positive', 'neutral', 'negative'])->nullable()->default('neutral')->after('address');
            $table->dateTime('change_status_at')->nullable()->after('rating');
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
            $table->dropColumn('rating');
            $table->dropColumn('change_status_at');
        });
    }
};
