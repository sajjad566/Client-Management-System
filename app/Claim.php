<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Claim extends Model
{
    protected $guarded = [];
    // Validate Claim File
	public function validateClaim(Request $request)
	{
		request()->validate([
            'file' => 'required|mimes:pdf|max:5120',
        ]);
	}

    // Store Product
    public function storeClaim(Request $request, $claim)
    {
        $claim->claim = request('file');
        $claim->user_id = request('the_user');
        $claim->save();
    }

    public function updateClaim(Request $request,$claim)
    {
        $sign = request('pdfInImg');;
        $image = $sign;
        $image = explode(";", $image)[1];
        $image = explode(",", $image)[1];
        // $image = str_replace('data:image/png;base64,', '', $sign);
        // $image = str_replace(' ', '+', $image);
        $imageName = $claim->id.str_random(40).'.'.'png';
        $claim->claim = $imageName;
        $claim->save();
        
        \File::put($_SERVER['DOCUMENT_ROOT']. '/storage/claims/' . $imageName, base64_decode($image));
        
        //  $user = User::find($claim->user_id);
        // dd($user->email);
        // dd($user);
        // $data = array(
        //     'document' => $contract->contract,
        //     'status' => $contract->signed,
        //     'email' => $contract->email,
        // );
        // \Mail::send('emails.newConByAdmin', $data, function($message) use ($data)
        // {
        //     $message->from('amit@ksbin.com', "KSBIN New Upload");
        //     $message->subject("New Contract");
        //     $message->to($data['email']);
        // });
        
    }

    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
