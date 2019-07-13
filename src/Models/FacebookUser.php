<?php

namespace OTIFSolutions\LaravelSocial\Models;

use Illuminate\Database\Eloquent\Model;

class FacebookUser extends Model
{
    public function posts(){
        return $this->hasMany('OTIFSolutions\LaravelSocial\Models\FacebookUserPost','facebook_user_id');
    }
}
