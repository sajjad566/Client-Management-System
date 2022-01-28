<?php

namespace App\Http\Controllers;

use App\Invoice;
use App\User;
use App\UserInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Auth;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices = Invoice::orderBy('created_at','desc')->get();
        return view('admin.invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $user = User::findOrFail($id);
        return view('admin.invoices.create', compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $invoice = new Invoice;
        $invoice->validateInvoice($request);
        $invoice->storeInvoice($request,$invoice);
        $invoice->updateInvoice($request,$invoice);
        // dd(request('paid'));
        return back()->with('message','Invoice Added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        if ($invoice->paid == 0) {
            $invoice->paid = 1;
        } else {
            $invoice->paid = 0;
        }
        $invoice->save();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        Storage::delete('/invoices/'.$invoice->invoice.'');
        $invoice->delete();
        return back();
    }

    // ALL USER CONTROLS
    public function allUserInvoices()
    {
        $user = Auth::user();
        $invoices = $user->invoices;
        $invoiceCount = count($invoices);
        return view('invoices.index', compact(['user','invoices', 'invoiceCount']));
    }

    // USER INVOICES
    public function userInvoices()
    {
        $invoices = UserInvoice::all();
        return  view('admin.invoices.userInvoices', compact(['invoices']));
    }


}
