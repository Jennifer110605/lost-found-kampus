@extends('layouts.app')

@section('title', 'Edit Postingan - Lost & Found Kampus')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('items.index') }}">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('items.show', $item) }}">{{ $item->name }}</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>

            <div class="card shadow form-card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="bi bi-pencil me-2 text-accent"></i>Edit Postingan</h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('items.update', $item) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Tipe --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Tipe Postingan <span class="text-danger">*</span></label>
                            <div class="type-selector d-flex gap-3">
                                <label class="type-option flex-fill">
                                    <input type="radio" name="type" value="lost"
                                           {{ old('type', $item->type) === 'lost' ? 'checked' : '' }}>
                                    <span class="type-box type-lost">
                                        <i class="bi bi-question-circle-fill"></i>
                                        <strong>Barang Hilang</strong>
                                        <small>Saya kehilangan barang</small>
                                    </span>
                                </label>
                                <label class="type-option flex-fill">
                                    <input type="radio" name="type" value="found"
                                           {{ old('type', $item->type) === 'found' ? 'checked' : '' }}>
                                    <span class="type-box type-found">
                                        <i class="bi bi-check-circle-fill"></i>
                                        <strong>Barang Ditemukan</strong>
                                        <small>Saya menemukan barang</small>
                                    </span>
                                </label>
                            </div>
                            @error('type')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nama barang --}}
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $item->name) }}">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Deskripsi --}}
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Deskripsi <span class="text-danger">*</span></label>
                            <textarea id="description" name="description" rows="4"
                                      class="form-control @error('description') is-invalid @enderror">{{ old('description', $item->description) }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Lokasi & Tanggal --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-7">
                                <label for="location" class="form-label fw-semibold">Lokasi <span class="text-danger">*</span></label>
                                <input type="text" id="location" name="location"
                                       class="form-control @error('location') is-invalid @enderror"
                                       value="{{ old('location', $item->location) }}">
                                @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-5">
                                <label for="date_occurred" class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" id="date_occurred" name="date_occurred"
                                       class="form-control @error('date_occurred') is-invalid @enderror"
                                       value="{{ old('date_occurred', $item->date_occurred->format('Y-m-d')) }}"
                                       max="{{ date('Y-m-d') }}">
                                @error('date_occurred') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="mb-3">
                            <label for="status" class="form-label fw-semibold">Status Postingan <span class="text-danger">*</span></label>
                            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="open"     {{ old('status', $item->status) === 'open'     ? 'selected' : '' }}>Aktif (Masih dicari)</option>
                                <option value="resolved" {{ old('status', $item->status) === 'resolved' ? 'selected' : '' }}>Selesai (Sudah ditemukan/dikembalikan)</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Foto --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Foto Barang</label>

                            @if($item->photo)
                                <div class="current-photo mb-2">
                                    <img src="{{ $item->photo_url }}"
                                         alt="Foto saat ini" class="img-thumbnail" style="max-height:160px;">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="remove_photo" id="removePhoto" value="1">
                                        <label class="form-check-label text-danger small" for="removePhoto">
                                            Hapus foto ini
                                        </label>
                                    </div>
                                </div>
                            @endif

                            <div class="photo-upload-area" id="photoUploadArea">
                                <input type="file" id="photo" name="photo"
                                       class="photo-input @error('photo') is-invalid @enderror"
                                       accept="image/jpg,image/jpeg,image/png,image/webp">
                                <div class="photo-upload-placeholder" id="photoPlaceholder">
                                    <i class="bi bi-cloud-upload"></i>
                                    <p>{{ $item->photo ? 'Ganti foto (opsional)' : 'Upload foto (opsional)' }}</p>
                                    <small>JPG, PNG, WEBP — Maks. 2MB</small>
                                </div>
                                <img id="photoPreview" src="" alt="Preview" class="photo-preview d-none">
                            </div>
                            @error('photo')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tombol --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg flex-fill">
                                <i class="bi bi-save me-2"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('items.show', $item) }}" class="btn btn-outline-secondary btn-lg">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const photoInput   = document.getElementById('photo');
const photoPreview = document.getElementById('photoPreview');
const photoPlaceholder = document.getElementById('photoPlaceholder');

photoInput.addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => {
            photoPreview.src = e.target.result;
            photoPreview.classList.remove('d-none');
            photoPlaceholder.classList.add('d-none');
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
