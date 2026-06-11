@extends('layouts.app')

@section('title', $item->name . ' - Lost & Found Kampus')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('items.index') }}">Beranda</a></li>
                    <li class="breadcrumb-item active">{{ $item->name }}</li>
                </ol>
            </nav>

            <div class="detail-card card shadow">
                {{-- Badge tipe --}}
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span class="badge {{ $item->type === 'lost' ? 'bg-danger' : 'bg-success' }} fs-6 px-3 py-2">
                        <i class="bi {{ $item->type === 'lost' ? 'bi-question-circle' : 'bi-check-circle' }} me-2"></i>
                        {{ $item->type_label }}
                    </span>
                    <span class="badge {{ $item->status === 'open' ? 'bg-warning text-dark' : 'bg-secondary' }}">
                        {{ $item->status === 'open' ? 'Aktif' : 'Selesai' }}
                    </span>
                </div>

                <div class="card-body p-4">
                    {{-- Foto --}}
                    @if($item->photo)
                        <div class="detail-photo mb-4">
                            <img src="{{ $item->photo_url }}"
                                 alt="{{ $item->name }}"
                                 class="img-fluid rounded-3 w-100"
                                 style="max-height: 400px; object-fit: cover;">
                        </div>
                    @endif

                    {{-- Judul --}}
                    <h1 class="detail-title">{{ $item->name }}</h1>

                    {{-- Info meta --}}
                    <div class="detail-meta row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="meta-block">
                                <span class="meta-label"><i class="bi bi-geo-alt me-1"></i>Lokasi</span>
                                <span class="meta-value">{{ $item->location }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="meta-block">
                                <span class="meta-label"><i class="bi bi-calendar3 me-1"></i>Tanggal</span>
                                <span class="meta-value">{{ $item->date_occurred->translatedFormat('d F Y') }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="meta-block">
                                <span class="meta-label"><i class="bi bi-person me-1"></i>Diposting oleh</span>
                                <span class="meta-value">{{ $item->user->name }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="meta-block">
                                <span class="meta-label"><i class="bi bi-telephone me-1"></i>Kontak</span>
                                <span class="meta-value">
                                    {{ $item->user->phone ?? 'Tidak tersedia' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-4">
                        <h5 class="fw-semibold mb-2">Deskripsi</h5>
                        <p class="text-body">{{ $item->description }}</p>
                    </div>

                    {{-- Waktu posting --}}
                    <p class="text-muted small">
                        <i class="bi bi-clock me-1"></i>
                        Diposting {{ $item->created_at->diffForHumans() }}
                        @if($item->updated_at->gt($item->created_at))
                            &middot; Diedit {{ $item->updated_at->diffForHumans() }}
                        @endif
                    </p>

                    {{-- Action buttons (hanya pemilik) --}}
                    @auth
                        @if(Auth::id() === $item->user_id)
                            <hr>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('items.edit', $item) }}" class="btn btn-primary">
                                    <i class="bi bi-pencil me-1"></i>Edit Postingan
                                </a>
                                <form action="{{ route('items.destroy', $item) }}" method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus postingan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="bi bi-trash me-1"></i>Hapus
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="detail-comments card shadow mt-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Komentar</h5>
                    <small class="text-muted">{{ $item->comments->count() }} komentar</small>
                </div>
                <div class="card-body">
                    @if($item->comments->isEmpty())
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-chat-left-text fs-1"></i>
                            <p class="mt-3 mb-0">Belum ada komentar untuk postingan ini.</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($item->comments->where('parent_id', null) as $comment)
                                <div class="list-group-item px-0 py-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong>{{ $comment->user->name }}</strong>
                                            <span class="text-muted small">&middot; {{ $comment->created_at->diffForHumans() }}</span>
                                            <p class="mb-0 mt-2">{{ $comment->content }}</p>
                                        </div>
                                        @auth
                                            @if(Auth::id() === $comment->user_id)
                                                <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="ms-3">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>

                                    @if($comment->replies->isNotEmpty())
                                        <div class="mt-3 ps-4 border-start border-2 border-light">
                                            @foreach($comment->replies as $reply)
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <strong>{{ $reply->user->name }}</strong>
                                                            <span class="text-muted small">&middot; {{ $reply->created_at->diffForHumans() }}</span>
                                                            <p class="mb-0 mt-2">{{ $reply->content }}</p>
                                                        </div>
                                                        @auth
                                                            @if(Auth::id() === $reply->user_id)
                                                                <form action="{{ route('comments.destroy', $reply) }}" method="POST" class="ms-3">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        @endauth
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    @auth
                                        <form action="{{ route('items.comments.store', $item) }}" method="POST" class="mt-3 ps-4 border-start border-2 border-light">
                                            @csrf
                                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                            <div class="mb-3">
                                                <textarea name="content" rows="2"
                                                          class="form-control form-control-sm @error('content') is-invalid @enderror"
                                                          placeholder="Balas komentar ini..."></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-reply-fill me-1"></i>Balas
                                            </button>
                                        </form>
                                    @endauth
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <hr>

                    @auth
                        <form action="{{ route('items.comments.store', $item) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="content" class="form-label">Tulis komentar</label>
                                <textarea id="content" name="content" rows="3"
                                          class="form-control @error('content') is-invalid @enderror"
                                          placeholder="Tinggalkan komentar untuk penemu atau pemilik barang">{{ old('content') }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-accent">
                                <i class="bi bi-send me-1"></i>Kirim Komentar
                            </button>
                        </form>
                    @else
                        <div class="alert alert-secondary mb-0" role="alert">
                            <strong>Login dulu</strong> untuk meninggalkan komentar.
                            <a href="{{ route('login') }}">Masuk</a> atau
                            <a href="{{ route('register') }}">daftar</a> sekarang.
                        </div>
                    @endauth
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali ke Beranda
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
