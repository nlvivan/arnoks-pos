<?php

namespace App\Http\Controllers;

use App\Models\PointOfSale;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PosController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')->search($request->search)->get();

        return Inertia::render('POS/Index', [
            'products' => $products,
            'filters' => $request->only('search'),
        ]);
    }

    public function store(Request $request)
    {
        $data = [];
        $transactionNumber = uniqid('ARNOKS-', true);
        foreach ($request->items as $item) {

            $product = Product::find($item['product_id']);
            $product->quantity = $product->quantity - $item['quantity'];
            $product->save();

            $obj = [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'total' => $item['quantity'] * $product->price,
                'transaction_number' => $transactionNumber,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            array_push($data, $obj);
        }

        PointOfSale::insert($data);

        return redirect()->back();
    }
}
