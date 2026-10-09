<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\SellerAccountService;
use Illuminate\Http\Request;

class SellerAccountController extends Controller
{
    public function __construct(private SellerAccountService $sellerAccounts)
    {
    }

    public function index()
    {
        $today = now()->toDateString();

        $sellers = User::query()
            ->role('Seller')
            ->where('status', 'active')
            ->orderBy('name')
            ->get()
            ->map(function (User $seller) use ($today) {
                $summary = $this->sellerAccounts->summary($seller, $today);
                $seller->daily_totals = $summary['totals'];
                return $seller;
            });

        return view('seller_accounts.index', compact('sellers', 'today'));
    }

    public function show(Request $request, User $seller)
    {
        abort_unless($seller->hasRole('Seller'), 404);

        $date = $request->validate([
            'date' => ['nullable', 'date'],
        ])['date'] ?? now()->toDateString();

        $summary = $this->sellerAccounts->summary($seller, $date);

        return view('seller_accounts.show', $summary);
    }
}
