<?php

namespace App\Http\Controllers;
use PDF;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function createPDF(Request $request)
    {
        // set certificate file
        $certificate = 'file://'.base_path().'/public/tcpdf.crt';

        // set additional information in the signature
        $info = array(
            'Name' => 'TCPDF',
            'Location' => 'Office',
            'Reason' => 'Testing TCPDF',
            'ContactInfo' => 'http://www.tcpdf.org',
        );

        // set document signature
        PDF::setSignature($certificate, $certificate, 'tcpdfdemo', '', 2, $info);
        
        PDF::SetFont('helvetica', '', 12);
        PDF::SetTitle('Hello World');
        PDF::AddPage();

        // print a line of text
        $text = view('tcpdf');

        // add view content
        PDF::writeHTML($text, true, 0, true, 0);

        // add image for signature
        PDF::Image('tcpdf.png', 180, 60, 15, 15, 'PNG');
        
        // define active area for signature appearance
        PDF::setSignatureAppearance(180, 60, 15, 15);
        
        // save pdf file
        PDF::Output(public_path('hello_world.pdf'), 'F');

        PDF::reset();

        dd('pdf created');
    }
}
