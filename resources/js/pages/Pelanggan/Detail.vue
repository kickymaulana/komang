<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppBar from '@/components/AppBar.vue';
import type { Pelanggan } from '@/types';

const props = defineProps<{
    pelanggan: Pelanggan;
}>();

const kddkUrl = route('kddk.show', { kode: props.pelanggan.kddk }) + '#' + props.pelanggan.id;

const fields = [
    { label: 'ID Pelanggan', value: props.pelanggan.idpel, icon: 'tag' },
    { label: 'Nama', value: props.pelanggan.nama, icon: 'account-circle' },
    { label: 'Alamat', value: props.pelanggan.alamat, icon: 'map-marker' },
    { label: 'Tarif (trf)', value: props.pelanggan.trf, icon: 'information' },
    { label: 'Daya', value: props.pelanggan.daya, icon: 'weather-lightning' },
    { label: 'kddk', value: props.pelanggan.kddk, icon: 'file-document-outline' },
];
</script>

<template>
    <div>
        <AppBar title="Detail Pelanggan" show-back />

        <div class="page">
            <div class="pelanggan-header">
                <div class="pelanggan-nama">{{ props.pelanggan.nama }}</div>
                <div class="pelanggan-idpel">{{ props.pelanggan.idpel }}</div>
            </div>

            <var-cell-group inset :elevation="1" class="info-group">
                <var-cell v-for="f in fields" :key="f.label" :title="f.label" :description="f.value" :icon="f.icon" />
            </var-cell-group>

            <div class="actions">
                <Link :href="kddkUrl" class="link-block">
                    <var-button type="primary" block size="large">
                        Lihat List kddk
                    </var-button>
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

.pelanggan-header {
    margin-bottom: 16px;
}

.pelanggan-nama {
    font-size: 20px;
    font-weight: 700;
    color: #1c2438;
}

.pelanggan-idpel {
    margin-top: 4px;
    color: #1565c0;
    font-size: 14px;
    font-weight: 600;
}

.actions {
    margin-top: 24px;
}

.link-block {
    text-decoration: none;
}
</style>
