<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;

class AccountController extends Controller
{
    public function index()
    {
        // Fetch all accounts for display in accounting index page
        $accountingData = Account::all();
        
        // If you need to load related data, uncomment:
        // $accountingData = Account::with('tour', 'client', 'office')->get();
        
        return view('accounting.index', compact('accountingData'));
    }

    public function balanceSheet()
    {
        $accounts = Account::all();
        return view('accounts.balance_sheet', compact('accounts'));
    }
}