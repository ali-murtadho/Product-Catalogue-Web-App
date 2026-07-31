<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Display the cart page with Livewire CartManager component.
     */
    public function index(): View
    {
        return view('public.cart');
    }
}
