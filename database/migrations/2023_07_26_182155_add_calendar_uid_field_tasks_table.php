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
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('calendar_uid')->nullable()->after('type')->comment('UID календаря');
            $table->enum('type_duration', ['old', 'new'])
                ->default('old')
                ->after('duration')
                ->comment('Тип хранения значение продолжительности. Старые значения в old, новые/обновленные будут хранится в new.');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->double('execution_time')->nullable()->change();

            $table->enum('type_execution_time', ['old', 'new'])
                ->default('old')
                ->after('execution_time')
                ->comment('Тип хранения значение продолжительности. Старые значения в old, новые/обновленные будут хранится в new.');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('services', 'type_execution_time')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('type_execution_time');
            });
        }

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('type_duration');
            $table->dropColumn('calendar_uid');
        });
    }
};
