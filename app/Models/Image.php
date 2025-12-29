<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Image Model
 * 
 * This model represents the `images` table and is used to
 * manage uploaded files metadata such as file name, path,
 * size, status, and user tracking.
 */
class Image extends Model
{
    // Enables soft delete functionality (uses deleted_at column)
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * 
     * These fields can be safely filled using
     * Image::create() or $image->update()
     */
    protected $fillable = [
        'original_name', // Original file name uploaded by the user
        'file_name',     // Stored unique file name to avoid conflicts
        'file_path',     // Storage path of the file (uploads/filename)
        'file_size',     // File size stored in KB
        'status',        // File status (1 = active, 0 = inactive)
        'created_by',    // ID of the user who uploaded the file
        'updated_by'     // ID of the user who last updated the file
    ];
}
