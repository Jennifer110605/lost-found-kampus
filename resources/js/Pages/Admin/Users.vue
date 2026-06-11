<script setup>
import { reactive } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    users: Object,
    filters: { type: Object, default: () => ({}) },
});

const page = usePage();
const authId = page.props.auth?.user?.id;

const f = reactive({ search: props.filters.search || '' });

function applyFilter() {
    router.get('/admin/users', { search: f.search || undefined },
        { preserveState: true, preserveScroll: true, replace: true });
}

function resetFilter() { f.search = ''; router.get('/admin/users'); }

function deleteUser(id, name) {
    if (!confirm(`Hapus user "${name}"? Semua postingannya juga akan terhapus.`)) return;
    router.delete(`/admin/users/${id}`, { preserveScroll: true });
}

function toggleAdmin(id, name, isAdmin) {
    const msg = isAdmin ? `Cabut status admin dari "${name}"?` : `Jadikan "${name}" sebagai admin?`;
    if (!confirm(msg)) return;
    router.post(`/admin/users/${id}/toggle-admin`, {}, { preserveScroll: true });
}

function formatDate(d) {
    return new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}
</script>

<template>
    <Head title="Admin - User" />

    <div class="p-4">
        <!-- Search -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form class="row g-3 align-items-end" @submit.prevent="applyFilter">
                    <div class="col-md-8">
                        <input v-model="f.search" type="text" class="form-control"
                               placeholder="Cari nama, email, atau NIM...">
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">Cari</button>
                        <button type="button" class="btn btn-outline-secondary" @click="resetFilter">Reset</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Semua User</h6>
                <span class="badge bg-primary">{{ users.total }} total</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>NIM</th>
                            <th>Email</th>
                            <th>Postingan</th>
                            <th>Role</th>
                            <th>Bergabung</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!users.data.length">
                            <td colspan="8" class="text-center text-muted py-5">Tidak ada user.</td>
                        </tr>
                        <tr v-for="user in users.data" :key="user.id">
                            <td class="text-muted small">{{ user.id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="avatar-sm">{{ user.name.charAt(0).toUpperCase() }}</span>
                                    <span class="fw-semibold">{{ user.name }}</span>
                                    <span v-if="user.id === authId" class="badge bg-secondary">Kamu</span>
                                </div>
                            </td>
                            <td class="small text-muted">{{ user.nim }}</td>
                            <td class="small text-muted">{{ user.email }}</td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark">{{ user.items_count }}</span>
                            </td>
                            <td>
                                <span class="badge" :class="user.is_admin ? 'bg-danger' : 'bg-secondary'">
                                    {{ user.is_admin ? 'Admin' : 'User' }}
                                </span>
                            </td>
                            <td class="small text-muted">{{ formatDate(user.created_at) }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button v-if="user.id !== authId" type="button"
                                            class="btn btn-sm"
                                            :class="user.is_admin ? 'btn-outline-warning' : 'btn-outline-primary'"
                                            :title="user.is_admin ? 'Cabut admin' : 'Jadikan admin'"
                                            @click="toggleAdmin(user.id, user.name, user.is_admin)">
                                        <i class="bi" :class="user.is_admin ? 'bi-shield-x' : 'bi-shield-check'"></i>
                                    </button>
                                    <button v-if="user.id !== authId" type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Hapus user"
                                            @click="deleteUser(user.id, user.name)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="users.last_page > 1" class="card-footer bg-white d-flex justify-content-center py-3">
                <ul class="pagination mb-0">
                    <li v-for="(link, i) in users.links" :key="i"
                        class="page-item" :class="{ active: link.active, disabled: !link.url }">
                        <Link v-if="link.url" class="page-link" :href="link.url" preserve-scroll v-html="link.label" />
                        <span v-else class="page-link" v-html="link.label" />
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
