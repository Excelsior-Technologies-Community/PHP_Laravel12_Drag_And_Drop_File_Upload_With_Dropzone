<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dropzone File Upload</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Dropzone CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- Dropzone JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>

    <style>
        body { background: #f8f9fa; }
        h2 { text-align: center; margin-bottom: 30px; font-weight: 700; }

        .dropzone {
            border: 2px dashed #0d6efd;
            border-radius: 10px;
            background: #ffffff;
            padding: 30px;
        }

        .card {
            transition: transform 0.2s, box-shadow 0.2s;
            border-radius: 10px;
        }

<<<<<<< HEAD
       /* PDF preview container */
        .pdf-preview {
            display: flex;
            flex-direction: column;
            /* Stack icon and filename vertically */
            justify-content: center;
            align-items: center;
            height: 150px;
            background-color: #e9ecef;
            padding: 10px;
            text-align: center;
            border-radius: 0.5rem;
            overflow: hidden;
            /* Hide overflow */
        }

        /* PDF link styling */
        .pdf-preview a {
            text-decoration: none;
            color: #0d6efd;
            font-size: 0.9rem;
            display: block;
            word-wrap: break-word;
            /* Wrap long filenames */
            overflow: hidden;
            text-overflow: ellipsis;
            /* Show "..." for very long text */
            max-width: 100%;
        }


        /* Uniform size for images and PDF previews */
        .card-img-top,
        .pdf-preview {
            width: 100%;
            height: 150px;
=======
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }

        .file-badge {
            font-size: 0.75rem;
            padding: 2px 6px;
            border-radius: 5px;
            background: #6c757d;
            color: white;
            margin-right: 5px;
        }

        .card-img-top {
            max-height: 150px;
>>>>>>> development
            object-fit: cover;
        }

        .btn-sm { font-size: 0.75rem; }
    </style>
</head>

<body>

<div class="container py-5">
    <h2>Professional Dropzone File Upload</h2>

    <!-- Dropzone -->
    <form method="POST" action="{{ route('dropzone.store') }}" class="dropzone mb-5" id="my-dropzone">
        @csrf
        <div class="dz-message text-center">
            <h4>Drag & Drop files here or click to upload</h4>
            <p class="text-muted">Allowed: JPG, PNG, GIF, PDF, DOCX</p>
        </div>
    </form>

    <!-- Files -->
    <h4 class="mb-4">Uploaded Files</h4>
    <div class="row g-3">

        @foreach($images as $img)
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm">

                @if(in_array(strtolower(pathinfo($img->file_name, PATHINFO_EXTENSION)), ['jpg','jpeg','png','gif']))
                    <img src="{{ asset('storage/' . $img->file_path) }}" class="card-img-top">
                @else
                    <img src="https://via.placeholder.com/150?text={{ strtoupper(pathinfo($img->file_name, PATHINFO_EXTENSION)) }}" class="card-img-top">
                @endif

                <div class="card-body">
                    <h6 class="text-truncate">{{ $img->file_name }}</h6>

                    <div class="mb-2">
                        <span class="file-badge">Size: {{ round($img->file_size / 1024, 2) }} KB</span>
                        <span class="file-badge">Type: {{ pathinfo($img->file_name, PATHINFO_EXTENSION) }}</span>
                    </div>

                    <div class="d-flex">
                        <a href="{{ route('dropzone.download', $img->id) }}"
                           class="btn btn-sm btn-primary w-50 me-1">
                           Download
                        </a>

                        <!-- ✅ FIXED DELETE BUTTON -->
                        <button class="btn btn-sm btn-danger w-50 delete-file"
                                data-id="{{ $img->id }}">
                            Delete
                        </button>
                    </div>
                </div>

            </div>
        </div>
        @endforeach

    </div>
</div>

<script>
Dropzone.options.myDropzone = {
    paramName: "file",
    maxFilesize: 10,
    acceptedFiles: ".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx",
    success: function () {
        location.reload();
    }
};

// ✅ DELETE WITH POPUP + AJAX
$(document).on('click', '.delete-file', function () {

    let id = $(this).data('id');

    if (confirm('Are you sure you want to delete this file?')) {

        $.ajax({
            url: '/dropzone/delete/' + id,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function (res) {
                alert(res.success);
                location.reload();
            },
            error: function () {
                alert('Error deleting file');
            }
        });

    }
});
</script>

</body>
</html>