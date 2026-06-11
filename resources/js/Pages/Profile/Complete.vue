<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

defineOptions({ layout: AuthLayout });

const props = defineProps({ user: Object });

const form = useForm({
    nim:   props.user.nim   || '',
    phone: props.user.phone || '',
});

function submit() {
    form.post('/profile/complete');
}
</script>

<template>
    <Head title="Lengkapi Profil" />

    <div class="auth-card card shadow p-4">
        <div class="text-center mb-4">
            <div class="mb-2" style="font-size:2.5rem">📋</div>
            <h5 class="auth-title fw-bold">Lengkapi Profilmu</h5>
            <p class="text-muted small">
                Halo, <strong>{{ user.name }}</strong>! Isi NIM dan nomor HP dulu
                sebelum menggunakan aplikasi.
            </p>
        </div>

        <form @submit.prevent="submit">
            <div class="mb-3">
                <label class="form-label fw-semibold">NIM <span class="text-danger">*</span></label>
                <input v-model="form.nim" type="text" class="form-control"
                       :class="{ 'is-invalid': form.errors.nim }"
                       placeholder="Contoh: 230211060081" autofocus>
                <div v-if="form.errors.nim" class="invalid-feedback">{{ form.errors.nim }}</div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">
                    Nomor WhatsApp <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-whatsapp text-success"></i></span>
                    <input v-model="form.phone" type="text" class="form-control"
                           :class="{ 'is-invalid': form.errors.phone }"
                           placeholder="Contoh: 081234567890">
                </div>
                <div class="form-text">Nomor ini digunakan untuk dihubungi via WhatsApp.</div>
                <div v-if="form.errors.phone" class="text-danger small mt-1">{{ form.errors.phone }}</div>
            </div>

            <button type="submit" class="btn btn-primary w-100" :disabled="form.processing">
                <i class="bi bi-check-circle me-2"></i>Simpan &amp; Lanjutkan
            </button>
        </form>
    </div>
</template>
