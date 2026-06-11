<script setup>
import { reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    items: Object,
    filters: { type: Object, default: () => ({}) },
});

const f = reactive({
    search: props.filters.search || '',
    type:   props.filters.type   || '',
    status: props.filters.status || '',
});

function applyFilter() {
    router.get('/admin/items', {
        search: f.search || undefined,
        type:   f.type   || undefined,
        status: f.status || undefined,
    }, { preserveState: true, preserveScroll: true, replace: true });
}

function resetFilter() {
    f.search = ''; f.type = ''; f.status = '';
    router.get('/admin/items');
}

function deleteItem(id, name) {
    if (!confirm(`Hapus postingan "${name}"?`)) return;
    router.delete(`/admin/items/${id}`, { preserveScroll: true });
}

function formatDate(d) {
    return new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}
</script>

<template>
    <Head title="Admin - Postingan" />

    <div class="p-4">
        <!-- Filter -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form class="row g-3 align-items-end" @submit.prevent="applyFilter">
                    <div class="col-md-5">
                        <input v-model="f.search" type="text" class="form-control" placeholder="Cari nama barang...">
                    </div>
                    <div class="col-md-2">
                        <select v-model="f.type" class="form-select">
                            <option value="">Semua Tipe</option>
                            <option value="lost">Hilang</option>
                            <option value="found">Ditemukan</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select v-model="f.status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="open">Aktif</option>
                            <option value="resolved">Selesai</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">Filter</button>
                        <button type="button" class="btn btn-outline-secondary" @click="resetFilter">Reset</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Semua Postingan</h6>
                <span class="badge bg-primary">{{ items.total }} total</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nama Barang</th>
                            <th>Tipe</th>
                            <th>Pelapor</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!items.data.length">
                            <td colspan="8" class="text-center text-muted py-5">Tidak ada postingan.</td>
                        </tr>
                        <tr v-for="item in items.data" :key="item.id">
                            <td class="text-muted small">{{ item.id }}</td>
                            <td class="fw-semibold">
                                <Link :href="`/items/${item.id}`" class="text-decoration-none text-dark">{{ item.name }}</Link>
                            </td>
                            <td>
                                <span class="badge" :class="item.type === 'lost' ? 'bg-danger' : 'bg-success'">
                                    {{ item.type_label }}
                                </span>
                            </td>
                            <td class="small">{{ item.user?.name }}</td>
                            <td class="small text-muted">{{ item.location }}</td>
                            <td>
                                <span class="badge" :class="item.status === 'open' ? 'bg-warning text-dark' : 'bg-secondary'">
                                    {{ item.status === 'open' ? 'Aktif' : 'Selesai' }}
                                </span>
                            </td>
                            <td class="small text-muted">{{ formatDate(item.created_at) }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                        @click="deleteItem(item.id, item.name)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="items.last_page > 1" class="card-footer bg-white d-flex justify-content-center py-3">
                <ul class="pagination mb-0">
                    <li v-for="(link, i) in items.links" :key="i"
                        class="page-item" :class="{ active: link.active, disabled: !link.url }">
                        <Link v-if="link.url" class="page-link" :href="link.url" preserve-scroll v-html="link.label" />
                        <span v-else class="page-link" v-html="link.label" />
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
