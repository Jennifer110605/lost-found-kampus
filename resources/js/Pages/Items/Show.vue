<script setup>
import { computed, reactive, ref } from 'vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps({ item: Object, userClaim: { type: Object, default: null } });

const page = usePage();
const authUser = computed(() => page.props.auth?.user || null);
const isOwner = computed(() => authUser.value && authUser.value.id === props.item.user_id);

// Komentar level atas (parent_id null); tiap komentar bawa array replies
const topComments = computed(() => (props.item.comments || []).filter((c) => c.parent_id === null));

function canDelete(comment) {
    return authUser.value && authUser.value.id === comment.user_id;
}


// WhatsApp link helper
function waLink(phone, itemName) {
    if (!phone) return null;
    let p = phone.replace(/\D/g, '');
    if (p.startsWith('0')) p = '62' + p.slice(1);
    else if (!p.startsWith('62')) p = '62' + p;
    const msg = encodeURIComponent(
        'Halo, saya dari web Lost & Found FATEK UNSRAT. Saya ingin menghubungi Anda terkait postingan barang "' + (itemName || '') + '". Terima kasih.'
    );
    return 'https://wa.me/' + p + '?text=' + msg;
}

// Claim form
const claimForm = useForm({ description: '', proof_photo: null });
const claimPhotoPreview = ref(null);
function onClaimPhotoChange(e) {
    const f = e.target.files[0];
    claimForm.proof_photo = f || null;
    claimPhotoPreview.value = f ? URL.createObjectURL(f) : null;
}
function submitClaim() {
    claimForm.post('/items/' + props.item.id + '/claim', { forceFormData: true, preserveScroll: true, onSuccess: () => claimForm.reset() });
}
// Handover upload
const handoverForm = useForm({ handover_photo: null });
const handoverPreview = ref(null);
function onHandoverChange(e) {
    const f = e.target.files[0];
    handoverForm.handover_photo = f || null;
    handoverPreview.value = f ? URL.createObjectURL(f) : null;
}
function submitHandover() {
    if (!props.userClaim?.id) return;
    handoverForm.post('/claims/' + props.userClaim.id + '/handover', { forceFormData: true, preserveScroll: true });
}

// --- Format tanggal & waktu (Bahasa Indonesia) ---
function formatLongDate(d) {
    if (!d) return '';
    return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
}

function timeAgo(d) {
    if (!d) return '';
    const date = new Date(d);
    const diff = Math.floor((Date.now() - date.getTime()) / 1000);
    if (diff < 60) return 'baru saja';
    const mnt = Math.floor(diff / 60);
    if (mnt < 60) return `${mnt} menit yang lalu`;
    const jam = Math.floor(mnt / 60);
    if (jam < 24) return `${jam} jam yang lalu`;
    const hari = Math.floor(jam / 24);
    if (hari < 30) return `${hari} hari yang lalu`;
    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
}

const wasEdited = computed(() => {
    if (!props.item.updated_at || !props.item.created_at) return false;
    return new Date(props.item.updated_at).getTime() > new Date(props.item.created_at).getTime();
});

// --- Komentar utama ---
const commentForm = useForm({ content: '', parent_id: null });

function submitComment() {
    commentForm.post(`/items/${props.item.id}/comments`, {
        preserveScroll: true,
        onSuccess: () => commentForm.reset('content'),
    });
}

// --- Balasan per-komentar ---
const replyText = reactive({});
const replyProcessing = reactive({});

function submitReply(comment) {
    const content = (replyText[comment.id] || '').trim();
    if (!content) return;
    replyProcessing[comment.id] = true;
    router.post(
        `/items/${props.item.id}/comments`,
        { content, parent_id: comment.id },
        {
            preserveScroll: true,
            onSuccess: () => { replyText[comment.id] = ''; },
            onFinish: () => { replyProcessing[comment.id] = false; },
        }
    );
}

// --- Hapus ---
function deleteComment(comment) {
    if (!confirm('Yakin ingin menghapus komentar ini?')) return;
    router.delete(`/comments/${comment.id}`, { preserveScroll: true });
}

function deleteItem() {
    if (!confirm('Yakin ingin menghapus postingan ini?')) return;
    router.delete(`/items/${props.item.id}`);
}
</script>

