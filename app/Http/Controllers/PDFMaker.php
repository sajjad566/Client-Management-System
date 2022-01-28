<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use Auth;
use PDF;
use App\Contract;
use App\User;

class PDFMaker extends Controller
{
    function printContract($id) {
        $contract = Contract::findOrFail($id);
        // dd($contract->userSign);
        $cn = $contract->contract;
        $user = Auth::user();
        $pdf = PDF::loadView($cn , compact('contract'))->setPaper('a4', 'potrait');
        $c = $contract->id.str_random(40).'.'.'pdf';
        return $pdf->download($c);
    }
    function gen()
    {
    	$user = Auth::user();
    	$data = compact('user');
        

	// $content = $pdf->output();

//	\Storage::put('csv/name2.pdf',$content) ;


        return view('pdf', compact(['user']));

   //  	$pdf = App::make('dompdf.wrapper');
   //  	$pdf->loadHTML('<h1 style="background:gray;">PDF Heading</h1>
			// <img width="150px" src="/clientBrocker/storage/signs/sJpJxseoJfJUC8qo4mhbBYhhUXlSQzQDrPRE4u44.png">
			// <input type="text">
   //  		');
   //  	return PDF::setOptions(['isRemoteEnabled' => true])->stream();
   //  	echo '<img width="150px" src="/clientBrocker/storage/signs/sJpJxseoJfJUC8qo4mhbBYhhUXlSQzQDrPRE4u44.png">';
    }
    function signNow()
    {
        $user = Auth::user();
        request()->validate([
            'user-signature' => 'required',
        ]);
        $sign = request('user-signature');
        $image = $sign;  // your base64 encoded
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = str_random(40).'.'.'png';
        
        \File::put($_SERVER['DOCUMENT_ROOT']. '/storage/signs/' . $imageName, base64_decode($image));
        
        return back()->with('imageName', $imageName);
    }
}
