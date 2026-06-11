<script setup>
import { computed, reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps({
    items: Object,
    filters: { type: Object, default: () => ({}) },
    stats: { type: Object, default: () => ({ total: 0, lost: 0, found: 0 }) },
});

const f = reactive({
    search: props.filters.search || '',
    type: props.filters.type || '',
    status: props.filters.status || '',
});

const hasFilter = computed(() => !!(f.search || f.type || f.status));

function applyFilter() {
    router.get('/items', {
        search: f.search || undefined,
        type: f.type || undefined,
        status: f.status || undefined,
    }, { preserveScroll: true, preserveState: true, replace: true });
}

function resetFilter() {
    f.search = '';
    f.type = '';
    f.status = '';
    router.get('/items');
}

function truncate(text, n) {
    if (!text) return '';
    return text.length > n ? text.slice(0, n) + '...' : text;
}

function formatDate(d) {
    return new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}
</script>

<template>
    <Head title="Beranda" />

    <!-- Hero -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <h1 class="hero-title">
                        Ngoni pe barang ilang<br>
                        di kampus? <span class="text-accent">Cari jo SANDIRI disini!</span>
                    </h1>
                    <p class="hero-subtitle">
                        Lost &amp; Found khusus for mahasiswa Fakultas Teknik UNSRAT. SUPAYA APA SO? Supaya ngoni pe barang yang ilang
                        boleh ngoni mo dapa disini, ato yang dapa barang boleh ngoni mo posting supaya yang punya boleh mo dapa tau.
                    </p>
                </div>
                <div class="col-lg-5 d-none d-lg-flex justify-content-center">
                    <div class="hero-illustration"><i class="bi bi-search-heart"></i></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="stats-bar py-3">
        <div class="container">
            <div class="row text-center g-3">
                <div class="col-4">
                    <span class="stat-number">{{ stats.total }}</span>
                    <span class="stat-label">Total Postingan</span>
                </div>
                <div class="col-4">
                    <span class="stat-number">{{ stats.lost }}</span>
                    <span class="stat-label">Barang Hilang</span>
                </div>
                <div class="col-4">
                    <span class="stat-number">{{ stats.found }}</span>
                    <span class="stat-label">Barang Ditemukan</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Filter & Search -->
    <section class="py-4">
        <div class="container">
            <form class="search-bar-form" @submit.prevent="applyFilter">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-5">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input v-model="f.search" type="text" class="form-control" placeholder="Cari nama barang, lokasi...">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <select v-model="f.type" class="form-select">
                            <option value="">Semua Tipe</option>
                            <option value="lost">Barang Hilang</option>
                            <option value="found">Barang Ditemukan</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <select v-model="f.status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="open">Aktif</option>
                            <option value="resolved">Selesai</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                        <button v-if="hasFilter" type="button" class="btn btn-outline-secondary" @click="resetFilter">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- Daftar Item -->
    <section class="pb-5">
        <div class="container">
            <div class="mb-4">
                <h2 class="section-title mb-0">
                    <template v-if="f.type === 'lost'"><i class="bi bi-question-circle text-danger me-2"></i>Barang Hilang</template>
                    <template v-else-if="f.type === 'found'"><i class="bi bi-check-circle text-success me-2"></i>Barang Ditemukan</template>
                    <template v-else><i class="bi bi-grid me-2"></i>Semua Postingan</template>
                    <span class="badge bg-secondary ms-2">{{ items.total }}</span>
                </h2>
            </div>

            <div v-if="!items.data.length" class="empty-state text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <h4 class="mt-3 text-muted">Belum ada postingan</h4>
                <p class="text-muted">
                    <template v-if="hasFilter">
                        Tidak ada hasil yang cocok dengan filter kamu.
                        <a href="#" @click.prevent="resetFilter">Reset filter</a>
                    </template>
                    <template v-else>Jadilah yang pertama membuat postingan!</template>
                </p>
            </div>

            <div v-else class="row g-4">
                <div v-for="item in items.data" :key="item.id" class="col-lg-3 col-md-6 col-sm-12">
                    <div class="item-card card h-100 shadow-sm">
                        <div class="card-type-badge" :class="item.type === 'lost' ? 'badge-lost' : 'badge-found'">
                            <i class="bi me-1" :class="item.type === 'lost' ? 'bi-question-circle' : 'bi-check-circle'"></i>
                            {{ item.type_label }}
                        </div>

                        <div class="card-img-wrapper">
                            <img v-if="item.photo" :src="item.photo_url" class="card-img-top" :alt="item.name" loading="lazy">
                            <div v-else class="card-img-placeholder"><i class="bi bi-image"></i></div>

                            <div v-if="item.status === 'resolved'" class="card-resolved-overlay">
                                <i class="bi bi-check-circle-fill me-1"></i>Sudah Ditemukan
                            </div>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ item.name }}</h5>
                            <p class="card-text text-muted small flex-grow-1">{{ truncate(item.description, 80) }}</p>

                            <div class="card-meta mt-2">
                                <span class="meta-item"><i class="bi bi-geo-alt text-primary"></i> {{ truncate(item.location, 30) }}</span>
                                <span class="meta-item"><i class="bi bi-calendar3 text-primary"></i> {{ formatDate(item.date_occurred) }}</span>
                                <span class="meta-item"><i class="bi bi-person text-primary"></i> {{ item.user?.name }}</span>
                            </div>

                            <Link :href="`/items/${item.id}`" class="btn btn-outline-primary btn-sm mt-3 stretched-link">
                                Lihat Detail <i class="bi bi-arrow-right ms-1"></i>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <nav v-if="items.data.length && items.last_page > 1" class="mt-5 d-flex justify-content-center">
                <ul class="pagination">
                    <li v-for="(link, i) in items.links" :key="i"
                        class="page-item" :class="{ active: link.active, disabled: !link.url }">
                        <Link v-if="link.url" class="page-link" :href="link.url" preserve-scroll v-html="link.label" />
                        <span v-else class="page-link" v-html="link.label" />
                    </li>
                </ul>
            </nav>
        </div>
    </section>
</template>
