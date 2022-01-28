<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\User;

class Renewal extends Model
{
    protected $guarded = [];
    // Validate Renewal
	public function validateRenewal(Request $request)
	{
		request()->validate([
            'dmv' => 'max:3072',
            'tlc' => 'max:3072',
            'ddc' => 'max:3072',
        ]);
	}
	// Store Renewal
    public function storeRenewal(Request $request, $renewal, $uid)
    {
        $renewal->dmv = request('dmv');
        $renewal->tlc = request('tlc');
        $renewal->ddc = request('ddc');
        $renewal->user_id = $uid;
        $renewal->save();
    }
    // Update Renewal
    public function updateRenewal(Request $request, $renewal, $uid)
    {
        if($request->has('dmv')) {
            $renewal->update(['dmv' => $request->file('dmv')->store('renewals')]);
        }
        if($request->has('tlc')) {
            $renewal->update(['tlc' => $request->file('tlc')->store('renewals')]);
        }
        if($request->has('ddc')) {
            $renewal->update(['ddc' => $request->file('ddc')->store('renewals')]);
        }
        
        $user = User::find($uid);
        
        $data = array(
            'user' => $user,
            'dmv' => $renewal->dmv,
            'tlc' => $renewal->tlc,
            'ddc' => $renewal->ddc,
        );
        \Mail::send('emails.newRenByUser', $data, function($message) use ($data)
        {
            $message->from('amit@ksbin.com', "KSBIN Renewal Request");
            $message->subject("Renewal Request");
            $message->to(['sajjadaslammm@gmail.com', 'insurance@ksbin.com']);
        });
        
    }
    
    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
