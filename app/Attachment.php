<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class attachment extends Model
{
    protected $guarded = [];
    // Validate attachment
	public function validateattachment(Request $request)
	{
		request()->validate([
            'file' => 'required|mimes:pdf|max:3072',
            'attachment-user' => 'required',
        ]);
	}

    // Store Product
    public function storeattachment(Request $request, $attachment)
    {
        $attachment->attachment = request('file');
        $attachment->user_id = request('attachment-user');
        $attachment->save();
    }

    public function updateattachment(Request $request,$attachment)
    {
        if($request->has('file')) {
            $attachment->update(['attachment' => $request->file('file')->store('attachments')]);
        }
    }


    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
