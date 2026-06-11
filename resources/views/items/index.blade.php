@extends('layouts.app')

@section('title', 'Beranda - Lost & Found Kampus')

@section('content')

{{-- Hero Section --}}
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <h1 class="hero-title">
                    Ngoni pe barang ilang<br>
                    di kampus? <span class="text-accent">Cari jo SANDIRI disini!</span>
                </h1>
                <p class="hero-subtitle">
                    Lost &amp; Found for mahasiswa kampus. SUPAYA APA SO? Supaya ngoni pe barang yang ilang
                    boleh ngoni mo dapa disini, ato yang dapa barang boleh ngoni mo posting supaya yang punya boleh mo dapa tau.
                </p>
            </div>
            <div class="col-lg-5 d-none d-lg-flex justify-content-center">
                <div class="hero-illustration">
                    <i class="bi bi-search-heart"></i>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Stats bar --}}
<section class="stats-bar py-3">
    <div class="container">
        <div class="row text-center g-3">
            <div class="col-4">
                <span class="stat-number">{{ \App\Models\Item::count() }}</span>
                <span class="stat-label">Total Postingan</span>
            </div>
            <div class="col-4">
                <span class="stat-number">{{ \App\Models\Item::where('type', 'lost')->count() }}</span>
                <span class="stat-label">Barang Hilang</span>
            </div>
            <div class="col-4">
                <span class="stat-number">{{ \App\Models\Item::where('type', 'found')->count() }}</span>
                <span class="stat-label">Barang Ditemukan</span>
            </div>
        </div>
    </div>
</section>

{{-- Filter & Search --}}
<section class="py-4">
    <div class="container">
        <form method="GET" action="{{ route('items.index') }}" class="search-bar-form">
            <div class="row g-3 align-items-end">
                {{-- Search --}}
                <div class="col-lg-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control"
                               placeholder="Cari nama barang, lokasi..."
                               value="{{ request('search') }}">
                    </div>
                </div>

                {{-- Filter tipe --}}
                <div class="col-lg-3 col-md-4">
                    <select name="type" class="form-select">
                        <option value="">Semua Tipe</option>
                        <option value="lost"  {{ request('type') === 'lost'  ? 'selected' : '' }}>Barang Hilang</option>
                        <option value="found" {{ request('type') === 'found' ? 'selected' : '' }}>Barang Ditemukan</option>
                    </select>
                </div>

                {{-- Filter status --}}
                <div class="col-lg-2 col-md-4">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="open"     {{ request('status') === 'open'     ? 'selected' : '' }}>Aktif</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>

                {{-- Tombol --}}
                <div class="col-lg-2 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    @if(request()->hasAny(['search', 'type', 'status']))
                        <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</section>

{{-- Daftar Item --}}
<section class="pb-5">
    <div class="container">
        <div class="mb-4">
            <h2 class="section-title mb-0">
                @if(request('type') === 'lost')
                    <i class="bi bi-question-circle text-danger me-2"></i>Barang Hilang
                @elseif(request('type') === 'found')
                    <i class="bi bi-check-circle text-success me-2"></i>Barang Ditemukan
                @else
                    <i class="bi bi-grid me-2"></i>Semua Postingan
                @endif
                <span class="badge bg-secondary ms-2">{{ $items->total() }}</span>
            </h2>
        </div>

        @if($items->isEmpty())
            <div class="empty-state text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <h4 class="mt-3 text-muted">Belum ada postingan</h4>
                <p class="text-muted">
                    @if(request()->hasAny(['search', 'type', 'status']))
                        Tidak ada hasil yang cocok dengan filter kamu.
                        <a href="{{ route('items.index') }}">Reset filter</a>
                    @else
                        Jadilah yang pertama membuat postingan!
                    @endif
                </p>
            </div>
        @else
            <div class="row g-4">
                @foreach($items as $item)
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        @include('partials.item-card', ['item' => $item])
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-5 d-flex justify-content-center">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</section>

@endsection
