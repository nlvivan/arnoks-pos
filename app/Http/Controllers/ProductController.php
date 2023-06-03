<?php

namespace App\Http\Controllers;

use App\Exports\ProductExport;
use App\Http\Requests\ProductStoreOrUpdateRequest;
use App\Models\Category;
use App\Models\Location;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = Category::all();
        $locations = Location::all();
        $products = Product::with('category', 'location')->search($request->search)->paginate();

        return Inertia::render('Product/Index', [
            'categories' => $categories,
            'products' => $products,
            'locations' => $locations,
            'filters' => $request->only('search'),
        ]);
    }

    public function store(ProductStoreOrUpdateRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('image', 'public');
        }

        unset($data['hasImage']);

        Product::create($data);

        return redirect()->back();
    }

    public function update(ProductStoreOrUpdateRequest $request, Product $product)
    {
        $data = $request->validated();

        if ($request->hasFile('image') && $data['hasImage']) {
            Storage::delete('public/'.$product->image);
            $data['image'] = $request->file('image')->store('device_type_image', 'public');
        } elseif (! $request->hasFile('image') && $data['hasImage']) {
            unset($data['image']);
        } else {
            Storage::delete('public/'.$product->image);
        }

        unset($data['hasImage']);

        $product->update($data);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->back();
    }

    public function generateStockReport()
    {
        return (new ProductExport)->download('product-stock.xlsx');
    }
}
