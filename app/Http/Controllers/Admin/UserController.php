<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

public function index()
{
    $users = User::where('role', 'kasir')->get();
    return view('admin.users.index', compact('users'));
}

public function approve($id)
{
    $user = User::findOrFail($id);

    $user->status = 'active';
    $user->save();

    return back()->with('success', 'Akun berhasil disetujui');
}
public function reject($id)
{
    $user = User::findOrFail($id);

    $user->delete();

    return back()->with('success', 'Akun berhasil dihapus');
}
}
