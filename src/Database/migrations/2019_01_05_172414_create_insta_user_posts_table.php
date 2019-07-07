<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstaUserPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insta_user_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('insta_user_id');
            $table->foreign('insta_user_id')->references('id')->on('insta_users');
            $table->string('pk');
            $table->string('insta_id');
            $table->string('taken_at');
            $table->integer('media_type');
            $table->integer('comment_count');
            $table->string('comments');
            $table->integer('like_count');
            $table->string('likes');
            $table->integer('engagement');
            $table->longText('full_image')->nullable();
            $table->longText('thumb')->nullable();
            $table->longText('video_url')->nullable();
            $table->longText('code')->nullable();
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
        Schema::dropIfExists('insta_user_posts');
    }
}
