<?php

namespace App\Http\Controllers;

use App\Attachment;
use Illuminate\Http\Request;
use App\User;

class AttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $attachments = Attachment::all();
        return view('admin.attachments.index', compact('attachments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $user = User::findOrFail($id);
        
        return view('admin.attachments.create', compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $attachment = new Attachment;
        $attachment->validateAttachment($request);
        $attachment->storeAttachment($request,$attachment);
        $attachment->updateAttachment($request,$attachment);
        // dd(request('paid'));
        return back()->with('message','Attachment Added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Attachment  $attachment
     * @return \Illuminate\Http\Response
     */
    public function show(Attachment $attachment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Attachment  $attachment
     * @return \Illuminate\Http\Response
     */
    public function edit(Attachment $attachment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Attachment  $attachment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attachment $attachment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Attachment  $attachment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $attachment = Attachment::findOrFail($id);
        $attachment->delete();
        return back();
    }
    
    
}
