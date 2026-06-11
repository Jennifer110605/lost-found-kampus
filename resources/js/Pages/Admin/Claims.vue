<script setup>
import { reactive, ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    claims: Object,
    filters: { type: Object, default: () => ({}) },
    counts: Object,
});

const statusFilter = ref(props.filters.status || '');

function setFilter(val) {
    statusFilter.value = val;
    router.get('/admin/claims', { status: val || undefined },
        { preserveState: true, replace: true });
}

// Approve form
const approveModal = ref(null);
const approveForm = useForm({ note: '', claim_id: null });
function openApprove(claim) {
    approveModal.value = claim;
    approveForm.reset();
}
function submitApprove() {
    approveForm.post(`/admin/claims/${approveModal.value.id}/approve`, {
        preserveScroll: true,
        onSuccess: () => { approveModal.value = null; },
    });
}

// Reject form
const rejectModal = ref(null);
const rejectForm = useForm({ note: '' });
function openReject(claim) {
    rejectModal.value = claim;
    rejectForm.reset();
}
function submitReject() {
    rejectForm.post(`/admin/claims/${rejectModal.value.id}/reject`, {
        preserveScroll: true,
        onSuccess: () => { rejectModal.value = null; },
    });
}

function formatDate(d) {
    return new Date(d).toLocaleDateString('id-ID', {
        day: '2-digit', month: 'short', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });
}

const statusLabel = { pending: 'Menunggu', approved: 'Disetujui', rejected: 'Ditolak' };
const statusClass = { pending: 'bg-warning text-dark', approved: 'bg-success', rejected: 'bg-danger' };
</script>

