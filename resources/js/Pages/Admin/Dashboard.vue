<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    stats: Object,
    recentItems: Array,
});

function formatDate(d) {
    return new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}
</script>

<template>
    <Head title="Admin Dashboard" />

    <div class="p-4">
        <!-- Stat Cards -->
        <div class="row g-4 mb-5">
            <div class="col-sm-6 col-xl-3">
                <div class="admin-stat-card">
                    <div class="stat-icon bg-primary-soft"><i class="bi bi-people-fill text-primary"></i></div>
                    <div>
                        <div class="stat-num">{{ stats.users }}</div>
                        <div class="stat-lbl">Total User</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="admin-stat-card">
                    <div class="stat-icon bg-info-soft"><i class="bi bi-collection-fill text-info"></i></div>
                    <div>
                        <div class="stat-num">{{ stats.items }}</div>
                        <div class="stat-lbl">Total Postingan</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="admin-stat-card">
                    <div class="stat-icon bg-danger-soft"><i class="bi bi-question-circle-fill text-danger"></i></div>
                    <div>
                        <div class="stat-num">{{ stats.lost }}</div>
                        <div class="stat-lbl">Barang Hilang</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="admin-stat-card">
                    <div class="stat-icon bg-success-soft"><i class="bi bi-check-circle-fill text-success"></i></div>
                    <div>
                        <div class="stat-num">{{ stats.found }}</div>
                        <div class="stat-lbl">Barang Ditemukan</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status overview -->
        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-4">
                        <div class="display-4 fw-bold text-warning mb-1">{{ stats.open }}</div>
                        <div class="text-muted fw-semibold">Postingan Aktif</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-4">
                        <div class="display-4 fw-bold text-secondary mb-1">{{ stats.resolved }}</div>
                        <div class="text-muted fw-semibold">Postingan Selesai</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent items -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0 fw-bold">Postingan Terbaru</h6>
                <Link href="/admin/items" class="btn btn-sm btn-outline-primary">Lihat Semua</Link>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Barang</th>
                            <th>Tipe</th>
                            <th>Pelapor</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in recentItems" :key="item.id">
                            <td class="fw-semibold">
                                <Link :href="`/items/${item.id}`" class="text-decoration-none text-dark">
                                    {{ item.name }}
                                </Link>
                            </td>
                            <td>
                                <span class="badge" :class="item.type === 'lost' ? 'bg-danger' : 'bg-success'">
                                    {{ item.type_label }}
                                </span>
                            </td>
                            <td class="text-muted small">{{ item.user?.name }}</td>
                            <td>
                                <span class="badge" :class="item.status === 'open' ? 'bg-warning text-dark' : 'bg-secondary'">
                                    {{ item.status === 'open' ? 'Aktif' : 'Selesai' }}
                                </span>
                            </td>
                            <td class="text-muted small">{{ formatDate(item.created_at) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
