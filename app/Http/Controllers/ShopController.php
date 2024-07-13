<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\MembershipUser;
use App\Models\Cart;
use App\Models\Review;
use App\Models\Transaction;
use App\Models\Order;
use App\Models\Reply;
use Illuminate\Validation\Rule;
use Session;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = "Home";
        $auth = Auth::user();
        $products = Product::latest()->get();
        $categories = Category::orderBy('name', 'asc')->get();

        $rate = $products->mapWithKeys(function ($product) {
            $averageRate = Review::where('product_id', $product->id)
                ->avg('rate');
        
            return [$product->id => round($averageRate, 1)];
        });

        $member = '';

        if($auth){
            $member = MembershipUser::where('user_id', $auth->id)->first();
        }

        if($member && $member->end_date > now()){
            $member_check = true;
        } else {
            $member_check = false;
        }
        
        return view('shops.index', compact('auth', 'title', 'products', 'categories', 'member_check', 'rate'));
    }

    public function search(Request $request)
    {
        $title = "Search Produk";
        $auth = Auth::user();
        $query = $request->input('query');
        $products = Product::where('name', 'LIKE', "%$query%")->latest()->get();
        $rate = $products->mapWithKeys(function ($product) {
            $averageRate = Review::where('product_id', $product->id)
                ->avg('rate');
        
            return [$product->id => round($averageRate, 1)];
        });
        $categories = Category::orderBy('name', 'asc')->get();
        $member = '';

        if($auth){
            $member = MembershipUser::where('user_id', $auth->id)->first();
        }

        if($member && $member->end_date > now()){
            $member_check = true;
        } else {
            $member_check = false;
        }

        return view('shops.search', compact('auth', 'title', 'products', 'query', 'categories', 'member_check', 'rate'));
    }

    public function category($slug)
    {
        $title = "Kategori Produk";
        $auth = Auth::user();
        $categories = Category::orderBy('name', 'asc')->get();
        $category = Category::where('slug', $slug)->first();
        $products = Product::where('category_id', $category->id)->latest()->get();
        $rate = $products->mapWithKeys(function ($product) {
            $averageRate = Review::where('product_id', $product->id)
                ->avg('rate');
        
            return [$product->id => round($averageRate, 1)];
        });
        $member = '';

        if($auth){
            $member = MembershipUser::where('user_id', $auth->id)->first();
        }

        if($member && $member->end_date > now()){
            $member_check = true;
        } else {
            $member_check = false;
        }

        return view('shops.category', compact('auth', 'title', 'products', 'category', 'categories', 'member_check', 'rate'));
    }
    
    public function product($slug)
    {
        $title = "Detail Produk";
        $auth = Auth::user();
        $product = Product::with('category')->where('slug', $slug)->first();
        $categories = Category::orderBy('name', 'asc')->get();

        $reviews = Review::where('product_id', $product->id)->latest()->get();
        $rate = round($reviews->avg('rate'), 1);
        $admin = User::where('role', 'Admin')->first();
        $order = '';
        if($auth){
            $order = Order::where('user_id', $auth->id)->where('product_id', $product->id)->whereHas('transaction', function ($query) {
                    $query->where('status', 'Selesai');
                })->first();
        }

        $member = '';

        if($auth){
            $member = MembershipUser::where('user_id', $auth->id)->first();
        }

        if($member && $member->end_date > now()){
            $member_check = true;
        } else {
            $member_check = false;
        }

        return view('shops.product', compact('auth', 'title', 'product', 'categories', 'member_check', 'reviews', 'admin', 'rate', 'order'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
