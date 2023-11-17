<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $productsRepo = new ProductRepository(new Product());
        $allProducts = $productsRepo->getAll();
        return view('product.index', compact('allProducts'));
    }

    public function create(Request $request)
    {
        // this method not really only for testing
        $validData = $request->validate([
            'title' => ['required', 'string'],
            'price' => ['required', 'numeric'],
            'inventory' => ['required', 'integer'],
            'poster' => ['nullable', 'string'],
        ]);

        $productRepo = new ProductRepository(new Product());
        $productRepo->create($validData);

        return back();
    }
}
