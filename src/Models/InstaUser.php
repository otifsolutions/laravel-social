<?php

namespace OTIFSolutions\LaravelSocial\Models;

use Illuminate\Database\Eloquent\Model;

class InstaUser extends Model
{
    public function posts(){
        return $this->hasMany('OTIFSolutions\LaravelSocial\Models\InstaUserPost','insta_user_id');
    }
}
