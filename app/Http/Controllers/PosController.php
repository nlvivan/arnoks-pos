<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
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

        $transaction = Transaction::create([
            'transaction_number' => $transactionNumber,
            'cash' => $request->cash,
            'change' => $request->cash - $request->totalAmount,
            'total_amount' => $request->totalAmount,
        ]);

        foreach ($request->items as $item) {

            $product = Product::find($item['product_id']);
            $product->quantity = $product->quantity - $item['quantity'];
            $product->save();

            $obj = [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'total' => $item['quantity'] * $product->price,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            array_push($data, $obj);
        }

        $transaction->pointOfSales()->insert($data);

        $products = Product::with('category')->search($request->search)->get();

        return Inertia::render('POS/Index', [
            'products' => $products,
            'filters' => $request->only('search'),
            'change' => $transaction->change,
        ]);
    }
}
