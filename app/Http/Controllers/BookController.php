<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class BookController extends Controller
{
    public function index()
    {
        return response()->json(Book::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'author' => 'required',
            'cover' => 'nullable|image|max:2048',
        ]);

        $coverUrl = null;

        try {
            if ($request->hasFile('cover') && $request->file('cover')->isValid()) {
                $uploadedFile = Cloudinary::upload($request->file('cover')->getRealPath(), [
                    'folder' => 'perpus_buku'
                ]);

                // Validasi kembalian path
                $coverUrl = $uploadedFile->getSecurePath();
                if (!$coverUrl) {
                    return response()->json([
                        'message' => 'Gagal mendapatkan URL cover dari Cloudinary.'
                    ], 500);
                }
            }

            $book = Book::create([
                'title' => $request->title,
                'author' => $request->author,
                'cover' => $coverUrl,
            ]);

            return response()->json($book, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menyimpan buku: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function show(Book $book)
    {
        return response()->json($book);
    }

public function update(Request $request, Book $book)
{
    $request->validate([
        'title' => 'required',
        'author' => 'required',
        'cover' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    if ($request->hasFile('cover')) {
        // Hapus gambar lama dari Cloudinary (opsional, kalau kamu simpan public_id-nya)
        // contoh: Cloudinary::destroy($book->public_id);

        // Upload gambar baru
        $uploadedFile = Cloudinary::upload($request->file('cover')->getRealPath(), [
            'folder' => 'perpus_buku'
        ]);
        $book->cover = $uploadedFile->getSecurePath();
    }

    // Update data buku
    $book->update([
        'title' => $request->title,
        'author' => $request->author,
        'cover' => $book->cover, // tetap kirim cover baru (kalau diubah)
    ]);

    return response()->json($book);
}


    public function destroy(Book $book)
    {
        if ($book->cover) {
            Storage::disk('public')->delete($book->cover);
        }
        $book->delete();

        return response()->json(['message' => 'Book deleted']);
    }
}
