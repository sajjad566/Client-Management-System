<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Invoice extends Model
{
    protected $guarded = [];
    // Validate Invoice
	public function validateInvoice(Request $request)
	{
	   // 'file' => 'required|mimes:pdf|max:3072',
		request()->validate([
            
            'invoice-user' => 'required',
            'invoice-price' => 'required',
            'policy' => 'required',
            'due' => 'required'
            
        ]);
	}

    // Store Product
    public function storeInvoice(Request $request, $invoice)
    {
        $invoice->invoice = "OptionRemoved";
        $invoice->user_id = request('invoice-user');
        $invoice->paid = 0;
        $invoice->price = request('invoice-price');
        $invoice->policy = request('policy');
        $invoice->due = request('due');

        $invoice->save();

    }

    public function updateInvoice(Request $request,$invoice)
    {
        // if($request->has('file')) {
        //     $invoice->update(['invoice' => $request->file('file')->store('invoices')]);
        // }
        $user = User::find($invoice->user_id);
        // dd($user->email);
        // dd($user);
        $data = array(
            'document' => $invoice->invoice,
            'status' => $invoice->paid,
            'email' => $user->email,
        );
        \Mail::send('emails.newInvByAdmin', $data, function($message) use ($data)
        {
            $message->from('amit@ksbin.com', "KSBIN New Upload");
            $message->subject("New Invoice");
            $message->to($data['email']);
        });
    }


    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
