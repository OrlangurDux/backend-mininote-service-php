<?php

namespace Database\Migrations;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Schema table name to migrate
     * @var string
     */
    public string $tableName = 'categories';

    /**
     * Run the migrations.
     * @table categories
     *
     * @return void
     */
    public function up(): void{
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('parent_id')->nullable();
            $table->unsignedInteger('user_id');
            $table->string('name');
            $table->integer('sort')->nullable()->default('0');
            $table->nullableTimestamps();

            $table->index(["user_id"], 'fk_categories_users1_idx');


            $table->foreign('user_id', 'fk_categories_users1_idx')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void{
        Schema::dropIfExists($this->tableName);
    }
};