<template>
    <Head title="Admin - Klaim" />

    <div class="p-4">
        <!-- Stat tabs -->
        <div class="d-flex gap-3 mb-4 flex-wrap">
            <button class="btn" :class="!statusFilter ? 'btn-primary' : 'btn-outline-secondary'"
                    @click="setFilter('')">
                Semua <span class="badge bg-secondary ms-1">{{ counts.pending + counts.approved + counts.rejected }}</span>
            </button>
            <button class="btn" :class="statusFilter === 'pending' ? 'btn-warning text-dark' : 'btn-outline-warning'"
                    @click="setFilter('pending')">
                Menunggu <span class="badge bg-warning text-dark ms-1">{{ counts.pending }}</span>
            </button>
            <button class="btn" :class="statusFilter === 'approved' ? 'btn-success' : 'btn-outline-success'"
                    @click="setFilter('approved')">
                Disetujui <span class="badge bg-success ms-1">{{ counts.approved }}</span>
            </button>
            <button class="btn" :class="statusFilter === 'rejected' ? 'btn-danger' : 'btn-outline-danger'"
                    @click="setFilter('rejected')">
                Ditolak <span class="badge bg-danger ms-1">{{ counts.rejected }}</span>
            </button>
        </div>

        <!-- Table -->
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Barang</th>
                            <th>Pelapor Klaim</th>
                            <th>Deskripsi Bukti</th>
                            <th>Foto Bukti</th>
                            <th>Status</th>
                            <th>Waktu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!claims.data.length">
                            <td colspan="8" class="text-center text-muted py-5">Tidak ada klaim.</td>
                        </tr>
                        <tr v-for="claim in claims.data" :key="claim.id">
                            <td class="text-muted small">{{ claim.id }}</td>
                            <td>
                                <Link :href="`/items/${claim.item_id}`" class="fw-semibold text-decoration-none text-dark">
                                    {{ claim.item?.name }}
                                </Link>
                                <div class="small text-muted">
                                    <span class="badge" :class="claim.item?.type === 'lost' ? 'bg-danger' : 'bg-success'">
                                        {{ claim.item?.type === 'lost' ? 'Hilang' : 'Ditemukan' }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold small">{{ claim.user?.name }}</div>
                                <div class="text-muted" style="font-size:.75rem">{{ claim.user?.email }}</div>
                                <div class="text-muted" style="font-size:.75rem">{{ claim.user?.phone }}</div>
                            </td>
                            <td style="max-width:220px">
                                <p class="small mb-0" style="white-space:pre-line;word-break:break-word">{{ claim.description }}</p>
                            </td>
                            <td>
                                <a v-if="claim.proof_photo" :href="`/storage/${claim.proof_photo}`"
                                   target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-image me-1"></i>Lihat
                                </a>
                                <span v-else class="text-muted small">—</span>
                            </td>
                            <td>
                                <span class="badge" :class="statusClass[claim.status]">
                                    {{ statusLabel[claim.status] }}
                                </span>
                                <div v-if="claim.admin_note" class="small text-muted mt-1 fst-italic">
                                    "{{ claim.admin_note }}"
                                </div>
                                <div v-if="claim.handover_photo" class="mt-1">
                                    <a :href="`/storage/${claim.handover_photo}`" target="_blank"
                                       class="btn btn-xs btn-outline-success btn-sm" style="font-size:.72rem">
                                        <i class="bi bi-camera me-1"></i>Foto Serah Terima
                                    </a>
                                </div>
                            </td>
                            <td class="text-muted small">{{ formatDate(claim.created_at) }}</td>
                            <td>
                                <div v-if="claim.status === 'pending'" class="d-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-success" @click="openApprove(claim)">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" @click="openReject(claim)">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                                <span v-else class="text-muted small">—</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="claims.last_page > 1" class="card-footer bg-white d-flex justify-content-center py-3">
                <ul class="pagination mb-0">
                    <li v-for="(link, i) in claims.links" :key="i"
                        class="page-item" :class="{ active: link.active, disabled: !link.url }">
                        <Link v-if="link.url" class="page-link" :href="link.url" preserve-scroll v-html="link.label" />
                        <span v-else class="page-link" v-html="link.label" />
                    </li>
                </ul>
            </div>
        </div>

        <!-- Modal Approve -->
        <div v-if="approveModal" class="modal-backdrop-custom" @click.self="approveModal = null">
            <div class="modal-card">
                <h6 class="fw-bold mb-3"><i class="bi bi-check-circle text-success me-2"></i>Setujui Klaim #{{ approveModal.id }}</h6>
                <p class="text-muted small mb-3">
                    Klaim dari <strong>{{ approveModal.user?.name }}</strong> untuk barang
                    <strong>{{ approveModal.item?.name }}</strong>.
                    Item akan otomatis ditandai <strong>Selesai</strong>.
                </p>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Catatan Admin (opsional)</label>
                    <textarea v-model="approveForm.note" rows="3" class="form-control"
                              placeholder="Misal: Bukti kepemilikan valid, silakan ambil barang di..."></textarea>
                </div>
                <div class="d-flex gap-2 justify-content-end">
                    <button type="button" class="btn btn-secondary" @click="approveModal = null">Batal</button>
                    <button type="button" class="btn btn-success" :disabled="approveForm.processing"
                            @click="submitApprove">
                        <i class="bi bi-check-lg me-1"></i>Setujui
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Reject -->
        <div v-if="rejectModal" class="modal-backdrop-custom" @click.self="rejectModal = null">
            <div class="modal-card">
                <h6 class="fw-bold mb-3"><i class="bi bi-x-circle text-danger me-2"></i>Tolak Klaim #{{ rejectModal.id }}</h6>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Alasan Penolakan <span class="text-danger">*</span></label>
                    <textarea v-model="rejectForm.note" rows="3" class="form-control"
                              :class="{ 'is-invalid': rejectForm.errors.note }"
                              placeholder="Misal: Bukti tidak cukup, deskripsi tidak sesuai..."></textarea>
                    <div v-if="rejectForm.errors.note" class="invalid-feedback">{{ rejectForm.errors.note }}</div>
                </div>
                <div class="d-flex gap-2 justify-content-end">
                    <button type="button" class="btn btn-secondary" @click="rejectModal = null">Batal</button>
                    <button type="button" class="btn btn-danger" :disabled="rejectForm.processing"
                            @click="submitReject">
                        <i class="bi bi-x-lg me-1"></i>Tolak Klaim
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
