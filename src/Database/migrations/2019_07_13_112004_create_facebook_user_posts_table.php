<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacebookUserPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_user_posts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('facebook_user_id');
            $table->foreign('facebook_user_id')->references('id')->on('facebook_users');
            $table->string('status_type')->nullable();
            $table->string('facebook_id')->nullable();
            $table->string('message')->nullable();
            $table->string('story')->nullable();
            $table->string('picture')->nullable();
            $table->string('full_picture')->nullable();
            $table->double('comments')->nullable()->default(0);
            $table->double('shares')->nullable()->default(0);
            $table->double('likes')->nullable()->default(0);
            
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
        Schema::dropIfExists('facebook_user_posts');
    }
}
