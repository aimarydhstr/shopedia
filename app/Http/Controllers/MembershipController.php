<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Membership;
use App\Models\MembershipTransaction;
use App\Models\MembershipUser;
use Illuminate\Validation\Rule;
use Session;
use Auth;

class MembershipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "Membership Management";
        $auth = Auth::user();

        $memberships = Membership::all();
        return view('memberships.index', compact('memberships', 'title', 'auth'))->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = "Add Membership";
        $auth = Auth::user();

        return view('memberships.create', compact('title', 'auth'));
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
            'name' => 'required|string',
            'price' => 'required|numeric',
            'duration' => 'required|numeric',
        ]);

        Membership::create($request->all());

        Session::flash('success', 'Membership added successfully!');
        return redirect()->route('memberships.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title = "Edit Membership";
        $auth = Auth::user();

        $membership = Membership::findOrFail($id);
        return view('memberships.edit', compact('membership', 'title', 'auth'));
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
        $membership = Membership::findOrFail($id);

        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'duration' => 'required|numeric',
        ]);

        $membership->update($request->all());

        Session::flash('success', 'Membership updated successfully!');
        return redirect()->route('memberships.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $membership = Membership::findOrFail($id);
        $membership->delete();

        Session::flash('success', 'Membership deleted successfully!');
        return redirect()->route('memberships.index');
    }

    public function my()
    {
        $title = 'Histori Membership';
        $auth = Auth::user();
        $memberships = MembershipTransaction::with('membership')->where('user_id', $auth->id)->latest()->get();

        foreach ($memberships as $membership) {
            if ($membership->status == 'Selesai') {
                $membership->end_date = $membership->updated_at->format('d F Y');
            }
        }

        $member = MembershipUser::where('user_id', $auth->id)->first();

        return view('memberships.my', compact('memberships', 'auth', 'title', 'member'))->with('i');
    }

    public function transaction()
    {
        $title = 'Pembelian Membership';
        $auth = Auth::user();
        $memberships = Membership::all();

        return view('memberships.transaction', compact('memberships', 'auth', 'title'));
    }
    
    public function purchase(Request $request)
    {
        $request->validate([
            'membership_id' => 'required|exists:memberships,id',
            'image' => 'required|image|max:2048',
        ]);

        $user = Auth::user();

        $membership = new MembershipTransaction();
        $membership->user_id = $user->id;
        $membership->membership_id = $request->membership_id;
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalExtension();
            $image->move(public_path('image/memberships'), $imageName);
            $membership->image = $imageName;
        }

        $membership->status = "Proses";
        
        $membership->save();

        Session::flash('success', 'Pembelian membership berhasil, tunggu konfirmasi!');

        return redirect()->route('memberships.my');
    }

    public function list()
    {
        $title = 'Transaksi Membership';
        $auth = Auth::user();
        $memberships = MembershipTransaction::with('membership', 'user')->latest()->get();

        foreach ($memberships as $membership) {
            if ($membership->status == 'Selesai') {
                $membership->end_date = $membership->updated_at->format('d F Y');
            }
        }

        return view('memberships.list', compact('memberships', 'auth', 'title'));
    }
    
    public function activation($id)
    {
        $title = 'Aktivasi Membership';
        $auth = Auth::user();
        $memberships = MembershipTransaction::with('membership', 'user')->findOrFail($id);

        return view('memberships.activation', compact('memberships', 'auth', 'title'));
    }

    public function activate(Request $request, $id)
    {
        $membership = MembershipTransaction::findOrFail($id);

        $request->validate([
            'status' => 'required'
        ]);

        if($request->status == 'Selesai'){
            $membership_user = MembershipUser::where('user_id', $membership->user_id)->first();

            if($membership_user){
                if($membership_user->end_date > now()){
                    $start = $membership_user->end_date;
                } else {
                    $start = now();
                }

                $end = $start->copy()->addMonths($membership->membership->duration);
                $membership_user->update([
                    'start_date' => $start,
                    'end_date' => $end,
                ]);
            } else {
                $start = now();

                $end = $start->copy()->addMonths($membership->membership->duration);
                MembershipUser::create([
                    'user_id' => $membership->user_id,
                    'start_date' => $start,
                    'end_date' => $end,
                ]);
            }
        }

        $membership->update([
            'status' => $request->status,
        ]);

        Session::flash('success', 'Membership updated successfully!');
        return redirect()->route('memberships.list');
    }
}
