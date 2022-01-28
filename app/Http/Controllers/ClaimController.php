<?php

namespace App\Http\Controllers;

use App\Claim;
use App\Invoice;
use App\Contract;
use App\Attachment;
use App\User;
use Illuminate\Http\Request;
use Auth;
use PDF;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class ClaimController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Claim::where('signed', 0)->delete();
        $claims = Claim::where('signed', 1)->get();
        $users = User::all();
        return view('admin.claims.index', compact(['claims', 'users']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::all();
        return view('admin.contracts.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $claim = new Claim;
        $claim->validateClaim($request);
        $claim->storeClaim($request,$claim);
        $claim->updateClaim($request,$claim);
        // dd(request('paid'));
        $user = Auth::user();
        return view('claim', compact(['claim','user']))->with('message','Contract Added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function show(Contract $contract)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function edit(Contract $contract)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $userID, $id)
    {
        $claim = Claim::findOrFail($id);
        if ($contract->signed == 0) {
            $contract->signed = 1;
        } else {
            $contract->signed = 0;
        }
        $contract->save();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $claim = Claim::findOrFail($id);
        Storage::delete('/claims/'.$claim->claim.'');
        $claim->delete();
        return back();
    }

    // ALL USER CONTROLS
    public function allUserContracts()
    {
        $user = Auth::user();
        $contracts = $user->contracts;
        $contractCount = count($contracts);
        return view('contracts.index', compact(['user','contracts', 'contractCount']));
    }

    // Sign Contract Page
    public function signDocument($id)
    {
        $user = Auth::user();
        $contract = Contract::findOrFail($id);
        return view('contracts.sign', compact(['user', 'contract']));
    }

    // Contract Sign
    public function signClaim($userID, $id)
    {
        $request = new Request;
        $user = User::findOrFail($userID);
        $claim = Claim::findOrFail($id);
        request()->validate([
            'user-signature' => 'required',
        ]);
        $sign = request('user-signature');
        // $contract->signed = $sign;
        // $contract->save();

        $image = $sign;  // your base64 encoded 
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = $claim->id.str_random(40).'.'.'png';
        \File::put($_SERVER['DOCUMENT_ROOT']. '/storage/signs/' . $imageName, base64_decode($image));
        
        $claim->signed = 1;
        $claim->userSign = $imageName;
        
        $sign = request('user-claim');
        $image = $sign;
        $image = explode(";", $image)[1];
        $image = explode(",", $image)[1];
        // $image = str_replace('data:image/png;base64,', '', $sign);
        // $image = str_replace(' ', '+', $image);
        $imageName = $claim->id.str_random(40).'.'.'png';
        $claim->claim = $imageName;
        
        \File::put($_SERVER['DOCUMENT_ROOT']. '/storage/claims/' . $imageName, base64_decode($image));
        
        $claim->save();
        
         $data = array(
            'user' => $user,
            'document' => $claim->claim,
            'email' => $claim->email,
        );
        \Mail::send('emails.newClaimByUser', $data, function($message) use ($data)
        {
            $message->from('amit@ksbin.com', "KSBIN Contract Signed");
            $message->subject("Claim A File");
            $message->to(['sajjadaslammm@gmail.com', 'insurance@ksbin.com']);
        });
        
        $invoices = Auth::user()->invoices;
    	$invoiceCount = count($invoices);
    	$contracts = Auth::user()->contracts;
    	$contractCount = count($contracts);
    	$attachments = Auth::user()->attachments;
    	$user = Auth::user();
    	$userDocuments = $user->userInvoices;
        return Redirect::route('welcome')->with( ['message' => 'Thank you! Your File has been submitted. We will contact you shortly.'] );
    }
}