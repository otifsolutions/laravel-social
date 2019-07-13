<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacebookUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('global_brand_page_name')->nullable();
            $table->string('name_with_location_descriptor')->nullable();
            $table->string('facebook_id')->nullable();
            $table->double('talking_about_count')->nullable()->default(0);
            $table->double('rating_count')->nullable()->default(0);
            $table->double('new_like_count')->nullable()->default(0);
            $table->double('fan_count')->nullable()->default(0);
            $table->string('about')->nullable();
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
        Schema::dropIfExists('facebook_users');
    }
}
