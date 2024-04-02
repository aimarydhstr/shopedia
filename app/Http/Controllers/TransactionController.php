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
        $title = "Transaction Management";
        $auth = Auth::user();

        $transactions = Transaction::latest()->get();
        return view('transactions.index', compact('transactions', 'title', 'auth'))->with('i');
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

        $subtotal -= $discount;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalExtension();
            $image->move(public_path('images/transactions'), $imageName);
        }

        $transaction = Transaction::create([
            'subtotal' => $subtotal,
            'tax' => 2500,
            'total' => $subtotal + 2500,
            'image' => $imageName,
            'status' => 'Proses', 
        ]);

        if(!$transaction){
            Session::flash('failed', 'Terdapat Kesalahan!');
            return redirect()->route('carts.index');
        }
        
        foreach ($carts as $cart) {
            $order = Order::create([
                'transaction_id' => $transaction->id,
                'product_id' => $cart->product_id,
                'user_id' => $cart->user_id,
                'qty' => $cart->qty,
            ]);
        }

        Session::flash('success', 'transaction added successfully!');
        return redirect()->route('transactions.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title = "Edit transaction";
        $auth = Auth::user();

        $transaction = transaction::findOrFail($id);
        return view('transactions.edit', compact('transaction', 'title', 'auth'));
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
    public function destroy($id)
    {
        $transaction = transaction::findOrFail($id);
        $transaction->delete();

        Session::flash('success', 'transaction deleted successfully!');
        return redirect()->route('transactions.index');
    }

    public function my()
    {
        $title = 'Histori transaction';
        $auth = Auth::user();
        $transactions = transactionTransaction::with('transaction')->where('user_id', $auth->id)->latest()->get();

        foreach ($transactions as $transaction) {
            if ($transaction->status == 'Selesai') {
                $transaction->end_date = $transaction->updated_at->format('d F Y');
            }
        }

        $member = transactionUser::where('user_id', $auth->id)->first();

        return view('transactions.my', compact('transactions', 'auth', 'title', 'member'))->with('i');
    }

    public function transaction()
    {
        $title = 'Pembelian transaction';
        $auth = Auth::user();
        $transactions = transaction::all();

        return view('transactions.transaction', compact('transactions', 'auth', 'title'));
    }
    
    public function purchase(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'image' => 'required|image|max:2048',
        ]);

        $user = Auth::user();

        $transaction = new transactionTransaction();
        $transaction->user_id = $user->id;
        $transaction->transaction_id = $request->transaction_id;
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalExtension();
            $image->move(public_path('image/transactions'), $imageName);
            $transaction->image = $imageName;
        }

        $transaction->status = "Proses";
        
        $transaction->save();

        Session::flash('success', 'Pembelian transaction berhasil, tunggu konfirmasi!');

        return redirect()->route('transactions.my');
    }

    public function list()
    {
        $title = 'Transaksi transaction';
        $auth = Auth::user();
        $transactions = transactionTransaction::with('transaction', 'user')->latest()->get();

        foreach ($transactions as $transaction) {
            if ($transaction->status == 'Selesai') {
                $transaction->end_date = $transaction->updated_at->format('d F Y');
            }
        }

        return view('transactions.list', compact('transactions', 'auth', 'title'));
    }
    
    public function activation($id)
    {
        $title = 'Aktivasi transaction';
        $auth = Auth::user();
        $transactions = transactionTransaction::with('transaction', 'user')->findOrFail($id);

        return view('transactions.activation', compact('transactions', 'auth', 'title'));
    }

    public function activate(Request $request, $id)
    {
        $transaction = transactionTransaction::findOrFail($id);

        $request->validate([
            'status' => 'required'
        ]);

        if($request->status == 'Selesai'){
            $transaction_user = transactionUser::where('user_id', $transaction->user_id)->first();

            if($transaction_user){
                if($transaction_user->end_date > now()){
                    $start = $transaction_user->end_date;
                } else {
                    $start = now();
                }

                $end = $start->copy()->addMonths($transaction->transaction->duration);
                $transaction_user->update([
                    'start_date' => $start,
                    'end_date' => $end,
                ]);
            } else {
                $start = now();

                $end = $start->copy()->addMonths($transaction->transaction->duration);
                transactionUser::create([
                    'user_id' => $transaction->user_id,
                    'start_date' => $start,
                    'end_date' => $end,
                ]);
            }
        }

        $transaction->update([
            'status' => $request->status,
        ]);

        Session::flash('success', 'transaction updated successfully!');
        return redirect()->route('transactions.list');
    }
}
