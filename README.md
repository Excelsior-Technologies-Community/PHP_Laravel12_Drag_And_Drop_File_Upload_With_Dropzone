# PHP_Laravel12_Drag_And_Drop_File_Upload_With_Dropzone

##  Project Overview

This project demonstrates how to implement **Drag and Drop File Upload functionality** in **Laravel 12** using **Dropzone.js**. It supports uploading **images and PDF files**, storing them using Laravel's **storage system**, saving file metadata in the database, and managing files with **AJAX (without page refresh)**.

The project follows **Laravel best practices**, includes **soft deletes**, `status`, `created_by`, and `updated_by` fields, and uses a **modern Bootstrap UI**.

---

##  Features

* Drag & Drop file upload using Dropzone.js
* Supports JPG, PNG, JPEG, PDF
* Files stored using `storage:link`
* Database entry for each upload
* Soft delete support
* AJAX-based upload & delete (no refresh)
* Live preview of uploaded files
* Clean & professional UI

---

##  Technologies Used

* Laravel 12
* PHP 8+
* Dropzone.js
* Bootstrap 5
* jQuery
* MySQL

---

##  Project Structure

```text
PHP_Laravel12_Drag_And_Drop_File_Upload_With_Dropzone
│
├── app
│   ├── Http/Controllers
│   │   └── DropzoneController.php
│   └── Models
│       └── Image.php
│
├── database
│   └── migrations
│       └── xxxx_xx_xx_create_images_table.php
│
├── public/storage/uploads
│
├── resources
│   └── views
│       └── dropzone.blade.php
│
├── routes
│   └── web.php
│
├── storage/app/public/uploads
├── .env
├── artisan
└── README.md
```

---

###  Installation & Setup

## Step 1: Create Laravel 12 Project

```bash
composer create-project laravel/laravel PHP_Laravel12_Drag_And_Drop_File_Upload_With_Dropzone "12.*"
cd PHP_Laravel12_Drag_And_Drop_File_Upload_With_Dropzone
```

---


##  Step 2: Configure Environment

Update `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_dropzone
DB_USERNAME=root
DB_PASSWORD=
```

Then Using this command create database:

```
php artisan migrate
```

---

## Step 3:  Database Migration

### Create Migration

```bash
php artisan make:migration create_images_table
```

File: database/migrations/xxxx_xx_xx_create_images_table.php

### Migration File

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {

            // Primary key (Auto Increment ID)
            $table->id();

            // Original file name uploaded by the user
            // Example: my_photo.jpg
            $table->string('original_name');

            // Stored file name used internally to avoid conflicts
            // Example: 1712345678_my_photo.jpg
            $table->string('file_name');

            // File storage path inside storage/app/public
            // Example: uploads/1712345678_my_photo.jpg
            $table->string('file_path');

            // File size (stored in KB for display purpose)
            $table->string('file_size')->nullable();

            // Status column (1 = active, 0 = inactive)
            $table->boolean('status')->default(1);

            // User ID who created/uploaded the file
            $table->unsignedBigInteger('created_by')->nullable();

            // User ID who last updated the file record
            $table->unsignedBigInteger('updated_by')->nullable();

            // created_at and updated_at timestamps
            $table->timestamps();

            // Soft delete column (deleted_at)
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the images table if rollback is executed
        Schema::dropIfExists('images');
    }
};
```

Run migration:

```bash
php artisan migrate
```

---

## Step 4: Model (Image.php)

```
php artisan make:model Image
```

File: app/Models/Image.php

```php
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
```

---


## Step 5: Storage Configuration (IMPORTANT)

We use Laravel Storage instead of public/uploads.

**Create Storage Link:**

```bash
php artisan storage:link
```

**Files are stored in:**

storage/app/public/uploads


**Accessed via:**

public/storage/uploads


---

## Step 6: Controller 

**DropzoneController.php**

Create Controller

```bash
php artisan make:controller DropzoneController
```

File: app/Http/Controllers/DropzoneController.php

```php
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
```

---

## Step 7: Routes 

File: routes/web.php

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DropzoneController;

/*
|--------------------------------------------------------------------------
| Dropzone File Upload Routes
|--------------------------------------------------------------------------
|
| These routes handle the drag & drop file upload feature
| using Dropzone.js in Laravel 12.
|
*/

// Display the Dropzone upload page
Route::get('/dropzone', [DropzoneController::class, 'index']);

// Handle file upload request from Dropzone (AJAX)
Route::post('/dropzone/store', [DropzoneController::class, 'store'])
     ->name('dropzone.store');

// Handle file delete request (soft delete + storage delete)
Route::delete('/dropzone/{id}', [DropzoneController::class, 'destroy'])
     ->name('dropzone.destroy');

/*
|--------------------------------------------------------------------------
| Default Welcome Route
|--------------------------------------------------------------------------
|
| Default Laravel welcome page route.
|
*/
Route::get('/', function () {
    return view('welcome');
});

```

---


## Step 8: dropzone.blade.php

File: resources/views/dropzone.blade.php

Includes:

* Dropzone upload area
* Uploaded file previews
* AJAX delete
* Image & PDF handling


