<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use App\Models\MembershipUser;
use Illuminate\Validation\Rule;
use Auth;
use Session;

class CartController extends Controller
{
    public function index()
    {
        $title = "Keranjang Saya";
        $auth = Auth::user();
        $carts = Cart::with('product')->where('user_id', $auth->id)->get();

        $member = MembershipUser::where('user_id', $auth->id)->first();
        
        if ($member && $member->end_date > now()) {
            $member_check = true;
            $subtotal = $carts->sum(function ($cart) {
                return $cart->product->member_price * $cart->qty;
            });
            $discount = $carts->sum(function ($cart) {
                return ($cart->product->member_price * $cart->product->discount / 100) * $cart->qty;
            });
        } else {
            $member_check = false;
            $subtotal = $carts->sum(function ($cart) {
                return $cart->product->price * $cart->qty;
            });
            $discount = $carts->sum(function ($cart) {
                return ($cart->product->price * $cart->product->discount / 100) * $cart->qty;
            });
        }

        $total = $subtotal - $discount;
        
        return view('shops.cart', compact('carts', 'auth', 'title', 'member_check', 'total'))->with('i');
    }

    public function store($slug)
    {
        $auth = Auth::user();
        $product = Product::where('slug', $slug)->first();

        $cart = Cart::where('user_id', $auth->id)->where('product_id', $product->id)->first();
        
        if($cart){
            $cart->update(['qty' => $cart->qty + 1]);
        } else {
            Cart::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'qty' => 1,
            ]);
        }
        
        Session::flash('success', 'Produk berhasil ditambahkan ke keranjang!');
        return redirect()->route('carts.index');
    }

    public function add($id)
    {
        $cart = Cart::findOrFail($id);
        
        $cart->update(['qty' => $cart->qty + 1]);
        
        Session::flash('success', 'Jumlah produk berhasil ditambahkan ke keranjang!');
        return redirect()->route('carts.index');
    }
    
    public function remove($id)
    {
        $cart = Cart::findOrFail($id);
        
        $cart->update(['qty' => $cart->qty - 1]);
        
        Session::flash('success', 'Jumlah produk berhasil dikurangi ke keranjang!');
        return redirect()->route('carts.index');
    }
    
    public function destroy($id)
    {
        $cart = Cart::findOrFail($id)->delete();
        
        Session::flash('success', 'Produk di keranjang berhasil hapus!');
        return redirect()->route('carts.index');
    }
}
