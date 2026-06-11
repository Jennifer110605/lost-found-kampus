<div class="item-card card h-100 shadow-sm">
    {{-- Badge tipe --}}
    <div class="card-type-badge {{ $item->type === 'lost' ? 'badge-lost' : 'badge-found' }}">
        <i class="bi {{ $item->type === 'lost' ? 'bi-question-circle' : 'bi-check-circle' }} me-1"></i>
        {{ $item->type_label }}
    </div>

    {{-- Foto barang --}}
    <div class="card-img-wrapper">
        @if($item->photo)
            <img src="{{ $item->photo_url }}"
                 class="card-img-top"
                 alt="{{ $item->name }}"
                 loading="lazy">
        @else
            <div class="card-img-placeholder">
                <i class="bi bi-image"></i>
            </div>
        @endif

        {{-- Status resolved --}}
        @if($item->status === 'resolved')
            <div class="card-resolved-overlay">
                <i class="bi bi-check-circle-fill me-1"></i>Sudah Ditemukan
            </div>
        @endif
    </div>

    <div class="card-body d-flex flex-column">
        <h5 class="card-title">{{ $item->name }}</h5>

        <p class="card-text text-muted small flex-grow-1">
            {{ Str::limit($item->description, 80) }}
        </p>

        <div class="card-meta mt-2">
            <span class="meta-item">
                <i class="bi bi-geo-alt text-primary"></i>
                {{ Str::limit($item->location, 30) }}
            </span>
            <span class="meta-item">
                <i class="bi bi-calendar3 text-primary"></i>
                {{ $item->date_occurred->translatedFormat('d M Y') }}
            </span>
            <span class="meta-item">
                <i class="bi bi-person text-primary"></i>
                {{ $item->user->name }}
            </span>
        </div>

        <a href="{{ route('items.show', $item) }}" class="btn btn-outline-primary btn-sm mt-3 stretched-link">
            Lihat Detail <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
</div>
