<script setup>
import { computed, ref, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth?.user || null);
const url = computed(() => page.url || '');

function isActive(type = null) {
    const [pathname, query = ''] = url.value.split('?');
    if (pathname !== '/items' && pathname !== '/') return false;
    if (type === null) return !query.includes('type=');
    return query.includes('type=' + type);
}

const initial = computed(() => (user.value?.name ? user.value.name.charAt(0).toUpperCase() : '?'));

const notifications = computed(() => page.props.notifications || []);
const unreadCount = computed(() => notifications.value.filter(n => n.type === 'comment' || (n.type === 'claim' && (!n.has_handover || n.status === 'rejected'))).length);
const showNotif = ref(false);

function formatNotifDate(d) {
    return new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' });
}

function logout() {
    router.post('/logout');
}

// Flash message (success / error) dari session Laravel
const flash = ref({ success: null, error: null });
watch(
    () => page.props.flash,
    (f) => {
        flash.value = { success: f?.success || null, error: f?.error || null };
    },
    { immediate: true, deep: true }
);

const year = new Date().getFullYear();
</script>

<template>
    <nav class="navbar navbar-expand-lg sticky-top" id="mainNav">
        <div class="container">
            <Link class="navbar-brand" href="/items">
                <img src="/images/logo-unsrat.png" alt="UNSRAT" style="height:30px;width:30px;object-fit:cover;border-radius:50%;" class="me-1">
                Lost<span class="text-accent">&amp;</span>Found
                <small>FATEK</small>
            </Link>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <Link class="nav-link" :class="{ active: isActive() }" href="/items">
                            <i class="bi bi-house me-1"></i>Beranda
                        </Link>
                    </li>
                    <li class="nav-item">
                        <Link class="nav-link" :class="{ active: isActive('lost') }" href="/items?type=lost">
                            <i class="bi bi-question-circle me-1"></i>Barang Hilang
                        </Link>
                    </li>
                    <li class="nav-item">
                        <Link class="nav-link" :class="{ active: isActive('found') }" href="/items?type=found">
                            <i class="bi bi-check-circle me-1"></i>Barang Ditemukan
                        </Link>
                    </li>
                </ul>

                <ul class="navbar-nav align-items-lg-center gap-2">
                    <template v-if="user">
                        <li class="nav-item">
                            <Link href="/items/create" class="btn btn-accent btn-sm">
                                <i class="bi bi-plus-circle me-1"></i>Buat Postingan
                            </Link>
                        </li>
                        <li class="nav-item" v-if="user?.is_admin">
                            <Link href="/admin" class="btn btn-danger btn-sm">
                                <i class="bi bi-shield-check me-1"></i>Admin
                            </Link>
                        </li>

                        <!-- Notification Bell -->
                        <li v-if="user" class="nav-item position-relative">
                            <button type="button" class="btn btn-light btn-sm position-relative notif-btn"
                                    @click.stop="showNotif = !showNotif">
                                <i class="bi bi-bell-fill"></i>
                                <span v-if="unreadCount > 0"
                                      class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem">
                                    {{ unreadCount }}
                                </span>
                            </button>

                            <!-- Overlay tutup dropdown kalau klik di luar -->
                            <div v-if="showNotif"
                                 class="position-fixed"
                                 style="inset:0;z-index:1049;"
                                 @click="showNotif = false">
                            </div>

                            <!-- Dropdown Notifikasi -->
                            <div v-if="showNotif" class="notif-dropdown shadow" @click.stop style="z-index:1050">
                                <div class="notif-header">
                                    <span class="fw-bold">Notifikasi</span>
                                    <button type="button" class="btn-close btn-sm" @click="showNotif = false"></button>
                                </div>
                                <div v-if="!notifications.length" class="notif-empty">
                                    <i class="bi bi-bell-slash text-muted fs-4"></i>
                                    <p class="text-muted small mb-0 mt-2">Belum ada notifikasi</p>
                                </div>
                                <div v-for="n in notifications" :key="n.id" class="notif-item"
                                     :class="{ 'notif-unread': (n.type==='claim' && !n.has_handover && n.status==='approved') || n.type==='comment' }">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="bi mt-1 flex-shrink-0"
                                           :class="{
                                               'bi-check-circle-fill text-success': n.type==='claim' && n.status==='approved',
                                               'bi-x-circle-fill text-danger':      n.type==='claim' && n.status==='rejected',
                                               'bi-chat-left-text-fill text-primary': n.type==='comment'
                                           }"></i>
                                        <div class="flex-grow-1">
                                            <p class="mb-0 small fw-semibold">{{ n.message }}</p>
                                            <p class="mb-0 small text-muted">{{ n.item_name }}</p>
                                            <p v-if="n.admin_note" class="mb-0 small fst-italic text-muted">"{{ n.admin_note }}"</p>
                                            <p v-if="n.type==='claim' && n.status==='approved' && !n.has_handover"
                                               class="mb-1 small text-warning fw-semibold">
                                                <i class="bi bi-camera me-1"></i>Upload foto serah terima!
                                            </p>
                                            <Link :href="'/items/' + n.item_id"
                                                  class="btn btn-outline-primary btn-sm py-0 px-2 mt-1"
                                                  style="font-size:.72rem"
                                                  @click="showNotif = false">
                                                Lihat Barang
                                            </Link>
                                            <p class="mb-0 text-muted mt-1" style="font-size:.7rem">{{ formatNotifDate(n.created_at) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2"
                               href="#" data-bs-toggle="dropdown">
                                <span class="avatar-sm">{{ initial }}</span>
                                {{ user.name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li><span class="dropdown-item-text small text-muted">{{ user.email }}</span></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <Link class="dropdown-item" href="/my-items">
                                        <i class="bi bi-collection me-2"></i>Postingan Saya
                                    </Link>
                                </li>
                                <li>
                                    <Link class="dropdown-item" href="/profile">
                                        <i class="bi bi-person me-2"></i>Edit Profil
                                    </Link>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <button type="button" class="dropdown-item text-danger" @click="logout">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </li>
                            </ul>
                        </li>
                    </template>

                    <template v-else>
                        <li class="nav-item">
                            <Link href="/login" class="btn btn-outline-primary btn-sm">Login</Link>
                        </li>
                        <li class="nav-item">
                            <Link href="/register" class="btn btn-primary btn-sm">Daftar</Link>
                        </li>
                    </template>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash messages -->
    <div class="container mt-3" v-if="flash.success || flash.error">
        <div v-if="flash.success" class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ flash.success }}
            <button type="button" class="btn-close" aria-label="Tutup" @click="flash.success = null"></button>
        </div>
        <div v-if="flash.error" class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>{{ flash.error }}
            <button type="button" class="btn-close" aria-label="Tutup" @click="flash.error = null"></button>
        </div>
    </div>

    <main>
        <slot />
    </main>

    <footer class="site-footer mt-5">
        <div class="container">
            <div class="row align-items-center py-4">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <span class="footer-brand d-inline-flex align-items-center gap-1">
                        <img src="/images/logo-unsrat.png" alt="UNSRAT" style="height:30px;width:30px;object-fit:cover;border-radius:50%;" class="me-1">
                        Lost<span class="text-accent">&amp;</span>Found
                        <small class="footer-badge">FATEK</small>
                    </span>
                    <p class="text-muted small mb-0 mt-2">
                        Membantu mahasiswa menemukan barang yang hilang di area kampus.
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <small class="text-muted">
                        &copy; {{ year }} Lost &amp; Found FATEK &mdash;
                        Dibuat untuk tugas Pengembangan Aplikasi Web Berbasis Framework
                    </small>
                </div>
            </div>
        </div>
    </footer>
</template>
