<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function viewCA()
    {
        // Add logic to retrieve and display Continuous Assessment data
        return view('students.view_ca');
    }

    public function viewResults()
    {
        // Add logic to retrieve and display exam results
        return view('students.view_results');
    }

    public function exemptionForm()
    {
        return view('students.exemption_form');
    }

    public function withdrawalForm()
    {
        return view('students.withdrawal_form');
    }

    public function changeProgramForm()
    {
        return view('students.change_program_form');
    }

    public function changeStudyModeForm()
    {
        return view('students.change_study_mode_form');
    }

    public function withdrawalDeferment()
    {
        return view('students.withdrawal_deferment');
    }

    public function differedModules()
    {
        return view('students.differed_modules');
    }

    public function viewInvoices()
    {
        // Add logic to retrieve and display invoices for the student
        $invoices = auth()->user()->invoices(); 
        return view('students.view_invoices', compact('invoices'));
    }

    public function viewReceipts()
    {
        // Add logic to retrieve and display receipts for the student
        $receipts = auth()->user()->payments(); 
        return view('students.view_receipts', compact('receipts'));
    }
}
