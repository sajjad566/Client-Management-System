<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Invoice;
use App\Contract;
use App\UserInvoice;
use App\Attachment;
use App\Claim;
use App\Policy;
use App\Detail;
use App\Mail\SendMailable;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function approve($user_id, $inv_id)
    {
        $invoice = Invoice::findOrFail($inv_id);
        $invoice->paid = 1;
        $invoice->save();
        
        return view('payment');
    }
    
}