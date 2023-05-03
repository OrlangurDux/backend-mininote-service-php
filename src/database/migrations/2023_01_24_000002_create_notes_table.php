<?php

namespace Database\Migrations;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public string $tableName = 'notes';

    /**
     * Run the migrations.
     * @table notes
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->text('title')->nullable();
            $table->longText('note')->nullable();
            $table->enum('status', ['draft', 'public', 'archive']);

            $table->index(["user_id"], 'fk_notes_users_idx');

            $table->index(["category_id"], 'fk_notes_categories1_idx');
            $table->nullableTimestamps();


            $table->foreign('user_id', 'fk_notes_users_idx')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('category_id', 'fk_notes_categories1_idx')
                ->references('id')->on('categories')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
