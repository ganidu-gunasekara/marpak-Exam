<?php

// app/Http/Controllers/TransactionController.php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    protected $service;

    public function __construct(TransactionService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $user = Auth::user();
        $transactions = $this->service->getTransactions($user);
        return view('dashboard', compact('user', 'transactions'));
    }

    public function topUp(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:1']);
        $this->service->topUp(Auth::user(), $request->amount);
        return back()->with('success', 'Account topped up!');
    }

    public function payBill(Request $request)
    {
        $request->validate([
            'biller' => 'required|in:electricity,water,internet',
            'amount' => 'required|numeric|min:1'
        ]);

        try {
            $this->service->payBill(Auth::user(), $request->biller, $request->amount);
            return back()->with('success', 'Bill paid successfully!');
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'recipient_email' => 'required|email|exists:users,email',
            'amount' => 'required|numeric|min:1'
        ]);

        try {
            $this->service->transfer(Auth::user(), $request->recipient_email, $request->amount);
            return back()->with('success', 'Transfer successful!');
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }
    }
}
