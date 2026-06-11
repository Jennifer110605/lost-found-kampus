<script setup>
import { onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';

// Halaman auth pakai background gradient biru (.auth-body) seperti desain lama.
onMounted(() => document.body.classList.add('auth-body'));
onUnmounted(() => document.body.classList.remove('auth-body'));
</script>

<template>
    <div class="auth-wrapper">
        <div class="auth-brand text-center mb-4">
            <Link href="/items" class="text-decoration-none">
                <span class="brand-icon"><i class="bi bi-search-heart"></i></span>
                <span class="brand-text">Lost<span class="text-accent">&amp;</span>Found</span>
                <small class="brand-sub d-block">Kampus</small>
            </Link>
        </div>

        <div class="auth-card card shadow">
            <slot />
        </div>
    </div>
</template>
