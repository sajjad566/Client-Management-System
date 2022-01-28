<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Detail;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('changePassword', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (request('userPass') != '' || request('newPass') != '') {

            $pass = request('userPass');

            if (Hash::check($pass, $user->password)) {
                 request()->validate([
                    'newPass' => 'required|min:8',
                ]);
                 $newPass = request('newPass');
                 $user->password = Hash::make($newPass);

            } else {
                return back()->with('error', 'Old Password Not Matched');
            }
        
        }
        $user->save();
        return back()->with('message', 'Password Updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
    
    // Change Policy Details
    public function policyDetails($id)
    {
        $user = User::findOrFail($id);
        request()->validate([
            'subject' => 'required',
            'policy' => 'required',
            'details' => 'required'
        ]);
        $data = [
            'name' => $user->name,    
            'email' => $user->email,    
            'subject' => request('subject'),    
            'policy' => request('policy'),    
            'details' => request('details'),    
        ];
        $users = User::where('role_id', 1)->get();
        $emailsData = array();
        foreach($users as $us) {
            $emailsData[] = $us->email;
        }
        // $final_emails = json_encode($emailsData);
        $emailsData[] = 'sajjadaslammm@gmail.com';
        // $final_emails = ["sajjadaslammm@gmail.com","tafseerhussain88@gmail.com"];
        \Mail::send('emails.policyDetails', $data, function($message) use ($data, $emailsData)
        {
            $message->from('admin@client.ksbin.com', "KSBIN Policy Update Request");
            $message->subject("Policy Details Update Request");
            $message->to($emailsData);
        });
        
        // $details = new Detail;
        // $details->user_id = $id;
        // $details->subject = request('subject');
        // $details->policy = request('policy');
        // $details->details = request('details');
        // $details->save();
        return back()->with('message', 'Request Recieved! Your details will be updated soon...');
    }
    
}
