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
        Schema::create('configuration_change_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('configuration_id')
                ->references('id')
                ->on('configurations')
                ->onDelete('cascade');
            $table->text('from');
            $table->text('to');
//            $table->string('created_by');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configuration_change_logs');
    }
};
