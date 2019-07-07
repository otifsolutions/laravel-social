<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstaUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insta_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pk')->nullable();
            $table->string('username');
            $table->string('full_name')->nullable();
            $table->longText('image')->nullable();
            $table->boolean('is_private')->nullable();
            $table->boolean('is_verified')->nullable();
            $table->integer('media_count')->nullable();
            $table->integer('follower_count')->nullable();
            $table->string('followers')->nullable();
            $table->integer('following_count')->nullable();
            $table->string('followings')->nullable();
            $table->integer('following_tag_count')->nullable();
            $table->double('engagement_rate', 8, 2)->nullable();
            $table->enum('status', ['ACTIVE', 'PENDING','FOUND','NOT_FOUND','PRIVATE']);
            $table->date('last_viewed_at')->default(date('Y-m-d'));
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
        Schema::dropIfExists('insta_users');
    }
}
