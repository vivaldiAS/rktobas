<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Merchants; // bukan App\Models\Merchant
use Illuminate\Http\Request;

class TokoAPIController extends Controller
{
    public function show($id)
{
    $merchant = Merchants::with(['address', 'products.productImages'])->where('merchant_id', $id)->first();

    if (!$merchant) {
        return response()->json([
            'success' => false,
            'message' => 'Toko tidak ditemukan.',
        ], 404);
    }

    $products = $merchant->products->map(function ($product) {
        // Ambil gambar pertama, jika ada
        $imageName = $product->productImages->first()->product_image_name ?? 'default.jpg';

        return [
            'id' => $product->product_id,
            'merchant_id' => $product->merchant_id,
            'category_id' => $product->category_id,
            'name' => $product->product_name,
            'description' => $product->product_description,
            'price' => $product->price,
            'heavy' => $product->heavy,
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
            'type' => $product->type,
            'subdistrict_name' => optional($product->merchant)->address->subdistrict_name ?? null,
            'count_product_purchases' => $product->count_product_purchases ?? 0,
            'product_image_name' => $imageName,
        ];
    });

    return response()->json([
        'success' => true,
        'data' => [
            'merchant_id' => $merchant->merchant_id,
            'user_id' => $merchant->user_id,
            'nama_merchant' => $merchant->nama_merchant,
            'deskripsi_toko' => $merchant->deskripsi_toko,
            'kontak_toko' => $merchant->kontak_toko,
            'foto_merchant' => asset('storage/' . $merchant->foto_merchant),
            'is_verified' => $merchant->is_verified,
            'on_vacation' => $merchant->on_vacation,
            'is_closed' => $merchant->is_closed,
            'created_at' => $merchant->created_at,
            'updated_at' => $merchant->updated_at,
            'alamat' => $merchant->address ? [
                'province_name' => $merchant->address->province_name,
                'city_name' => $merchant->address->city_name,
                'subdistrict_name' => $merchant->address->subdistrict_name,
                'street_address' => $merchant->address->merchant_street_address,
            ] : null,
            'produk' => $products,
        ],
    ]);
}
}
