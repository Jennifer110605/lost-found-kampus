<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps({ user: Object });

const form = useForm({
    name: props.user.name || '',
    email: props.user.email || '',
    nim:   props.user.nim   || '',
    phone: props.user.phone || '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.put('/profile/update', {
        preserveScroll: true,
        onSuccess: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <Head title="Edit Profil" />

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <div class="card shadow form-card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="bi bi-person me-2 text-accent"></i>Edit Profil</h4>
                    </div>

                    <div class="card-body p-4">
                        <form @submit.prevent="submit">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama</label>
                                <input v-model="form.name" type="text" class="form-control"
                                       :class="{ 'is-invalid': form.errors.name }" required>
                                <div v-if="form.errors.name" class="invalid-feedback">{{ form.errors.name }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input v-model="form.email" type="email" class="form-control"
                                       :class="{ 'is-invalid': form.errors.email }" required>
                                <div v-if="form.errors.email" class="invalid-feedback">{{ form.errors.email }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">NIM</label>
                                <input v-model="form.nim" type="text" class="form-control"
                                       :class="{ 'is-invalid': form.errors.nim }"
                                       placeholder="Nomor Induk Mahasiswa">
                                <div v-if="form.errors.nim" class="invalid-feedback">{{ form.errors.nim }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nomor WhatsApp</label>
                                <input v-model="form.phone" type="text" class="form-control"
                                       :class="{ 'is-invalid': form.errors.phone }">
                                <div v-if="form.errors.phone" class="invalid-feedback">{{ form.errors.phone }}</div>
                            </div>

                            <hr class="my-4">
                            <p class="text-muted small mb-3">Kosongkan password jika tidak ingin menggantinya.</p>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Password Baru</label>
                                <input v-model="form.password" type="password" class="form-control"
                                       :class="{ 'is-invalid': form.errors.password }" autocomplete="new-password">
                                <div v-if="form.errors.password" class="invalid-feedback">{{ form.errors.password }}</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Konfirmasi Password</label>
                                <input v-model="form.password_confirmation" type="password" class="form-control"
                                       autocomplete="new-password">
                            </div>

                            <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                <i class="bi bi-save me-2"></i>Update Profil
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>
