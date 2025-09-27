<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;

class AccountController extends Controller
{
    //
    
public function index()
{
    $accounts = Account::all();
    return view('accounts.index', compact('accounts'));
}

public function balanceSheet()
{
    $accounts = Account::all();
    return view('accounts.balance_sheet', compact('accounts'));
}
}
