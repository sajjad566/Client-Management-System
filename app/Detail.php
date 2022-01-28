<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class detail extends Model
{
    protected $guarded = [];
    
    public function user()
    {
    	return $this->belongsTo(User::class);
    }
    
}