<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * DropzoneController
 * 
 * Handles drag & drop file uploads using Dropzone.js,
 * stores files using Laravel storage, and manages
 * file records with soft delete support.
 */
class DropzoneController extends Controller
{
    /**
     * Display the upload page with existing uploaded files.
     */
    public function index()
    {
        // Fetch only active (status = 1) images from database
        $images = Image::where('status', 1)->get();

        // Load the dropzone view and pass images data
        return view('dropzone', compact('images'));
    }

    /**
     * Store uploaded file via AJAX request from Dropzone.
     */
    public function store(Request $request)
    {
        // Check if file is present in the request
        if ($request->hasFile('file')) {

            // Get uploaded file instance
            $file = $request->file('file');

            // Generate unique file name to prevent conflicts
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Store file in storage/app/public/uploads directory
            $path = $file->storeAs('uploads', $fileName, 'public');

            // Save file metadata into database
            $image = Image::create([
                'original_name' => $file->getClientOriginalName(), // User uploaded file name
                'file_name'     => $fileName,                      // Stored unique file name
                'file_path'     => $path,                          // File storage path
                'file_size'     => round($file->getSize() / 1024) . ' KB', // File size in KB
                'status'        => 1,                               // Active status
                'created_by'    => null,                      // No authentication implemented yet
                'updated_by'    => null,                      // No authentication implemented yet

            ]);

            // Return success response with image ID
            return response()->json(['success' => true, 'id' => $image->id, 'url' => asset('storage/' . $path) ]);
        }

        // Return failure response if no file found
        return response()->json(['success' => false]);
    }

    /**
     * Soft delete uploaded file and remove it from storage.
     */
    public function destroy($id)
    {
        // Find file record by ID or fail
        $image = Image::findOrFail($id);

        // Delete the physical file from storage
        Storage::disk('public')->delete($image->file_path);

        // Soft delete the database record (sets deleted_at)
        $image->delete();

        // Return success response
        return response()->json(['success' => true]);
    }
}
