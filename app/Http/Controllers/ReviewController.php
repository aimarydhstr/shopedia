<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\User;
use App\Models\Product;
use App\Models\Reply;
use Illuminate\Http\Request;
use Session;

class ReviewController extends Controller
{
    public function store(Request $request, $slug)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rate' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
        ]);

        $product = Product::where('slug', $slug)->first();

        Review::create([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'rate' => $request->rate,
            'comment' => $request->comment,
        ]);

        Session::flash('success', 'Review berhasil ditambahkan!');
        return redirect()->route('shops.product', $slug);
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|string',
        ]);

        $review = Review::with('product')->findOrFail($id);

        Reply::create([
            'review_id' => $id,
            'user_id' => auth()->id(),
            'reply' => $request->reply,
        ]);

        Session::flash('success', 'Balasan berhasil ditambahkan!');
        return redirect()->route('shops.product', $review->product->slug);
    }
}
