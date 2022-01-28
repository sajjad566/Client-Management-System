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

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $totalUsers = count(User::all());
        $totalAdmins = count(User::where('role_id', 1)->get());
        $totalInvoices = count(Invoice::all());
        $totalContracts = count(Contract::all());
        $totalUserInvoices = count(UserInvoice::all());
        $attachments = count(Attachment::all());
        $claims = count(Claim::all());
        return view('admin.index', compact(['totalUsers', 'totalInvoices', 'totalContracts', 'totalUserInvoices', 'attachments', 'claims', 'totalAdmins']));
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
        return view('admin.adminControls.edit');
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
        $validated = request()->validate([
            'adminName' => 'required',
            'adminEmail' => 'required|email',
        ]);
        $user->name = request('adminName');
        $user->email = request('adminEmail');
        
        if (request('adminPass') != '' || request('newPass') != '') {

            $pass = request('adminPass');

            if (Hash::check($pass, $user->password)) {
                 request()->validate([
                    'newPass' => 'required|min:6',
                ]);
                 $newPass = request('newPass');
                 $user->password = Hash::make($newPass);

            } else {
                return back()->with('error', 'Old Password Not Matched');
            }
        
        }

        $user->save();
        return back()->with('message', 'Profile Updated!');
    }

    // VIEW ALL USERS
    public function allUsers()
    {
        $users = User::all();
        return view('admin.viewUsers', compact('users'));
    }
    
    // VIEW ALL ADMINS
    public function allAdmins()
    {
        $users = User::where('role_id', 1)->get();
        return view('admin.adminUsers', compact('users'));
    }
    
    public function addAdmin(Request $request)
    {
        $validated = request()->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role_id = 1;
        $user->save();
        
        return back()->with('success', 'New Admin Added Successfully!');
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

    // Assign policy Number
    public function assign($id)
    {
        $user = User::findOrFail($id);
        $user->policy_number = request('policy_number');
        $user->save();
        return back()->with('success', 'Policy Number Updated');
    }

    // REMOVE USER
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->invoices()->delete();
        $user->contracts()->delete();
        $user->userInvoices()->delete();
        $user->attachments()->delete();
        $user->delete();
        return back();
    }
    
    public function sendMail()
    {
        $data = [
            'name' => 'sajjad',    
        ];
        \Mail::send('emails.newUser', $data, function($message) use ($data)
        {
            $message->from('amit@client.ksbin.com', "KSBIN New User");
            $message->subject("New User Registration");
            $message->to('sajjadaslammm@gmail.com');
        });
        echo "Email Sent";
    }
    
    // User Profiles For Admin
    public function userProfile($id) 
    {
        $user = User::findOrFail($id);
        $policy = Policy::where('user_id', $id)->get();
        // echo "Name : ".$user->name."</br>";
        // echo "Email : ".$user->email."</br>";
        // echo "Policy Number : ".$user->policy_number."</br>";
        
        // echo "</br>User Invoices: </br>";
        // foreach($user->invoices as $userInvoice) {
        //     echo "<a target='_blank' href='https://docs.google.com/viewerng/viewer?url=http://client.ksbin.com/storage/".$userInvoice->invoice."&embedded=true'> Invoice ".$userInvoice->id." </a></br>";
        // }
        
        // echo "</br>User Contracts: </br>";
        
        return view('admin.userProfiles', compact(['user','policy']));
    }
    
    // Add User Profile Image
    public function userProfileImage(Request $request, $id) 
    {
        $user = User::findOrFail($id);
        $validated = request()->validate([
            'user_image' => 'required|max:3072|',
        ]);
        $image = $request->user_image;
        if($request->has('user_image')) {
            
            $imageName = $request->file('user_image')->store("avatars");
           
            $user->user_image = $imageName;
            $user->save();
        }
        return back()->with('success', 'Profile Image Updated!');
    }
    
    // Add User Policy Details
    public function userPolicyDetails(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if($user->policies->count() < 8)
        {
            $policy = new Policy;
            $validated = request()->validate([
                'policy_number' => 'required', 
                'policy_from' => 'required', 
                'policy_to' => 'required', 
            ]);
            $policy->user_id = $id;
            $policy->policy_number = $request->policy_number;
            $policy->policy_from = $request->policy_from;
            $policy->policy_to = $request->policy_to;
            $policy->save();
            return back()->with('success', 'Policy Details Updated!');
        }
        else
        {
            return back()->with('error', 'You have reached the maximum number of policies for this user!');
        }
        
    }
    // Update User Policy Details
    public function userPolicyDetailsUpdate(Request $request, $id)
    {
        $policy = Policy::findOrFail($id);
        $validated = request()->validate([
            'policy_number' => 'required', 
            'policy_from' => 'required', 
            'policy_to' => 'required', 
        ]);
        $policy->policy_number = $request->policy_number;
        $policy->policy_from = $request->policy_from;
        $policy->policy_to = $request->policy_to;
        $policy->save();
        return back()->with('success', 'Policy Details Updated!');
    }
    
    // User Policy Details (Attachment)
    public function policyDetailsUpate()
    {
        $details = Detail::all();
        return view('/admin.details', compact(['details']));
    }
    
    // Remove Details
    public function deleteDetails($id)
    {
        $detail = Detail::findOrFail($id);
        $detail->delete();
        return back()->with('success', 'Details Removed!');
    }

    
}