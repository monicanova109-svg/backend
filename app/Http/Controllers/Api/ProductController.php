<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    private function rules(Request $request): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,category_id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ];

        if ($request->hasFile('img')) {
            $rules['img'] = 'image|mimes:jpg,jpeg,png,webp|max:2048';
        }

        return $rules;
    }

    private function messages(): array
    {
        return [
            'name.required' => 'Nama produk wajib diisi',
            'category_id.required' => 'Kategori wajib dipilih',
            'category_id.exists' => 'Kategori tidak valid',
            'price.required' => 'Harga produk wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'stock.required' => 'Stok produk wajib diisi',
            'stock.integer' => 'Stok harus berupa angka bulat',
            'img.image' => 'File harus berupa gambar',
        ];
    }

    // GET /api/products?page=1&limit=10&category_id=1&keyword=laskar
    public function index(Request $request)
    {
        $limit = (int) $request->query('limit', 10);

        $products = Product::with('category')
            ->when($request->filled('category_id'), fn ($q) =>
                $q->where('category_id', $request->category_id))
            ->when($request->filled('keyword'), fn ($q) =>
                $q->where('name', 'like', '%' . $request->keyword . '%'))
            ->orderBy('id')
            ->paginate($limit);

        return response()->json($products);
    }

    // GET /api/products/{id}
    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);

        return response()->json(['data' => $product]);
    }

    // POST /api/products
    public function store(Request $request)
    {
        $data = $request->validate($this->rules($request), $this->messages());

        if ($request->hasFile('img')) {
            $data['img'] = $request->file('img')->store('products', 'public');
        } else {
            unset($data['img']);
        }

        $product = Product::create($data);

        return response()->json([
            'message' => 'Produk berhasil disimpan',
            'data' => $product->load('category'),
        ], 201);
    }

    // POST /api/products/{id}?_method=PUT
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $data = $request->validate($this->rules($request), $this->messages());

        if ($request->hasFile('img')) {
            if ($product->img) {
                Storage::disk('public')->delete($product->img);
            }
            $data['img'] = $request->file('img')->store('products', 'public');
        } else {
            unset($data['img']);
        }

        $product->update($data);

        return response()->json([
            'message' => 'Produk berhasil diperbarui',
            'data' => $product->load('category'),
        ], 200);
    }

    // DELETE /api/products/{id}
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->img) {
            Storage::disk('public')->delete($product->img);
        }

        $product->delete();

        return response()->json(['message' => 'Produk berhasil dihapus']);
    }
}