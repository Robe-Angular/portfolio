<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admins', function(Blueprint $table) {
            $table->increments('id'); 
            $table->string('name', 255);
            $table->string('email')->unique();
            $table->string('password');
            $table->date('created_at');
            $table->date('updated_at');
        });
        Schema::create('consumers', function(Blueprint $table) {
            $table->increments('id'); 
            $table->string('name', 255);
            $table->string('email')->unique();
            $table->string('password');
            $table->date('created_at');
            $table->date('updated_at');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
        Schema::dropIfExists('consumers');
    }
};
