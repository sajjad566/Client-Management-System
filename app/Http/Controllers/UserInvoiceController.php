<?php

namespace App\Http\Controllers;

use App\UserInvoice;
use Illuminate\Http\Request;
use Auth;

class UserInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        return view('userInvoices.create', compact(['user']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $invoice = new UserInvoice;
        $invoice->validateInvoice($request);
        $invoice->storeInvoice($request,$invoice);
        $invoice->updateInvoice($request,$invoice);
        // dd(request('paid'));
        return back()->with('message','Document Sent!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\UserInvoice  $userInvoice
     * @return \Illuminate\Http\Response
     */
    public function show(UserInvoice $userInvoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UserInvoice  $userInvoice
     * @return \Illuminate\Http\Response
     */
    public function edit(UserInvoice $userInvoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserInvoice  $userInvoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserInvoice $userInvoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserInvoice  $userInvoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserInvoice $userInvoice)
    {
        //
    }
    
    // Delete Invoice By User
    public function deleteInvoiceByUser($id)
    {
        $invoice = UserInvoice::findOrFail($id);
        $invoice->delete($id);
        return back();
    }
}
