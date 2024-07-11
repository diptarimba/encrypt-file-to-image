<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $search = $request->search['value'];
            $user = User::whereHas('roles', function ($query) {
                $query->where('name', 'user_corporate');
            })
                ->where('corporate_id', auth()->user()->corporate_id)
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%');
                })
                ->select();
            return datatables()->of($user)
                ->addIndexColumn()
                ->addColumn('action', function ($query) {
                    return $this->getActionColumn($query, 'user', 'corporate');
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('page.corporate-dashboard.user.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = $this->createMetaPageData(null, 'User', 'user', 'corporate');
        return view('page.corporate-dashboard.user.create-edit', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required',
            'password' => 'required',
            'email' => 'required',
            'phone' => 'required'
        ]);

        $user = User::create(array_merge($request->all(), [
            'password' => bcrypt($request->password),
            'corporate_id' => auth()->user()->corporate_id,
            'picture' => asset('assets-dashboard/images/placeholder.png')
        ]));
        $user->assignRole('user_corporate');
        return redirect()->route('corporate.user.index')->with('success', 'User created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $data = $this->createMetaPageData($user->id, 'User', 'user', 'corporate');
        return view('page.corporate-dashboard.user.create-edit', compact('data', 'user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required',
            'password' => 'sometimes',
            'phone' => 'required'
        ]);

        $user->update(array_merge($request->all(), [
            'password' => $request->password ? bcrypt($request->password) : $user->password,
            'corporate_id' => auth()->user()->corporate_id,
            'picture' => $user->picture
        ]));

        return redirect()->route('corporate.user.index')->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            if($user->id == auth()->user()->id) {
                throw new \Exception('You cannot delete yourself');
            }
            if(($user->corporate_id != auth()->user()->corporate_id) || ($user->corporate_id == null) || ($user->corporate_admin == 1)) {
                throw new \Exception('You cannot delete this user');
            }
            $user->delete();
            return redirect()->route('corporate.user.index')->with('success', 'Admin Deleted Successfully');
        } catch (\Throwable $th) {
            return redirect()->route('corporate.user.index')->with('error', $th->getMessage());
        }
    }
}
