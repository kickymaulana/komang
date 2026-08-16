<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { route } from 'ziggy-js';
import AppBar from '@/components/AppBar.vue';
import type { Paginated, Pelanggan } from '@/types';

const props = defineProps<{
    results: Paginated<Pelanggan> | null;
    kriteria: string;
    query: string;
    kolomOptions: string[];
}>();

const kolomLabel: Record<string, string> = {
    idpel: 'ID Pelanggan',
    nama: 'Nama',
    alamat: 'Alamat',
    trf: 'Tarif (trf)',
    daya: 'Daya',
    kddk: 'kddk',
};

const kolom = ref(props.kriteria || 'nama');
const q = ref(props.query);

function cari(page = 1) {
    router.get(
        route('pelanggan.index'),
        { kolom: kolom.value, q: q.value, page },
        { preserveState: true, preserveScroll: true, only: ['results'] },
    );
}

function goto(url: string | null) {
    if (!url) {
return;
}

    router.get(
        url,
        {},
        { preserveState: true, preserveScroll: true, only: ['results'] },
    );
}

function detailUrl(item: Pelanggan) {
    return route('pelanggan.show', { master: item.id });
}
</script>

<template>
    <div>
        <AppBar title="Pencarian Pelanggan" show-back />

        <div class="page">
            <var-space direction="column" :size="12" class="search-form">
                <var-select
                    v-model="kolom"
                    label="Kriteria"
                    :options="props.kolomOptions.map((k) => ({ label: kolomLabel[k] ?? k, value: k }))"
                />
                <var-input
                    v-model="q"
                    label="Kata kunci"
                    :inputmode="kolom === 'idpel' ? 'numeric' : 'text'"
                    placeholder="Ketik kata kunci pencarian"
                    clearable
                    @keyup.enter="cari()"
                />
                <var-button type="primary" block size="large" :disabled="q.trim() === ''" @click="cari()">
                    Cari
                </var-button>
            </var-space>

            <div v-if="props.results" class="result-info">
                Menampilkan {{ props.results.from ?? 0 }}–{{ props.results.to ?? 0 }} dari {{ props.results.total }} hasil
            </div>

            <div v-else class="result-info muted">
                Masukkan kata kunci lalu tekan Cari.
            </div>

            <!-- Mobile: kartu -->
            <div v-if="props.results && props.results.data.length" class="mobile-only">
                <div v-for="item in props.results.data" :key="item.id" class="pelanggan-card" @click="router.visit(detailUrl(item))">
                    <div class="card-top">
                        <span class="card-idpel">{{ item.idpel }}</span>
                        <var-tag color="#e3f2fd" text-color="#1565c0" round>{{ item.kddk }}</var-tag>
                    </div>
                    <div class="card-nama">{{ item.nama }}</div>
                    <div class="card-alamat">{{ item.alamat }}</div>
                    <div class="card-meta">
                        <span>trf {{ item.trf }}</span>
                        <span>{{ item.daya }} VA</span>
                    </div>
                </div>
            </div>

            <!-- Desktop: tabel -->
            <div v-if="props.results && props.results.data.length" class="desktop-only">
                <var-card :elevation="1" :radius="8">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>idpel</th>
                                <th>Nama</th>
                                <th>Alamat</th>
                                <th>trf</th>
                                <th>Daya</th>
                                <th>kddk</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="item in props.results.data"
                                :key="item.id"
                                class="table-row"
                                @click="router.visit(detailUrl(item))"
                            >
                                <td>{{ item.idpel }}</td>
                                <td>{{ item.nama }}</td>
                                <td>{{ item.alamat }}</td>
                                <td>{{ item.trf }}</td>
                                <td>{{ item.daya }}</td>
                                <td>{{ item.kddk }}</td>
                            </tr>
                        </tbody>
                    </table>
                </var-card>
            </div>

            <!-- Empty state -->
            <var-empty v-if="props.results && props.results.data.length === 0" description="Tidak ada hasil ditemukan" />

            <!-- Pagination -->
            <div v-if="props.results && props.results.last_page > 1" class="pagination">
                <var-button text @click="goto(props.results.prev_page_url)" :disabled="!props.results.prev_page_url">
                    Sebelumnya
                </var-button>
                <span class="pagination-info">Halaman {{ props.results.current_page }} / {{ props.results.last_page }}</span>
                <var-button text @click="goto(props.results.next_page_url)" :disabled="!props.results.next_page_url">
                    Berikutnya
                </var-button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.page {
    padding: 16px;
    max-width: 900px;
    margin: 0 auto;
}

.search-form {
    margin-bottom: 16px;
}

.result-info {
    margin: 8px 0;
    font-size: 14px;
    color: #374151;
}

.muted {
    color: #9ca3af;
}

.pelanggan-card {
    background: #ffffff;
    border-radius: 10px;
    padding: 14px;
    margin-bottom: 10px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
    cursor: pointer;
}

.card-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
}

.card-idpel {
    font-size: 16px;
    font-weight: 600;
    color: #1565c0;
}

.card-nama {
    font-size: 15px;
    font-weight: 600;
    color: #1c2438;
}

.card-alamat {
    font-size: 13px;
    color: #6b7280;
    margin-top: 2px;
}

.card-meta {
    display: flex;
    gap: 12px;
    margin-top: 8px;
    font-size: 12px;
    color: #6b7280;
}

.desktop-only {
    display: none;
}

.table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.table th,
.table td {
    text-align: left;
    padding: 10px 12px;
    border-bottom: 1px solid #eef0f3;
}

.table th {
    color: #6b7280;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.table-row {
    cursor: pointer;
}

.table-row:hover {
    background: #f5f8ff;
}

.pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin-top: 20px;
}

.pagination-info {
    font-size: 13px;
    color: #374151;
}

@media (min-width: 768px) {
    .mobile-only {
        display: none;
    }

    .desktop-only {
        display: block;
    }
}
</style>
