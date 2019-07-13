<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwitterUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twitter_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('full_name')->nullable();
            $table->string('screen_name')->nullable();
            $table->string('location')->nullable();
            $table->string('description')->nullable();
            $table->double('followers_count')->nullable()->default(0);
            $table->double('friends_count')->nullable()->default(0);
            $table->double('listed_count')->nullable()->default(0);
            $table->double('favourites_count')->nullable()->default(0);
            $table->double('statuses_count')->nullable()->default(0);
            $table->string('profile_background_image_url')->nullable();
            $table->string('profile_background_image_url_https')->nullable();
            $table->string('profile_image_url')->nullable();
            $table->string('profile_image_url_https')->nullable();
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
        Schema::dropIfExists('twitter_users');
    }
}
