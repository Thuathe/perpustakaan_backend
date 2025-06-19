<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Middleware hanya untuk admin
    public function __construct()
    {
        $this->middleware('auth:api');
    }

public function index()
{
    $user = auth()->user();

    if (!$user || $user->role !== 'admin') {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    return response()->json(User::where('id', '!=', $user->id)->get());
}


    public function destroy($id)
    {
        // Hanya admin yang boleh hapus
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        // Optional: Hindari hapus diri sendiri
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Tidak bisa hapus akun sendiri'], 400);
        }

        $user->delete();
        return response()->json(['message' => 'User berhasil dihapus']);
    }
}
