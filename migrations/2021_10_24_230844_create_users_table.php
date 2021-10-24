<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name', 100)->comment('TG 用戶名');
            $table->string('last_name', 100)->comment('TG 用戶姓');;
            $table->string('username', 100)->unique()->nullable()->comment('TG 用戶名(唯一)');
            $table->string('language_code', 50)->comment('TG 用戶語言');;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
}
