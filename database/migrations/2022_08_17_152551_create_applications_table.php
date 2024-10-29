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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('agent_id')->nullable();
            $table->string('column_1')->nullable();
            $table->string('column_2')->nullable();
            $table->string('column_3')->nullable();
            $table->string('column_4')->nullable();
            $table->string('column_5')->nullable();
            $table->string('column_6')->nullable();
            $table->string('column_7')->nullable();
            $table->string('column_8')->nullable();
            $table->string('column_9')->nullable();
            $table->string('column_10')->nullable();
            $table->json('application_data');
            $table->string('status')->default('draft');
            $table->string('note')->nullable();
            $table->json('address')->nullable();
            $table->integer('current_version')->default(1);
            $table->index(['column_1']);
            $table->index(['column_2']);
            $table->index(['column_3']);
            $table->index(['column_4']);
            $table->index(['column_5']);
            $table->index(['column_6']);
            $table->index(['column_7']);
            $table->index(['column_8']);
            $table->index(['column_9']);
            $table->index(['column_10']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('applications');
    }
};
