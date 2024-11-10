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
        Schema::create('insurance_confirmations', function (Blueprint $table) {
            $table->id();
            $table->string('fid');
            $table->string('farmer_name');
            $table->string('nid');
            $table->string('phone');
            $table->string('thana');
            $table->string('area');
            $table->string('region');
            $table->string('project_name');
            $table->string('fo_id');
            $table->string('fo_name');
            $table->string('area_manager');
            $table->string('regional_manager');
            $table->string('approved_amount'); 
            $table->string('acceptance')->nullable();
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
        Schema::dropIfExists('insurance_confirmations');
    }
};
