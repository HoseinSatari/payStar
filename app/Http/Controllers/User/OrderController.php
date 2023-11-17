<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $userRepo = new UserRepository(auth()->user());
        $orders = $userRepo->getOrders();
        return view('user.order'  , compact('orders'));
    }
}
