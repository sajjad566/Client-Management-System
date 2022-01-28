<?php

namespace App\Http\Controllers;

use App\Contract;
use App\User;
use Illuminate\Http\Request;
use Auth;
use PDF;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contracts = Contract::all();
        $users = User::all();
        return view('admin.contracts.index', compact(['contracts', 'users']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $user = User::findOrFail($id);
        return view('admin.contracts.create', compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $contract = new Contract;
        $contract->validateContract($request);
        $contract->storeContract($request,$contract);
        $contract->updateContract($request,$contract);
        // dd(request('paid'));
        return back()->with('message','Contract Added!');
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
    public function update(Request $request, $id)
    {
        $contract = Contract::findOrFail($id);
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
        $contract = Contract::findOrFail($id);
        $contract->delete();
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
    public function signContract1(Request $request, $userID, $contractID)
    {
        $user = User::where('id', $userID)->first();
        $contract = Contract::where('id', $contractID)->first();
        // request()->validate([
        //     'user-signature' => 'required',
        // ]);
        $sign = request('user-signature');
        // $contract->signed = $sign;
        // $contract->save();

        $image = $sign;  // your base64 encoded 
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = $contract->id.str_random(40).'.'.'png';
        \File::put($_SERVER['DOCUMENT_ROOT']. '/storage/signs/' . $imageName, base64_decode($image));
        
        $contract->signed = 1;
        $contract->userSign = $imageName;
        
        $sign = request('user-contract');
        $image = $sign;
        $image = explode(";", $image)[1];
        $image = explode(",", $image)[1];
        // $image = str_replace('data:image/png;base64,', '', $sign);
        // $image = str_replace(' ', '+', $image);
        $imageName = $contract->id.str_random(40).'.'.'png';
        $contract->contract = $imageName;
        
        \File::put($_SERVER['DOCUMENT_ROOT']. '/storage/contracts/' . $imageName, base64_decode($image));
        
        $contract->save();
        
         $data = array(
            'user' => $user,
            'document' => $contract->contract,
            'email' => $contract->email,
        );
        \Mail::send('emails.newConByUser', $data, function($message) use ($data)
        {
            $message->from('amit@ksbin.com', "KSBIN Contract Signed");
            $message->subject("Contract Signed");
            $message->to(['sajjadaslammm@gmail.com','insurance@ksbin.com']);
        });
        
        return back()->with('message','Contract Signed!');
    }
}
