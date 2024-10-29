<?php

use App\Services\Application\Status;
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
        Schema::create('application_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')
                ->references('id')
                ->on('applications')
                ->onDelete('cascade');
            $table->string('type');
            $table->string('section_slug')->nullable();
            $table->json('from');
            $table->json('to');
            $table->string('status')->nullable();
            $table->string('user_type');
            $table->unsignedInteger('created_by_id');
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
        Schema::dropIfExists('application_logs');
    }
};
