<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const today = new Date().toISOString().slice(0, 10);

const form = useForm({
    type: 'lost',
    name: '',
    description: '',
    location: '',
    date_occurred: today,
    photo: null,
});

const preview = ref(null);

function onPhotoChange(e) {
    const file = e.target.files[0];
    form.photo = file || null;
    preview.value = file ? URL.createObjectURL(file) : null;
}

function submit() {
    form.post('/items', { forceFormData: true });
}
</script>

<template>
    <Head title="Buat Postingan" />

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><Link href="/items">Beranda</Link></li>
                        <li class="breadcrumb-item active">Buat Postingan</li>
                    </ol>
                </nav>

                <div class="card shadow form-card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="bi bi-plus-circle me-2 text-accent"></i>Buat Postingan Baru</h4>
                        <p class="text-muted small mb-0 mt-1">Isi detail barang hilang atau barang yang kamu temukan.</p>
                    </div>
                    <div class="card-body p-4">
                        <form @submit.prevent="submit">
                            <!-- Tipe -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Tipe Postingan <span class="text-danger">*</span></label>
                                <div class="type-selector d-flex gap-3">
                                    <label class="type-option flex-fill">
                                        <input v-model="form.type" type="radio" value="lost">
                                        <span class="type-box type-lost">
                                            <i class="bi bi-question-circle-fill"></i>
                                            <strong>Barang Hilang</strong>
                                            <small>Saya kehilangan barang</small>
                                        </span>
                                    </label>
                                    <label class="type-option flex-fill">
                                        <input v-model="form.type" type="radio" value="found">
                                        <span class="type-box type-found">
                                            <i class="bi bi-check-circle-fill"></i>
                                            <strong>Barang Ditemukan</strong>
                                            <small>Saya menemukan barang</small>
                                        </span>
                                    </label>
                                </div>
                                <div v-if="form.errors.type" class="text-danger small mt-1">{{ form.errors.type }}</div>
                            </div>

                            <!-- Nama -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                                <input v-model="form.name" type="text" class="form-control" :class="{ 'is-invalid': form.errors.name }"
                                       placeholder="Contoh: Kartu Mahasiswa, Dompet Hitam, Botol Minum..." autofocus>
                                <div v-if="form.errors.name" class="invalid-feedback">{{ form.errors.name }}</div>
                            </div>

                            <!-- Deskripsi -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Deskripsi <span class="text-danger">*</span></label>
                                <textarea v-model="form.description" rows="4" class="form-control" :class="{ 'is-invalid': form.errors.description }"
                                          placeholder="Jelaskan ciri-ciri barang secara detail (warna, ukuran, merek, kondisi, dll.)"></textarea>
                                <div class="form-text">Semakin detail deskripsi, semakin mudah dikenali.</div>
                                <div v-if="form.errors.description" class="invalid-feedback d-block">{{ form.errors.description }}</div>
                            </div>

                            <!-- Lokasi & Tanggal -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-7">
                                    <label class="form-label fw-semibold">Lokasi <span class="text-danger">*</span></label>
                                    <input v-model="form.location" type="text" class="form-control" :class="{ 'is-invalid': form.errors.location }"
                                           placeholder="Contoh: Gedung A Lantai 2, Kantin Utama...">
                                    <div v-if="form.errors.location" class="invalid-feedback">{{ form.errors.location }}</div>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                                    <input v-model="form.date_occurred" type="date" class="form-control" :class="{ 'is-invalid': form.errors.date_occurred }" :max="today">
                                    <div v-if="form.errors.date_occurred" class="invalid-feedback">{{ form.errors.date_occurred }}</div>
                                </div>
                            </div>

                            <!-- Foto -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Foto Barang</label>
                                <div class="photo-upload-area">
                                    <input type="file" class="photo-input" accept="image/jpg,image/jpeg,image/png,image/webp" @change="onPhotoChange">
                                    <div v-show="!preview" class="photo-upload-placeholder">
                                        <i class="bi bi-cloud-upload"></i>
                                        <p>Klik atau drag foto ke sini</p>
                                        <small>JPG, PNG, WEBP — Maks. 2MB</small>
                                    </div>
                                    <img v-show="preview" :src="preview" alt="Preview" class="photo-preview">
                                </div>
                                <div v-if="form.errors.photo" class="text-danger small mt-1">{{ form.errors.photo }}</div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-accent btn-lg flex-fill" :disabled="form.processing">
                                    <i class="bi bi-send me-2"></i>Kirim Postingan
                                </button>
                                <Link href="/items" class="btn btn-outline-secondary btn-lg">Batal</Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
