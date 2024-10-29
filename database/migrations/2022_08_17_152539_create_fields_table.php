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
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->nullable();
            $table->string('slug')->unique();
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->string('type');
            $table->foreignId('group_id')->nullable()->constrained('fields')->nullOnDelete();
            $table->integer('order');
            $table->boolean('is_searchable')->default(0);
            $table->json('rules')->nullable();
            $table->json('rules_messages')->nullable();
            $table->json('visible_if')->nullable();
            $table->boolean('is_repeatable')->default(0);
            $table->string('repeatable_text')->nullable();
            $table->string('repeatable_class')->nullable();
            $table->json('possible_values')->nullable();
            $table->boolean('is_editable')->default(1);
            $table->string('cached_key')->nullable();
            $table->string('dependent_field')->nullable();
            $table->json('visibility_dependent_field')->nullable();
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
        Schema::dropIfExists('fields');
    }
};
