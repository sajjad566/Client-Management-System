<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;


class UserInvoice extends Model
{
	protected $guarded = [];
    // Validate Invoice
	public function validateInvoice(Request $request)
	{
		request()->validate([
            'file' => 'required|mimes:pdf|max:3072',
        ]);
	}

    // Store Product
    public function storeInvoice(Request $request, $invoice)
    {
        $invoice->invoice = request('file');
        $invoice->user_id = request('the_user');
        $invoice->save();
        
    }

    public function updateInvoice(Request $request,$invoice)
    {
        if($request->has('file')) {
            $invoice->update(['invoice' => $request->file('file')->store('userInvoices')]);
        }
        $user = User::find($invoice->user_id);
        // dd($user);
        $data = array(
            'user' => $user,
            'document' => $invoice->invoice,
        );
        \Mail::send('emails.newDocByUser', $data, function($message) use ($data)
        {
            $message->from('amit@ksbin.com', "KSBIN New Upload");
            $message->subject("New Document by User");
            $message->to(['sajjadaslammm@gmail.com', 'insurance@ksbin.com']);
        });
        
    }
    
    // Every Invoice Has One User
    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
