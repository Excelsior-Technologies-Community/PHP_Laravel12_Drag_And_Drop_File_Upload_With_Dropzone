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
