<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dropzone File Manager</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        body { background: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .dropzone { border: 2px dashed #0d6efd; border-radius: 15px; background: #fff; transition: 0.3s; min-height: 180px; }
        .dz-message h4 { font-weight: 700; color: #0d6efd; }
        .file-card { border: none; border-radius: 12px; transition: 0.3s; background: #fff; overflow: hidden; }
        .file-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .icon-box { height: 140px; display: flex; align-items: center; justify-content: center; font-size: 50px; background: #f8f9fa; }
        .preview-img { height: 140px; width: 100%; object-fit: cover; }
        .file-info { padding: 15px; }
        .file-name { font-size: 0.9rem; font-weight: 600; display: block; }
    </style>
</head>
<body>

    <div class="container py-5">
        <h2 class="text-center mb-5 fw-bold text-primary">Cloud Storage</h2>

        <form action="{{ route('dropzone.store') }}" method="POST" enctype="multipart/form-data" class="dropzone mb-5 shadow-sm" id="my-dropzone">
            @csrf
            <div class="dz-message d-flex flex-column align-items-center">
                <i class="fa-solid fa-cloud-arrow-up fa-3x mb-3 text-primary"></i>
                <h4>Drag & Drop files to upload</h4>
            </div>
        </form>

        <h4 class="mb-4">Recent Uploads (<span id="total-files">{{ count($images) }}</span>)</h4>

        <div class="row g-4" id="file-grid">
            @foreach($images as $img)
                @include('partials.file-card', ['img' => $img])
            @endforeach
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        const Toast = Swal.mixin({
            toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true
        });

        Dropzone.options.myDropzone = {
            paramName: "file",
            maxFilesize: 10,
            init: function () {
                this.on("success", function (file, response) {
                    if (response.html) {
                     
                        $('#file-grid').prepend(response.html);
                        
                        let count = parseInt($('#total-files').text());
                        $('#total-files').text(count + 1);
                        
                        Toast.fire({ icon: 'success', title: 'File uploaded!' });
                    }
                    this.removeFile(file);
                });
            }
        };

        $(document).on('click', '.delete-file', function () {
            let id = $(this).data('id');
            let element = $(this).closest('.file-item');

            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/dropzone/delete/' + id,
                        type: 'DELETE',
                        success: function (res) {
                            element.fadeOut(400, function () { 
                                $(this).remove(); 
                                let count = parseInt($('#total-files').text());
                                $('#total-files').text(count - 1);
                            });
                            Toast.fire({ icon: 'success', title: res.success });
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>