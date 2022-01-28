<?php

namespace App\Http\Controllers;

use App\Renewal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\User;

class RenewalController extends Controller
{
    public function index()
    {
        $renewals = Renewal::all();
        $users = User::all();
        return view('admin.renewals.index', compact(['renewals','users']));
    }
    
    public function store(Request $request, $uid)
    {
        $renewal = new Renewal;
        $renewal->validateRenewal($request);
        
        if(request('dmv') == "" && request('tlc') == "" && request('ddc') == "") {
            
            return back()->with('renewalError','Please Add Atleast One File For Renewal!');
        
            
        } else {
            
            $renewal->storeRenewal($request,$renewal, $uid);
            $renewal->updateRenewal($request,$renewal, $uid);
            
        }
        
        return back()->with('message','Renewal Request Sent!');
    }
    
    public function destroy($id)
    {
        $renewal = Renewal::findOrFail($id);
        Storage::delete('/'.$renewal->dmv.'');
        Storage::delete('/'.$renewal->tlc.'');
        Storage::delete('/'.$renewal->ddc.'');
        $renewal->delete();
        return back();
    }
    
    public function setPrice($uid, $id)
    {
        $renewal = Renewal::findOrFail($id);
        request()->validate([
            'renewal-price' => 'required',
        ]);
        $renewal->price = request('renewal-price');
        $renewal->status = 0;
        $renewal->save();
        return back()->with('message', 'Price Updated!');
    }
}