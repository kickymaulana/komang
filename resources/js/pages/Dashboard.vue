<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { route } from 'ziggy-js';
import AppBar from '@/components/AppBar.vue';
import type { PageProps } from '@/types';

const page = usePage<PageProps>();

const isAdmin = computed(() => (page.props.auth.roles ?? []).includes('admin'));

const q = ref('');
const kolom = ref('nama');

const kolomOptions = [
    { label: 'ID Pelanggan', value: 'idpel' },
    { label: 'Nama', value: 'nama' },
    { label: 'Alamat', value: 'alamat' },
    { label: 'Tarif (trf)', value: 'trf' },
    { label: 'Daya', value: 'daya' },
    { label: 'kddk', value: 'kddk' },
];

function cari() {
    router.get(route('pelanggan.index'), { kolom: kolom.value, q: q.value });
}
</script>

<template>
    <div>
        <AppBar title="Komang" />

        <div class="page">
            <div class="hero">
                <h2 class="hero-title">Cari Pelanggan</h2>
                <p class="hero-sub">Temukan pelanggan PLN berdasarkan idpel, nama, alamat, trf, daya, atau kddk.</p>
            </div>

            <var-space direction="column" :size="12">
                <var-select v-model="kolom" label="Kriteria" :options="kolomOptions" />
                <var-input
                    v-model="q"
                    label="Kata kunci"
                    :inputmode="kolom === 'idpel' ? 'numeric' : 'text'"
                    placeholder="Ketik kata kunci pencarian"
                    clearable
                    @keyup.enter="cari"
                />
                <var-button type="primary" block size="large" :disabled="q.trim() === ''" @click="cari">
                    Cari
                </var-button>
            </var-space>

            <div class="admin-menu" v-if="isAdmin">
                <Link :href="route('admin.users.index')" class="link-block">
                    <var-cell title="Kelola Pengguna" description="Tambah / hapus user (khusus admin)" is-link />
                </Link>
            </div>
        </div>
    </div>
</template>

<style scoped>
.page {
    padding: 16px;
    max-width: 640px;
    margin: 0 auto;
}

.hero {
    margin-bottom: 16px;
}

.hero-title {
    margin: 0;
    font-size: 22px;
}

.hero-sub {
    margin: 6px 0 0;
    color: #6b7280;
    font-size: 14px;
}

.admin-menu {
    margin-top: 24px;
}

.link-block {
    text-decoration: none;
    color: inherit;
}
</style>
