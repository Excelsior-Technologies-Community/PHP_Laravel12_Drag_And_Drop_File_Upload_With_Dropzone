<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class DropzoneController extends Controller
{
    public function index()
    {
        $images = Image::all(); // get all uploaded files
        return view('dropzone', compact('images'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // max 10MB
        ]);

        $file = $request->file('file');
        $path = $file->store('uploads', 'public');

        $image = new Image();
        $image->file_name = $file->getClientOriginalName();
        $image->original_name = $file->getClientOriginalName();
        $image->file_path = $path;
        $image->file_size = $file->getSize();
        $image->status = 1; // active
        $image->save();

        return response()->json(['success' => $file->getClientOriginalName()]);
    }

    public function destroy($id)
    {
        $image = Image::findOrFail($id);
        Storage::disk('public')->delete($image->file_path);
        $image->delete();

        return response()->json(['success' => 'File deleted successfully.']);
    }

    public function download($id)
    {
        $image = Image::findOrFail($id);
        $path = storage_path('app/public/' . $image->file_path);

        return response()->download($path, $image->original_name);
    }
}