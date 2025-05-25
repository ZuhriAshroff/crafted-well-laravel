<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Get some stats for the admin dashboard
        $usersCount = \App\Models\User::count();
        $ordersCount = \App\Models\Order::count();
        $productsCount = \App\Models\Product::count();

        return view('admin.dashboard', [
            'usersCount' => $usersCount,
            'ordersCount' => $ordersCount,
            'productsCount' => $productsCount,
        ]);
    }
}