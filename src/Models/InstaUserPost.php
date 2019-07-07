<?php

namespace OTIFSolutions\LaravelSocial\Models;

use Illuminate\Database\Eloquent\Model;

class InstaUserPost extends Model
{
    public function user(){
        return $this->belongsTo('OTIFSolutions\LaravelSocial\Models\InstaUser','insta_user_id');
    }
}
