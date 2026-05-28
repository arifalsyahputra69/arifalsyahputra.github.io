<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductSerial;
use App\Models\Category;
use App\Models\Size;

class ProductController extends Controller
{

    /*
    |----------------------------------
    | INDEX
    |----------------------------------
    */

    public function index(Request $request)
    {
        $query = Product::with(['variants','category']);

        if($request->search){
            $query->where(function($q) use ($request){
                $q->where('brand','like','%'.$request->search.'%')
                  ->orWhere('type','like','%'.$request->search.'%')
                  ->orWhere('color','like','%'.$request->search.'%');
            });
        }

        if($request->category){
            $query->where('category_id', $request->category);
        }

        $products   = $query->latest()->paginate(40);
        $categories = Category::all();

        if($request->ajax()){
            return view('admin.products.partials.table', compact('products','categories'))->render();
        }

        return view('admin.products.index', compact('products','categories'));
    }


    /*
    |----------------------------------
    | CREATE
    |----------------------------------
    */

    public function create()
    {
        $categories = Category::all();
        $sizes      = Size::with('category')->get();

        return view('admin.products.create', compact('categories','sizes'));
    }


    /*
    |----------------------------------
    | STORE
    |----------------------------------
    */

    public function store(Request $request)
    {
        $request->merge([
            'cost_price' => preg_replace('/[^0-9]/', '', $request->cost_price)
        ]);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand'       => 'required|string|max:255',
            'type'        => 'nullable|string|max:255',
            'color'       => 'nullable|string|max:100',
            'cost_price'  => 'required|numeric|min:0'
        ]);

        $existingProduct = Product::where('category_id', $request->category_id)
            ->whereRaw('LOWER(brand) = ?', [strtolower($request->brand)])
            ->whereRaw('LOWER(color) = ?', [strtolower($request->color ?? '')])
            ->when($request->filled('type'), fn($q) => $q->whereRaw('LOWER(type) = ?', [strtolower($request->type)]))
            ->first();

        if ($existingProduct) {
            $label = implode(' / ', array_filter([
                $existingProduct->brand,
                $existingProduct->type,
                $existingProduct->color,
            ]));

            return redirect()
                ->back()
                ->withInput()
                ->with('warning', "Produk \"$label\" sudah pernah ditambahkan sebelumnya! (ID: #{$existingProduct->id})");
        }

        DB::transaction(function () use ($request) {

            $category = Category::find($request->category_id);

            $product = Product::create([
                'category_id' => $request->category_id,
                'brand'       => $request->brand,
                'type'        => $request->type,
                'color'       => $request->color,
                'cost_price'  => $request->cost_price
            ]);

            if($category->id == 6){
                if($request->has('peci')){
                    foreach ($request->peci as $row) {
                        if(empty($row['qty'])) continue;
                        $qty = (int)$row['qty'];
                        $variant = ProductVariant::create([
                            'product_id'  => $product->id,
                            'peci_number' => $row['nomor'] ?? null,
                            'peci_height' => $row['tinggi'] ?? null,
                            'stock'       => $qty
                        ]);
                        for ($i = 0; $i < $qty; $i++) {
                            ProductSerial::create([
                                'product_variant_id' => $variant->id,
                                'serial_number'      => strtoupper(uniqid()),
                                'is_sold'            => false
                            ]);
                        }
                    }
                }
            }

            elseif(in_array($category->id, [3,8,9,11,12,13,21,23,25,27,28,33,37])){
                $qty = (int)($request->qty ?? 0);
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'stock'      => $qty
                ]);
                for ($i = 0; $i < $qty; $i++) {
                    ProductSerial::create([
                        'product_variant_id' => $variant->id,
                        'serial_number'      => strtoupper(uniqid()),
                        'is_sold'            => false
                    ]);
                }
            }

            else{
                if($request->has('sizes')){
                    foreach ($request->sizes as $row) {
                        if(empty($row['size'])) continue;
                        $qty = (int)($row['qty'] ?? 0);
                        $variant = ProductVariant::create([
                            'product_id' => $product->id,
                            'size'       => $row['size'],
                            'stock'      => $qty
                        ]);
                        for ($i = 0; $i < $qty; $i++) {
                            ProductSerial::create([
                                'product_variant_id' => $variant->id,
                                'serial_number'      => strtoupper(uniqid()),
                                'is_sold'            => false
                            ]);
                        }
                    }
                }
            }

        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan!');
    }


    /*
    |----------------------------------
    | EDIT
    |----------------------------------
    */

    public function edit(Product $product)
    {
        $categories = Category::all();
        $sizes      = Size::with('category')->get();

        $product->load('variants');

        return view('admin.products.edit', compact('product','categories','sizes'));
    }


    /*
    |----------------------------------
    | UPDATE ✅ ADDITIVE + SUBTRACTIVE STOCK
    |----------------------------------
    */

    public function update(Request $request, Product $product)
    {
        $request->merge([
            'cost_price' => preg_replace('/[^0-9]/', '', $request->cost_price)
        ]);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand'       => 'required|string|max:255',
            'type'        => 'nullable|string|max:255',
            'color'       => 'nullable|string|max:100',
            'cost_price'  => 'required|numeric|min:0'
        ]);

        $existingProduct = Product::where('category_id', $request->category_id)
            ->whereRaw('LOWER(brand) = ?', [strtolower($request->brand)])
            ->whereRaw('LOWER(color) = ?', [strtolower($request->color ?? '')])
            ->when($request->filled('type'), fn($q) => $q->whereRaw('LOWER(type) = ?', [strtolower($request->type)]))
            ->where('id', '!=', $product->id)
            ->first();

        if ($existingProduct) {
            $label = implode(' / ', array_filter([
                $existingProduct->brand,
                $existingProduct->type,
                $existingProduct->color,
            ]));

            return redirect()
                ->back()
                ->withInput()
                ->with('warning', "Produk \"$label\" sudah ada di database! (ID: #{$existingProduct->id})");
        }

        DB::transaction(function () use ($request, $product) {

            $category = Category::find($request->category_id);

            $product->update([
                'category_id' => $request->category_id,
                'brand'       => $request->brand,
                'type'        => $request->type,
                'color'       => $request->color,
                'cost_price'  => $request->cost_price
            ]);

            // mode: 'tambah' atau 'kurang' (dikirim dari form via hidden input)
            $mode = $request->input('stock_mode', 'tambah');

            /*
            ==================================
            PRODUK PECI
            ==================================
            */

            if($category->id == 6){

                if($request->has('peci')){

                    foreach ($request->peci as $row) {

                        $qty = (int)($row['qty'] ?? 0);
                        if($qty <= 0) continue;

                        $variant = $product->variants()
                            ->where('peci_number', $row['nomor'] ?? null)
                            ->first();

                        if($mode === 'kurang'){

                            // ✅ MODE KURANG: hapus serial yang belum terjual sejumlah qty
                            if($variant){
                                $availableStock = $variant->serials()->where('is_sold', false)->count();
                                $toRemove       = min($qty, $availableStock); // tidak boleh kurang dari 0

                                $serialsToDelete = $variant->serials()
                                    ->where('is_sold', false)
                                    ->latest('id')
                                    ->take($toRemove)
                                    ->pluck('id');

                                ProductSerial::whereIn('id', $serialsToDelete)->delete();
                                $variant->decrement('stock', $toRemove);
                            }

                        } else {

                            // ✅ MODE TAMBAH: tambahkan serial baru
                            if($variant){
                                $variant->increment('stock', $qty);
                            } else {
                                $variant = ProductVariant::create([
                                    'product_id'  => $product->id,
                                    'peci_number' => $row['nomor'] ?? null,
                                    'peci_height' => $row['tinggi'] ?? null,
                                    'stock'       => $qty
                                ]);
                            }

                            for ($i = 0; $i < $qty; $i++) {
                                ProductSerial::create([
                                    'product_variant_id' => $variant->id,
                                    'serial_number'      => strtoupper(uniqid()),
                                    'is_sold'            => false
                                ]);
                            }

                        }

                    }

                }

            }

            /*
            ==================================
            PRODUK SIMPLE
            ==================================
            */

            elseif(in_array($category->id, [3,8,9,11,12,13,21,23,25,27,28,33,37])){

                $qty = (int)($request->qty ?? 0);
                if($qty <= 0) return;

                $variant = $product->variants()->first();

                if($mode === 'kurang'){

                    // ✅ MODE KURANG
                    if($variant){
                        $availableStock = $variant->serials()->where('is_sold', false)->count();
                        $toRemove       = min($qty, $availableStock);

                        $serialsToDelete = $variant->serials()
                            ->where('is_sold', false)
                            ->latest('id')
                            ->take($toRemove)
                            ->pluck('id');

                        ProductSerial::whereIn('id', $serialsToDelete)->delete();
                        $variant->decrement('stock', $toRemove);
                    }

                } else {

                    // ✅ MODE TAMBAH
                    if($variant){
                        $variant->increment('stock', $qty);
                    } else {
                        $variant = ProductVariant::create([
                            'product_id' => $product->id,
                            'stock'      => $qty
                        ]);
                    }

                    for ($i = 0; $i < $qty; $i++) {
                        ProductSerial::create([
                            'product_variant_id' => $variant->id,
                            'serial_number'      => strtoupper(uniqid()),
                            'is_sold'            => false
                        ]);
                    }

                }

            }

            /*
            ==================================
            PRODUK SIZE
            ==================================
            */

            else{

                if($request->has('sizes')){

                    foreach ($request->sizes as $row) {

                        if(empty($row['size'])) continue;

                        $qty = (int)($row['qty'] ?? 0);
                        if($qty <= 0) continue;

                        $variant = $product->variants()
                            ->where('size', $row['size'])
                            ->first();

                        if($mode === 'kurang'){

                            // ✅ MODE KURANG
                            if($variant){
                                $availableStock = $variant->serials()->where('is_sold', false)->count();
                                $toRemove       = min($qty, $availableStock);

                                $serialsToDelete = $variant->serials()
                                    ->where('is_sold', false)
                                    ->latest('id')
                                    ->take($toRemove)
                                    ->pluck('id');

                                ProductSerial::whereIn('id', $serialsToDelete)->delete();
                                $variant->decrement('stock', $toRemove);
                            }

                        } else {

                            // ✅ MODE TAMBAH
                            if($variant){
                                $variant->increment('stock', $qty);
                            } else {
                                $variant = ProductVariant::create([
                                    'product_id' => $product->id,
                                    'size'       => $row['size'],
                                    'stock'      => $qty
                                ]);
                            }

                            for ($i = 0; $i < $qty; $i++) {
                                ProductSerial::create([
                                    'product_variant_id' => $variant->id,
                                    'serial_number'      => strtoupper(uniqid()),
                                    'is_sold'            => false
                                ]);
                            }

                        }

                    }

                }

            }

        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil diupdate!');
    }


    /*
    |----------------------------------
    | DESTROY
    |----------------------------------
    */

    public function destroy(Product $product)
    {
        DB::transaction(function () use ($product) {

            foreach ($product->variants as $variant) {
                ProductSerial::where('product_variant_id', $variant->id)->delete();
                $variant->delete();
            }

            $product->delete();

        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus!');
    }

}