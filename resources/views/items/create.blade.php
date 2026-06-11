@extends('layouts.app')

@section('title', 'Buat Postingan - Lost & Found Kampus')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('items.index') }}">Beranda</a></li>
                    <li class="breadcrumb-item active">Buat Postingan</li>
                </ol>
            </nav>

            <div class="card shadow form-card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="bi bi-plus-circle me-2 text-accent"></i>Buat Postingan Baru</h4>
                    <p class="text-muted small mb-0 mt-1">Isi detail barang hilang atau barang yang kamu temukan.</p>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Tipe --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Tipe Postingan <span class="text-danger">*</span></label>
                            <div class="type-selector d-flex gap-3">
                                <label class="type-option flex-fill {{ old('type') === 'lost' || !old('type') ? '' : '' }}">
                                    <input type="radio" name="type" value="lost"
                                           {{ old('type', 'lost') === 'lost' ? 'checked' : '' }}>
                                    <span class="type-box type-lost">
                                        <i class="bi bi-question-circle-fill"></i>
                                        <strong>Barang Hilang</strong>
                                        <small>Saya kehilangan barang</small>
                                    </span>
                                </label>
                                <label class="type-option flex-fill">
                                    <input type="radio" name="type" value="found"
                                           {{ old('type') === 'found' ? 'checked' : '' }}>
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
                            <label for="name" class="form-label fw-semibold">
                                Nama Barang <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="name" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="Contoh: Kartu Mahasiswa, Dompet Hitam, Botol Minum..."
                                   value="{{ old('name') }}" autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Deskripsi --}}
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">
                                Deskripsi <span class="text-danger">*</span>
                            </label>
                            <textarea id="description" name="description" rows="4"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Jelaskan ciri-ciri barang secara detail (warna, ukuran, merek, kondisi, dll.)">{{ old('description') }}</textarea>
                            <div class="form-text">Semakin detail deskripsi, semakin mudah dikenali.</div>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Lokasi & Tanggal --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-7">
                                <label for="location" class="form-label fw-semibold">
                                    Lokasi <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="location" name="location"
                                       class="form-control @error('location') is-invalid @enderror"
                                       placeholder="Contoh: Gedung A Lantai 2, Kantin Utama..."
                                       value="{{ old('location') }}">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-5">
                                <label for="date_occurred" class="form-label fw-semibold">
                                    Tanggal <span class="text-danger">*</span>
                                </label>
                                <input type="date" id="date_occurred" name="date_occurred"
                                       class="form-control @error('date_occurred') is-invalid @enderror"
                                       value="{{ old('date_occurred', date('Y-m-d')) }}"
                                       max="{{ date('Y-m-d') }}">
                                @error('date_occurred')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Upload foto --}}
                        <div class="mb-4">
                            <label for="photo" class="form-label fw-semibold">Foto Barang</label>
                            <div class="photo-upload-area" id="photoUploadArea">
                                <input type="file" id="photo" name="photo"
                                       class="photo-input @error('photo') is-invalid @enderror"
                                       accept="image/jpg,image/jpeg,image/png,image/webp">
                                <div class="photo-upload-placeholder" id="photoPlaceholder">
                                    <i class="bi bi-cloud-upload"></i>
                                    <p>Klik atau drag foto ke sini</p>
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
                            <button type="submit" class="btn btn-accent btn-lg flex-fill">
                                <i class="bi bi-send me-2"></i>Kirim Postingan
                            </button>
                            <a href="{{ route('items.index') }}" class="btn btn-outline-secondary btn-lg">
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
// Preview foto sebelum upload
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