<template>
    <Head :title="item.name" />

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><Link href="/items">Beranda</Link></li>
                        <li class="breadcrumb-item active">{{ item.name }}</li>
                    </ol>
                </nav>

                <div class="detail-card card shadow">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <span class="badge fs-6 px-3 py-2" :class="item.type === 'lost' ? 'bg-danger' : 'bg-success'">
                            <i class="bi me-2" :class="item.type === 'lost' ? 'bi-question-circle' : 'bi-check-circle'"></i>
                            {{ item.type_label }}
                        </span>
                        <span class="badge" :class="item.status === 'open' ? 'bg-warning text-dark' : 'bg-secondary'">
                            {{ item.status === 'open' ? 'Aktif' : 'Selesai' }}
                        </span>
                    </div>

                    <div class="card-body p-4">
                        <!-- Foto -->
                        <div v-if="item.photo" class="detail-photo mb-4">
                            <img :src="item.photo_url" :alt="item.name" class="img-fluid rounded-3 w-100"
                                 style="max-height: 400px; object-fit: cover;">
                        </div>

                        <h1 class="detail-title">{{ item.name }}</h1>

                        <!-- Info meta -->
                        <div class="detail-meta row g-3 mb-4">
                            <div class="col-sm-6">
                                <div class="meta-block">
                                    <span class="meta-label"><i class="bi bi-geo-alt me-1"></i>Lokasi</span>
                                    <span class="meta-value">{{ item.location }}</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="meta-block">
                                    <span class="meta-label"><i class="bi bi-calendar3 me-1"></i>Tanggal</span>
                                    <span class="meta-value">{{ formatLongDate(item.date_occurred) }}</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="meta-block">
                                    <span class="meta-label"><i class="bi bi-person me-1"></i>Diposting oleh</span>
                                    <span class="meta-value">{{ item.user?.name }}</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="meta-block">
                                    <span class="meta-label"><i class="bi bi-telephone me-1"></i>Kontak</span>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="meta-value">{{ item.user?.phone || 'Tidak tersedia' }}</span>
                                        <a v-if="item.user?.phone" :href="waLink(item.user.phone, item.name)" target="_blank" class="btn btn-sm btn-success py-0 px-2">
                                            <i class="bi bi-whatsapp me-1"></i>Chat WA
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-4">
                            <h5 class="fw-semibold mb-2">Deskripsi</h5>
                            <p class="text-body" style="white-space: pre-line;">{{ item.description }}</p>
                        </div>

                        <!-- Waktu posting -->
                        <p class="text-muted small">
                            <i class="bi bi-clock me-1"></i>
                            Diposting {{ timeAgo(item.created_at) }}
                            <template v-if="wasEdited"> &middot; Diedit {{ timeAgo(item.updated_at) }}</template>
                        </p>

                        <!-- Aksi (hanya pemilik) -->

                        <!-- Klaim Barang -->
                        <template v-if="authUser && !isOwner && (item.status === 'open' || userClaim)">
                            <hr>
                            <div v-if="userClaim" class="alert mb-0"
                                 :class="{ 'alert-warning': userClaim.status==='pending', 'alert-success': userClaim.status==='approved', 'alert-danger': userClaim.status==='rejected' }">
                                <div class="fw-semibold mb-1">
                                    <i class="bi me-1" :class="{ 'bi-clock': userClaim.status==='pending', 'bi-check-circle-fill': userClaim.status==='approved', 'bi-x-circle-fill': userClaim.status==='rejected' }"></i>
                                    <span v-if="userClaim.status==='pending'">Klaimmu sedang ditinjau admin.</span>
                                    <span v-else-if="userClaim.status==='approved'">Klaim disetujui! Hubungi pemilik untuk serah terima.</span>
                                    <span v-else>Klaim ditolak.</span>
                                </div>
                                <p v-if="userClaim.admin_note" class="small fst-italic mb-2">"{{ userClaim.admin_note }}"</p>
                                <div v-if="userClaim.status==='approved'" class="mt-3 border-top pt-3">
                                    <div v-if="userClaim.handover_photo">
                                        <p class="small fw-semibold mb-1"><i class="bi bi-check2-circle text-success me-1"></i>Dokumentasi serah terima sudah diupload.</p>
                                        <a :href="'/storage/' + userClaim.handover_photo" target="_blank" class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-image me-1"></i>Lihat Foto
                                        </a>
                                    </div>
                                    <div v-else>
                                        <p class="small fw-semibold mb-1"><i class="bi bi-camera me-1"></i>Upload foto dokumentasi serah terima:</p>
                                        <p class="small text-muted mb-2">Foto akan diberi <strong>watermark timestamp</strong> oleh server — tidak bisa dipalsukan.</p>
                                        <input type="file" accept="image/*" capture="environment" class="form-control form-control-sm mb-2" @change="onHandoverChange">
                                        <img v-if="handoverPreview" :src="handoverPreview" class="img-thumbnail mb-2 d-block" style="max-height:140px">
                                        <button type="button" class="btn btn-success btn-sm"
                                                :disabled="!handoverForm.handover_photo || handoverForm.processing"
                                                @click="submitHandover">
                                            <i class="bi bi-upload me-1"></i>Upload Foto Serah Terima
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div v-else>
                                <h6 class="fw-bold mb-3">
                                    <i class="bi bi-hand-index me-2 text-primary"></i>
                                    {{ item.type === 'found' ? 'Ini barangmu? Ajukan Klaim' : 'Kamu menemukan barang ini?' }}
                                </h6>
                                <form @submit.prevent="submitClaim">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Deskripsi Bukti <span class="text-danger">*</span></label>
                                        <textarea v-model="claimForm.description" rows="4" class="form-control"
                                                  :class="{ 'is-invalid': claimForm.errors.description }"
                                                  :placeholder="item.type === 'found' ? 'Ciri-ciri khusus barangmu (warna, merek, tanda khusus, dll).' : 'Di mana dan kapan kamu menemukan barang ini.'">
                                        </textarea>
                                        <div class="form-text">Minimal 20 karakter.</div>
                                        <div v-if="claimForm.errors.description" class="invalid-feedback d-block">{{ claimForm.errors.description }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Foto Bukti <span class="text-muted small">(opsional)</span></label>
                                        <input type="file" accept="image/*" capture="environment" class="form-control" @change="onClaimPhotoChange">
                                        <img v-if="claimPhotoPreview" :src="claimPhotoPreview" class="img-thumbnail mt-2 d-block" style="max-height:140px">
                                    </div>
                                    <button type="submit" class="btn btn-primary" :disabled="claimForm.processing">
                                        <i class="bi bi-send me-2"></i>Ajukan Klaim
                                    </button>
                                </form>
                            </div>
                        </template>

                        <template v-if="isOwner">
                            <hr>
                            <div class="d-flex gap-2 flex-wrap">
                                <Link :href="`/items/${item.id}/edit`" class="btn btn-primary">
                                    <i class="bi bi-pencil me-1"></i>Edit Postingan
                                </Link>
                                <button type="button" class="btn btn-outline-danger" @click="deleteItem">
                                    <i class="bi bi-trash me-1"></i>Hapus
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Komentar -->
                <div class="detail-comments card shadow mt-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Komentar</h5>
                        <small class="text-muted">{{ (item.comments || []).length }} komentar</small>
                    </div>
                    <div class="card-body">
                        <div v-if="!topComments.length" class="text-center text-muted py-4">
                            <i class="bi bi-chat-left-text fs-1"></i>
                            <p class="mt-3 mb-0">Belum ada komentar untuk postingan ini.</p>
                        </div>

                        <div v-else class="list-group list-group-flush">
                            <div v-for="comment in topComments" :key="comment.id" class="list-group-item px-0 py-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>{{ comment.user?.name }}</strong>
                                        <span class="text-muted small">&middot; {{ timeAgo(comment.created_at) }}</span>
                                        <p class="mb-0 mt-2">{{ comment.content }}</p>
                                    </div>
                                    <button v-if="canDelete(comment)" type="button"
                                            class="btn btn-sm btn-outline-danger ms-3" @click="deleteComment(comment)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <!-- Balasan -->
                                <div v-if="comment.replies && comment.replies.length"
                                     class="mt-3 ps-4 border-start border-2 border-light">
                                    <div v-for="reply in comment.replies" :key="reply.id" class="mb-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong>{{ reply.user?.name }}</strong>
                                                <span class="text-muted small">&middot; {{ timeAgo(reply.created_at) }}</span>
                                                <p class="mb-0 mt-2">{{ reply.content }}</p>
                                            </div>
                                            <button v-if="canDelete(reply)" type="button"
                                                    class="btn btn-sm btn-outline-danger ms-3" @click="deleteComment(reply)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form balas (hanya jika login) -->
                                <form v-if="authUser" class="mt-3 ps-4 border-start border-2 border-light"
                                      @submit.prevent="submitReply(comment)">
                                    <div class="mb-3">
                                        <textarea v-model="replyText[comment.id]" rows="2"
                                                  class="form-control form-control-sm"
                                                  placeholder="Balas komentar ini..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-outline-primary"
                                            :disabled="replyProcessing[comment.id] || !(replyText[comment.id] || '').trim()">
                                        <i class="bi bi-reply-fill me-1"></i>Balas
                                    </button>
                                </form>
                            </div>
                        </div>

                        <hr>

                        <!-- Form komentar utama -->
                        <form v-if="authUser" @submit.prevent="submitComment">
                            <div class="mb-3">
                                <label for="content" class="form-label">Tulis komentar</label>
                                <textarea id="content" v-model="commentForm.content" rows="3"
                                          class="form-control" :class="{ 'is-invalid': commentForm.errors.content }"
                                          placeholder="Tinggalkan komentar untuk penemu atau pemilik barang"></textarea>
                                <div v-if="commentForm.errors.content" class="invalid-feedback">
                                    {{ commentForm.errors.content }}
                                </div>
                            </div>
                            <button type="submit" class="btn btn-accent" :disabled="commentForm.processing">
                                <i class="bi bi-send me-1"></i>Kirim Komentar
                            </button>
                        </form>
                        <div v-else class="alert alert-secondary mb-0" role="alert">
                            <strong>Login dulu</strong> untuk meninggalkan komentar.
                            <Link href="/login">Masuk</Link> atau
                            <Link href="/register">daftar</Link> sekarang.
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <Link href="/items" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali ke Beranda
                    </Link>
                </div>

            </div>
        </div>
    </div>
</template>
