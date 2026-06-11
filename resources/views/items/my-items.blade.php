@extends('layouts.app')

@section('title', 'Postingan Saya - Lost & Found Kampus')

@section('content')
<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="section-title mb-1">
                <i class="bi bi-collection me-2"></i>Postingan Saya
            </h2>
            <p class="text-muted mb-0">Kelola semua postingan yang kamu buat.</p>
        </div>
        <a href="{{ route('items.create') }}" class="btn btn-accent">
            <i class="bi bi-plus me-1"></i>Buat Baru
        </a>
    </div>

    @if($items->isEmpty())
        <div class="empty-state text-center py-5">
            <i class="bi bi-collection display-1 text-muted"></i>
            <h4 class="mt-3 text-muted">Kamu belum punya postingan</h4>
            <p class="text-muted">Mulai dengan membuat postingan barang hilang atau ditemukan.</p>
            <a href="{{ route('items.create') }}" class="btn btn-accent mt-2">
                <i class="bi bi-plus-circle me-1"></i>Buat Postingan Pertama
            </a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Barang</th>
                        <th>Tipe</th>
                        <th>Lokasi</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                @if($item->photo)
                                    <img src="{{ $item->photo_url }}"
                                         alt="{{ $item->name }}"
                                         class="rounded" width="48" height="48"
                                         style="object-fit:cover;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                         style="width:48px;height:48px;">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-semibold">{{ $item->name }}</div>
                                    <small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $item->type === 'lost' ? 'bg-danger' : 'bg-success' }}">
                                {{ $item->type_label }}
                            </span>
                        </td>
                        <td>
                            <small><i class="bi bi-geo-alt me-1 text-muted"></i>{{ $item->location }}</small>
                        </td>
                        <td>
                            <small>{{ $item->date_occurred->format('d/m/Y') }}</small>
                        </td>
                        <td>
                            <span class="badge {{ $item->status === 'open' ? 'bg-warning text-dark' : 'bg-secondary' }}">
                                {{ $item->status === 'open' ? 'Aktif' : 'Selesai' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('items.show', $item) }}"
                                   class="btn btn-sm btn-outline-primary" title="Lihat">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('items.edit', $item) }}"
                                   class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('items.destroy', $item) }}" method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus postingan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $items->links() }}
        </div>
    @endif

</div>
@endsection