```
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel 12 Drag & Drop File Upload</title>

    <!-- Bootstrap CSS for layout and styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Dropzone CSS for drag & drop functionality -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css">

    <style>
        /* Page background color */
        body {
            background-color: #f5f6fa;
        }

        /* Card styling for uploaded files */
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        /* Dropzone styling */
        .dropzone {
            border: 2px dashed #0d6efd;
            border-radius: 10px;
            background: #f8f9fa;
            padding: 40px 20px;
        }

        /* Dropzone default message */
        .dz-message {
            font-size: 1.2rem;
            color: #0d6efd;
            display: block !important; /* Always show the message */
        }

        /* PDF preview styling */
        .pdf-preview {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 150px;
            background-color: #e9ecef;
            font-size: 1rem;
            text-align: center;
        }

        /* PDF link styling */
        .pdf-preview a {
            text-decoration: none;
            color: #0d6efd;
        }

        /* Uniform size for images and PDF previews */
        .card-img-top,
        .pdf-preview {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
    </style>
</head>

<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card p-4">
                <!-- Page Heading -->
                <h3 class="text-center mb-4">Laravel 12 Drag & Drop File Upload</h3>

                <!-- Dropzone Upload Form -->
                <form action="{{ route('dropzone.store') }}"
                      method="POST"
                      enctype="multipart/form-data"
                      class="dropzone"
                      id="image-upload">
                    @csrf
                    <!-- Dropzone default message -->
                    <div class="dz-message" id="dz-message">
                        <h5>Drag & Drop your files here</h5>
                        <p>or click to select files</p>
                        <span class="text-muted">
                            Accepted: jpeg, jpg, png, pdf | Max: 5MB
                        </span>
                    </div>
                </form>

                <!-- Uploaded Files Section -->
                <h5 class="mt-4">Uploaded Files:</h5>
                <div class="row mt-2" id="existing-files">
                    <!-- Loop through all uploaded images -->
                    @foreach($images as $image)
                        @php
                            // Get file extension to decide display type
                            $extension = strtolower(pathinfo($image->file_name, PATHINFO_EXTENSION));
                        @endphp

                        <div class="col-3 mb-3" id="image-{{ $image->id }}">
                            <div class="card shadow-sm">

                                <!-- Display image preview if image file -->
                                @if(in_array($extension, ['jpg','jpeg','png']))
                                    <img src="{{ asset('storage/'.$image->file_path) }}"
                                         class="card-img-top"
                                         alt="{{ $image->original_name }}">
                                <!-- Display PDF preview if PDF file -->
                                @elseif($extension === 'pdf')
                                    <div class="pdf-preview">
                                        <a href="{{ asset('storage/'.$image->file_path) }}" target="_blank">
                                            📄 {{ $image->original_name }}
                                        </a>
                                    </div>
                                <!-- Other file types fallback -->
                                @else
                                    <div class="pdf-preview">
                                        <small>{{ $image->original_name }}</small>
                                    </div>
                                @endif

                                <!-- Delete button for each file -->
                                <div class="card-body p-2 text-center">
                                    <button class="btn btn-sm btn-danger delete-file"
                                            data-id="{{ $image->id }}">
                                        Delete
                                    </button>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Dropzone JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
<!-- jQuery for AJAX -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<script>
    // Dropzone configuration
    Dropzone.options.imageUpload = {
        paramName: "file", // The name used for the uploaded file
        maxFilesize: 5,    // Max file size in MB
        acceptedFiles: ".jpeg,.jpg,.png,.pdf", // Accepted file types
        addRemoveLinks: true, // Show remove links in Dropzone

        // Add CSRF token header for Laravel
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },

        // On successful upload
        success: function (file, response) {
            if (response.success) {
                // Remove file preview from Dropzone after upload
                if (file.previewElement) {
                    file.previewElement.remove();
                }

                // Determine file extension
                let extension = file.name.split('.').pop().toLowerCase();
                let cardHtml = '';

                // If uploaded file is image
                if (['jpg','jpeg','png'].includes(extension)) {
                    cardHtml = `
                    <div class="col-3 mb-3" id="image-${response.id}">
                        <div class="card shadow-sm">
                            <img src="${response.url}" class="card-img-top" alt="${file.name}">
                            <div class="card-body p-2 text-center">
                                <button class="btn btn-sm btn-danger delete-file" data-id="${response.id}">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>`;
                } else {
                    // For PDFs and other file types
                    cardHtml = `
                    <div class="col-3 mb-3" id="image-${response.id}">
                        <div class="card shadow-sm">
                            <div class="pdf-preview">
                                <a href="${response.url}" target="_blank">📄 ${file.name}</a>
                            </div>
                            <div class="card-body p-2 text-center">
                                <button class="btn btn-sm btn-danger delete-file" data-id="${response.id}">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>`;
                }

                // Append the uploaded file to the uploaded files section
                $('#existing-files').append(cardHtml);
            }
        },

        // Remove file preview if removed from Dropzone
        removedfile: function(file) {
            if (file.previewElement) file.previewElement.remove();
        }
    };

    // Handle file deletion using AJAX
    $(document).on('click', '.delete-file', function () {
        let id = $(this).data('id');

        if (confirm('Are you sure you want to delete this file?')) {
            $.ajax({
                url: '/dropzone/' + id,
                type: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    if (response.success) {
                        // Remove the deleted file card from UI
                        $('#image-' + id).remove();
                    }
                }
            });
        }
    });
</script>

</body>
</html>
```

---

## Step 9: Run Project

```bash
php artisan serve
```

Open:

```
http://127.0.0.1:8000/dropzone
```

---

## Output

<img width="1919" height="1030" alt="Screenshot 2025-12-29 111319" src="https://github.com/user-attachments/assets/3a87d5d1-cbf8-4f5f-9872-501f3afa6b37" />


---


Your PHP_Laravel12_Drag_And_Drop_File_Upload_With_Dropzone is Now Ready!
