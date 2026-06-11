<script setup>
import { computed, ref, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const url  = computed(() => page.url || '');
const sidebarOpen = ref(false);

const pageTitles = {
    '/admin':       'Dashboard',
    '/admin/items': 'Kelola Postingan',
    '/admin/users': 'Kelola User',
    '/admin/claims': 'Kelola Klaim',
};
const pageTitle = computed(() => pageTitles[url.value.split('?')[0]] || 'Admin Panel');

function isActive(path) {
    return url.value.split('?')[0] === path;
}

function logout() {
    router.post('/logout');
}

// Flash messages
const flash = ref({ success: null, error: null });
watch(() => page.props.flash, (f) => {
    flash.value = { success: f?.success || null, error: f?.error || null };
}, { immediate: true, deep: true });
</script>

<template>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar" :class="{ 'sidebar-open': sidebarOpen }">
            <div class="sidebar-brand">
                <Link href="/admin" class="text-decoration-none d-flex align-items-center gap-2">
                    <i class="bi bi-shield-check fs-5"></i>
                    <div>
                        <div class="sidebar-brand-title">Lost&amp;Found</div>
                        <div class="sidebar-brand-sub">Admin Panel</div>
                    </div>
                </Link>
            </div>

            <nav class="sidebar-nav">
                <Link href="/admin" class="sidebar-link" :class="{ active: isActive('/admin') }">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </Link>
                <Link href="/admin/items" class="sidebar-link" :class="{ active: isActive('/admin/items') }">
                    <i class="bi bi-collection"></i>
                    <span>Postingan</span>
                </Link>
                <Link href="/admin/users" class="sidebar-link" :class="{ active: isActive('/admin/users') }">
                    <i class="bi bi-people"></i>
                    <span>User</span>
                </Link>
                <Link href="/admin/claims" class="sidebar-link" :class="{ active: isActive('/admin/claims') }">
                    <i class="bi bi-clipboard-check"></i>
                    <span>Klaim</span>
                </Link>
            </nav>

            <div class="sidebar-footer">
                <Link href="/items" class="sidebar-link">
                    <i class="bi bi-arrow-left-circle"></i>
                    <span>Kembali ke App</span>
                </Link>
                <button type="button" class="sidebar-link text-danger border-0 bg-transparent w-100 text-start" @click="logout">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </button>
            </div>
        </aside>

        <!-- Overlay mobile -->
        <div v-if="sidebarOpen" class="sidebar-overlay" @click="sidebarOpen = false"></div>

        <!-- Main content -->
        <div class="admin-content">
            <!-- Topbar -->
            <header class="admin-topbar">
                <button class="btn btn-sm btn-outline-secondary d-lg-none me-3" @click="sidebarOpen = !sidebarOpen">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h5 class="mb-0 fw-bold">{{ pageTitle }}</h5>
                <div class="ms-auto d-flex align-items-center gap-3">
                    <span class="badge bg-danger">Admin</span>
                    <span class="fw-semibold small">{{ user?.name }}</span>
                </div>
            </header>

            <!-- Flash -->
            <div class="px-4 pt-3" v-if="flash.success || flash.error">
                <div v-if="flash.success" class="alert alert-success alert-dismissible mb-0" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ flash.success }}
                    <button type="button" class="btn-close" @click="flash.success = null"></button>
                </div>
                <div v-if="flash.error" class="alert alert-danger alert-dismissible mb-0" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>{{ flash.error }}
                    <button type="button" class="btn-close" @click="flash.error = null"></button>
                </div>
            </div>

            <main class="admin-main">
                <slot />
            </main>
        </div>
    </div>
</template>
