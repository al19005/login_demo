<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pre_user', function (Blueprint $table) {
            $table->id();
            $table->string('student_number', 32)->comment('学籍番号');
            $table->string('token', 255)->comment('確認トークン');
            $table->tinyInteger('status')->comment('ステータス');
            $table->dateTime('expiration_datetime')->comment('有効期限');
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
        Schema::dropIfExists('pre_users');
    }
}
