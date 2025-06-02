<?php

namespace App\Http\Controllers;

use App\Models\Inspector;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PDFController extends Controller
{
    public function generatePDF()
    {
        $inspectors = Inspector::all(); // Fetch all inspectors

        // Load the existing Blade view with data
        $pdf = Pdf::loadView('manage-inspector', compact('inspectors'));

        // Return the PDF for download
        return $pdf->download('inspectors_report.pdf');
    }
}
