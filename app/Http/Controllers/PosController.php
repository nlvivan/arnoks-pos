<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Str;

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
            ];

            $transaction->pointOfSales()->create($obj);
        }

        

        $products = Product::with('category')->search($request->search)->get();

        $transactionData = Transaction::with(['pointOfSales', 'pointOfSales.product'])->where('id', $transaction->id)->first();
        $data = [
            'data' => $transactionData,
            'point_of_sales' => $transactionData->pointOfSales
        ];
        $pdf = Pdf::loadView('receipt', $data)->setPaper([0, 0, 500, 800], 'landscape');

        $filename = Str::random().'receipt.pdf';
        
        Storage::disk('public')->put($filename, $pdf->output());
        
        $path = Storage::disk('public')->path($filename);
        // $pdf->save($receiptPath = storage_path("app/".Str::random().'.pdf'));
        return Inertia::render('POS/Index', [
            'products' => $products,
            'filters' => $request->only('search'),
            'change' => $transaction->change,
            'receiptPath' => $filename
        ]);
    }
}
