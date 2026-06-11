<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

defineProps({ items: Object });

function truncate(text, n) {
    if (!text) return '';
    return text.length > n ? text.slice(0, n) + '...' : text;
}

function formatDate(d) {
    if (!d) return '';
    return new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function deleteItem(item) {
    if (!confirm('Yakin ingin menghapus postingan ini?')) return;
    router.delete(`/items/${item.id}`, { preserveScroll: true });
}
</script>

<template>
    <Head title="Postingan Saya" />

    <div class="container py-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="section-title mb-1">
                    <i class="bi bi-collection me-2"></i>Postingan Saya
                </h2>
                <p class="text-muted mb-0">Kelola semua postingan yang kamu buat.</p>
            </div>
            <Link href="/items/create" class="btn btn-accent">
                <i class="bi bi-plus me-1"></i>Buat Baru
            </Link>
        </div>

        <div v-if="!items.data.length" class="empty-state text-center py-5">
            <i class="bi bi-collection display-1 text-muted"></i>
            <h4 class="mt-3 text-muted">Kamu belum punya postingan</h4>
            <p class="text-muted">Mulai dengan membuat postingan barang hilang atau ditemukan.</p>
            <Link href="/items/create" class="btn btn-accent mt-2">
                <i class="bi bi-plus-circle me-1"></i>Buat Postingan Pertama
            </Link>
        </div>

        <template v-else>
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
                        <tr v-for="item in items.data" :key="item.id">
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img v-if="item.photo" :src="item.photo_url" :alt="item.name"
                                         class="rounded" width="48" height="48" style="object-fit:cover;">
                                    <div v-else class="bg-light rounded d-flex align-items-center justify-content-center"
                                         style="width:48px;height:48px;">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ item.name }}</div>
                                        <small class="text-muted">{{ truncate(item.description, 50) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge" :class="item.type === 'lost' ? 'bg-danger' : 'bg-success'">
                                    {{ item.type_label }}
                                </span>
                            </td>
                            <td>
                                <small><i class="bi bi-geo-alt me-1 text-muted"></i>{{ item.location }}</small>
                            </td>
                            <td>
                                <small>{{ formatDate(item.date_occurred) }}</small>
                            </td>
                            <td>
                                <span class="badge" :class="item.status === 'open' ? 'bg-warning text-dark' : 'bg-secondary'">
                                    {{ item.status === 'open' ? 'Aktif' : 'Selesai' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <Link :href="`/items/${item.id}`" class="btn btn-sm btn-outline-primary" title="Lihat">
                                        <i class="bi bi-eye"></i>
                                    </Link>
                                    <Link :href="`/items/${item.id}/edit`" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </Link>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus"
                                            @click="deleteItem(item)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav v-if="items.last_page > 1" class="mt-4 d-flex justify-content-center">
                <ul class="pagination">
                    <li v-for="(link, i) in items.links" :key="i"
                        class="page-item" :class="{ active: link.active, disabled: !link.url }">
                        <Link v-if="link.url" class="page-link" :href="link.url" preserve-scroll v-html="link.label" />
                        <span v-else class="page-link" v-html="link.label" />
                    </li>
                </ul>
            </nav>
        </template>

    </div>
</template>
