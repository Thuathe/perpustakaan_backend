<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class SongController extends Controller
{
    public function index()
    {
        return response()->json(Song::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'artist' => 'required',
            'file' => 'required|mimes:mp3,wav|max:10240',
        ]);

try {
    $upload = Cloudinary::uploadFile(
        $request->file('file')->getRealPath(),
        ['resource_type' => 'video']
    );

    $song = Song::create([
        'title' => $request->title,
        'artist' => $request->artist,
        'file_url' => $upload->getSecurePath()
    ]);

    return response()->json($song);

} catch (\Exception $e) {
    return response()->json(['message' => 'Upload gagal: ' . $e->getMessage()], 500);
}

    }

    public function destroy(Song $song)
    {
        $song->delete();
        return response()->json(['message' => 'Song deleted']);
    }
}
