<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\MembershipUser;
use App\Models\Transaction;
use App\Models\Order;
use App\Models\Cart;
use Illuminate\Validation\Rule;
use Session;
use Auth;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "Histori Transaksi";
        $auth = Auth::user();

        $transactions = Transaction::with('orders')->where('user_id', $auth->id)->latest()->get();
        return view('transactions.index', compact('transactions', 'title', 'auth'))->with('i');
    }
    
    public function report()
    {
        $title = "Manajemen Transaksi";
        $auth = Auth::user();

        $transactions = Transaction::with('orders')->latest()->get();
        return view('transactions.report', compact('transactions', 'title', 'auth'))->with('i');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $auth = Auth::user();

        $carts = Cart::with('product')->where('user_id', $auth->id)->get();

        $member = MembershipUser::where('user_id', $auth->id)->first();
        
        if ($member && $member->end_date > now()) {
            $member_check = true;
            $subtotal = $carts->sum(function ($order) {
                return $order->product->member_price * $order->qty;
            });
            $discount = $carts->sum(function ($order) {
                return ($order->product->member_price * $order->product->discount / 100) * $order->qty;
            });
        } else {
            $member_check = false;
            $subtotal = $carts->sum(function ($order) {
                return $order->product->price * $order->qty;
            });
            $discount = $carts->sum(function ($order) {
                return ($order->product->price * $order->product->discount / 100) * $order->qty;
            });
        }

        $subtotal -= $discount;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalExtension();
            $image->move(public_path('images/transactions'), $imageName);
        }

        $transaction = Transaction::create([
            'user_id' => $auth->id,
            'subtotal' => $subtotal,
            'tax' => 2500,
            'total' => $subtotal + 2500,
            'image' => $imageName,
            'status' => 'Menunggu Verifikasi', 
        ]);

        if(!$transaction){
            Session::flash('failed', 'Terdapat Kesalahan!');
            return redirect()->route('orders.index');
        }
        
        foreach ($carts as $cart) {
            $order = Order::create([
                'transaction_id' => $transaction->id,
                'product_id' => $cart->product_id,
                'user_id' => $cart->user_id,
                'qty' => $cart->qty,
            ]);
        }

        Session::flash('success', 'Transaksi berhasil, silahkan tunggu verifikasi dari admin!');
        return redirect()->route('transactions.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $title = "Detail Transaksi";
        $auth = Auth::user();
        $transaction = Transaction::findOrFail($id);
        $orders = Order::with('product')->where('transaction_id', $transaction->id)->get();

        $member = MembershipUser::where('user_id', $auth->id)->first();
        
        if ($member && $member->end_date > now()) {
            $member_check = true;
            $subtotal = $orders->sum(function ($order) {
                return $order->product->member_price * $order->qty;
            });
            $discount = $orders->sum(function ($order) {
                return ($order->product->member_price * $order->product->discount / 100) * $order->qty;
            });
        } else {
            $member_check = false;
            $subtotal = $orders->sum(function ($order) {
                return $order->product->price * $order->qty;
            });
            $discount = $orders->sum(function ($order) {
                return ($order->product->price * $order->product->discount / 100) * $order->qty;
            });
        }

        $total = $subtotal - $discount;
        
        return view('transactions.show', compact('orders', 'auth', 'title', 'member_check', 'total'))->with('i');
    }

    public function edit($id)
    {
        $title = "Detail Transaksi";
        $auth = Auth::user();
        $transaction = Transaction::findOrFail($id);
        $orders = Order::with('product')->where('transaction_id', $transaction->id)->get();

        $member = MembershipUser::where('user_id', $auth->id)->first();
        
        if ($member && $member->end_date > now()) {
            $member_check = true;
            $subtotal = $orders->sum(function ($order) {
                return $order->product->member_price * $order->qty;
            });
            $discount = $orders->sum(function ($order) {
                return ($order->product->member_price * $order->product->discount / 100) * $order->qty;
            });
        } else {
            $member_check = false;
            $subtotal = $orders->sum(function ($order) {
                return $order->product->price * $order->qty;
            });
            $discount = $orders->sum(function ($order) {
                return ($order->product->price * $order->product->discount / 100) * $order->qty;
            });
        }

        $total = $subtotal - $discount;
        
        return view('transactions.edit', compact('orders', 'auth', 'title', 'member_check', 'transaction', 'total'))->with('i');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $transaction = transaction::findOrFail($id);

        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'duration' => 'required|numeric',
        ]);

        $transaction->update($request->all());

        Session::flash('success', 'transaction updated successfully!');
        return redirect()->route('transactions.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id)->update([
            'status' => 'Dibatalkan',
        ]);

        Session::flash('success', 'Transaksi Dibatalkan!');
        return redirect()->route('transactions.report');
    }

    public function send(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id)->update([
            'status' => 'Sedang Dikirim',
        ]);

        Session::flash('success', 'Transaksi Disetujui dan Sedang Dikirim!');
        return redirect()->route('transactions.report');
    }

    public function done(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id)->update([
            'status' => 'Selesai',
        ]);

        Session::flash('success', 'Transaksi Selesai!');
        return redirect()->route('transactions.index');
    }
}
