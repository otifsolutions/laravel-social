<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwitterUserPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twitter_user_posts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('twitter_user_id');
            $table->foreign('twitter_user_id')->references('id')->on('twitter_users');
            $table->string('post_created_at')->nullable();
            $table->string('twitter_id')->nullable();
            $table->string('text')->nullable();
            $table->double('retweet_count')->nullable();
            $table->double('favorite_count')->nullable();
            $table->boolean('possibly_sensitive')->nullable();
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
        Schema::dropIfExists('twitter_user_posts');
    }
}
