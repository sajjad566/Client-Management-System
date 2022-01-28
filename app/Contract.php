<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Contract extends Model
{
    protected $guarded = [];
    // Validate Invoice
	public function validateContract(Request $request)
	{
		request()->validate([
            'contract-name' => 'required',
            'file' => 'required|mimes:pdf|max:5120',
            'contract-user' => 'required',
        ]);
	}

    // Store Product
    public function storeContract(Request $request, $contract)
    {
        $contract->name = request('contract-name');
        $contract->contract = request('pdfInImg');
        $contract->user_id = request('contract-user');
        $contract->save();
    }

    public function updateContract(Request $request,$contract)
    {
        $sign = $contract->contract;
        $image = $sign;
        $image = explode(";", $image)[1];
        $image = explode(",", $image)[1];
        // $image = str_replace('data:image/png;base64,', '', $sign);
        // $image = str_replace(' ', '+', $image);
        $imageName = $contract->id.str_random(40).'.'.'png';
        $contract->contract = $imageName;
        
        $contract->save();
        
        \File::put($_SERVER['DOCUMENT_ROOT']. '/storage/contracts/' . $imageName, base64_decode($image));
        
         $user = User::find($contract->user_id);
        // dd($user->email);
        // dd($user);
        $data = array(
            'document' => $contract->contract,
            'status' => 0,
            'email' => $user->email,
        );
        $em = $user->email;
        \Mail::send('emails.newConByAdmin', $data, function($message) use ($em)
        {
            $message->from('amit@ksbin.com', "KSBIN New Upload");
            $message->subject("New Contract");
            $message->to($em);
        });
        
    }

    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
