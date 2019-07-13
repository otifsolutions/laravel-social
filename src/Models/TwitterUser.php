<?php

namespace OTIFSolutions\LaravelSocial\Models;

use Illuminate\Database\Eloquent\Model;

class TwitterUser extends Model
{
    public function posts(){
        return $this->hasMany('OTIFSolutions\LaravelSocial\Models\TwitterUserPost','twitter_user_id');
    }
}
