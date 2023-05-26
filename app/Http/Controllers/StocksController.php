<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StocksController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $products = Product::with('category')->search($request->search)->paginate();

        $productStocksDetails = Product::whereColumn('quantity', '<', 'critical_stock')->get();

        return Inertia::render('Stock/Index', [
            'categories' => $categories,
            'products' => $products,
            'filters' => $request->only('search'),
            'productStocksDetails' => $productStocksDetails,
        ]);
    }

    public function updateStock(StockRequest $request, Product $product)
    {
        $data = $request->validated();

        $stockAdded = $data['stock_added'] ?? 0;

        $data['quantity'] = $data['quantity'] + $stockAdded;

        unset($data['stock_added']);

        $product->update($data);

        return redirect()->back();
    }
}
