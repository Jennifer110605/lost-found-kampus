<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

defineOptions({ layout: AuthLayout });

const form = useForm({
    name: '',
    nim: '',
    phone: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);
const showConfirm = ref(false);

function submit() {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <Head title="Daftar" />

    <div class="card-body p-4 p-md-5">
        <h3 class="auth-title text-center mb-1">Buat Akun</h3>
        <p class="text-muted text-center mb-4 small">Daftar untuk mulai menggunakan Lost &amp; Found Kampus.</p>

        <form @submit.prevent="submit">
            <!-- Nama -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input
                        v-model="form.name"
                        type="text"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.name }"
                        placeholder="Nama lengkap kamu"
                        required
                        autofocus
                    >
                    <div v-if="form.errors.name" class="invalid-feedback">{{ form.errors.name }}</div>
                </div>
            </div>

            <!-- NIM & No HP -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">NIM</label>
                    <input
                        v-model="form.nim"
                        type="text"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.nim }"
                        placeholder="Nomor Induk Mahasiswa"
                    >
                    <div v-if="form.errors.nim" class="invalid-feedback">{{ form.errors.nim }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">No. HP</label>
                    <input
                        v-model="form.phone"
                        type="text"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.phone }"
                        placeholder="08xxxxxxxxxx"
                    >
                    <div v-if="form.errors.phone" class="invalid-feedback">{{ form.errors.phone }}</div>
                </div>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input
                        v-model="form.email"
                        type="email"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.email }"
                        placeholder="email@kampus.ac.id"
                        required
                    >
                    <div v-if="form.errors.email" class="invalid-feedback">{{ form.errors.email }}</div>
                </div>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input
                        v-model="form.password"
                        :type="showPassword ? 'text' : 'password'"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.password }"
                        placeholder="Minimal 8 karakter"
                        required
                    >
                    <button
                        class="btn toggle-password"
                        type="button"
                        :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'"
                        @click="showPassword = !showPassword"
                    >
                        <i class="bi" :class="showPassword ? 'bi-eye-slash' : 'bi-eye'"></i>
                    </button>
                    <div v-if="form.errors.password" class="invalid-feedback">{{ form.errors.password }}</div>
                </div>
            </div>

            <!-- Konfirmasi Password -->
            <div class="mb-4">
                <label class="form-label fw-semibold">Konfirmasi Password <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input
                        v-model="form.password_confirmation"
                        :type="showConfirm ? 'text' : 'password'"
                        class="form-control"
                        placeholder="Ulangi password kamu"
                        required
                    >
                    <button
                        class="btn toggle-password"
                        type="button"
                        :aria-label="showConfirm ? 'Sembunyikan password' : 'Tampilkan password'"
                        @click="showConfirm = !showConfirm"
                    >
                        <i class="bi" :class="showConfirm ? 'bi-eye-slash' : 'bi-eye'"></i>
                    </button>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn btn-accent w-100 btn-lg" :disabled="form.processing">
                <i class="bi bi-person-plus me-2"></i>Buat Akun
            </button>
        </form>

        <div class="auth-divider"><span>atau</span></div>

        <a href="/auth/google/redirect" class="btn btn-google w-100 btn-lg">
            <svg width="18" height="18" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844a4.14 4.14 0 0 1-1.796 2.716v2.259h2.908c1.702-1.567 2.684-3.875 2.684-6.615z"/>
                <path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18z"/>
                <path fill="#FBBC05" d="M3.964 10.71A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.042l3.007-2.332z"/>
                <path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.958L3.964 7.29C4.672 5.163 6.656 3.58 9 3.58z"/>
            </svg>
            <span>Daftar dengan Google</span>
        </a>

        <hr class="my-4">

        <p class="text-center mb-0 small">
            Sudah punya akun?
            <Link href="/login" class="fw-semibold">Login di sini</Link>
        </p>
    </div>
</template>
