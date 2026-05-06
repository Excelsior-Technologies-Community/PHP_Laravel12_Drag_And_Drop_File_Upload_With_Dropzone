<div class="col-xl-3 col-lg-4 col-md-6 file-item" id="file-container-{{ $img->id }}">
    <div class="card file-card shadow-sm">
        @php
            $ext = strtolower(pathinfo($img->file_name, PATHINFO_EXTENSION));
            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif']);
            $icon = match($ext) {
                'pdf' => 'fa-file-pdf text-danger',
                'doc', 'docx' => 'fa-file-word text-primary',
                'xls', 'xlsx' => 'fa-file-excel text-success',
                'zip', 'rar' => 'fa-file-zipper text-warning',
                'txt' => 'fa-file-lines text-secondary',
                default => 'fa-file text-muted',
            };
        @endphp

        <div class="icon-box">
            @if($isImage)
                <img src="{{ asset('storage/' . $img->file_path) }}" class="preview-img">
            @else
                <i class="fa-solid {{ $icon }}"></i>
            @endif
        </div>

        <div class="file-info">
            <span class="file-name text-truncate" title="{{ $img->file_name }}">{{ $img->file_name }}</span>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="file-meta">{{ round($img->file_size / 1024, 2) }} KB</span>
                <span class="badge bg-light text-dark border text-uppercase">{{ $ext }}</span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('dropzone.download', $img->id) }}" class="btn btn-sm btn-outline-primary w-100">
                    <i class="fa fa-download"></i>
                </a>
                <button class="btn btn-sm btn-outline-danger w-100 delete-file" data-id="{{ $img->id }}">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
</div>