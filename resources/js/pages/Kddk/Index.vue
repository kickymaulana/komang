<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { nextTick, onMounted, ref } from 'vue';
import { route } from 'ziggy-js';
import AppBar from '@/components/AppBar.vue';
import type { Pelanggan } from '@/types';

const props = defineProps<{
    kode: string;
    prefix: string;
    list: Pelanggan[];
    targetId: number | null;
}>();

const highlighted = ref<number | null>(props.targetId);

function detailUrl(item: Pelanggan) {
    return route('pelanggan.show', { master: item.id });
}

onMounted(async () => {
    await nextTick();

    const hash = window.location.hash;
    const match = hash.match(/^#(\d+)$/);

    if (match) {
        const el = document.getElementById(match[1]);

        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
});
</script>

<template>
    <div>
        <AppBar title="List Kode kddk" show-back />

        <div class="page">
            <div class="header">
                <div class="header-kode">{{ props.kode }}</div>
                <div class="header-sub">Area {{ props.prefix }} · {{ props.list.length }} kode</div>
                <div v-if="props.targetId" class="header-target">Target: pelanggan #{{ props.targetId }} ({{ props.kode }})</div>
            </div>

            <!-- Mobile: kartu -->
            <div class="mobile-only">
                <div
                    v-for="item in props.list"
                    :key="item.id"
                    :id="String(item.id)"
                    class="kddk-card"
                    :class="{ highlight: item.id === highlighted }"
                    @click="router.visit(detailUrl(item))"
                >
                    <div class="card-top">
                        <span class="card-kode">{{ item.kddk }}</span>
                        <var-icon
                            v-if="item.id === highlighted"
                            name="map-marker"
                            color="#1e88e5"
                            :size="18"
                        />
                    </div>
                    <div class="card-idpel">{{ item.idpel }}</div>
                    <div class="card-nama">{{ item.nama }}</div>
                    <div class="card-meta">
                        <span>{{ item.daya }} VA</span>
                        <span>{{ item.trf }}</span>
                    </div>
                </div>
            </div>

            <!-- Desktop: tabel -->
            <div class="desktop-only">
                <var-card :elevation="1" :radius="8">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>kddk</th>
                                <th>idpel</th>
                                <th>Nama</th>
                                <th>Alamat</th>
                                <th>Daya</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="item in props.list"
                                :key="item.id"
                                :id="String(item.id)"
                                class="table-row"
                                :class="{ highlight: item.id === highlighted }"
                                @click="router.visit(detailUrl(item))"
                            >
                                <td>{{ item.kddk }}</td>
                                <td>{{ item.idpel }}</td>
                                <td>{{ item.nama }}</td>
                                <td>{{ item.alamat }}</td>
                                <td>{{ item.daya }}</td>
                            </tr>
                        </tbody>
                    </table>
                </var-card>
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

.header {
    margin-bottom: 16px;
}

.header-kode {
    font-size: 20px;
    font-weight: 700;
    color: #1c2438;
}

.header-sub {
    margin-top: 4px;
    color: #6b7280;
    font-size: 14px;
}

.header-target {
    margin-top: 6px;
    color: #1565c0;
    font-size: 13px;
    font-weight: 600;
}

.kddk-card {
    background: #ffffff;
    border-radius: 10px;
    padding: 14px;
    margin-bottom: 10px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
    cursor: pointer;
    border-left: 3px solid transparent;
}

.kddk-card.highlight {
    border-left-color: #1e88e5;
    background: #f0f7ff;
    box-shadow: 0 2px 10px rgba(30, 136, 229, 0.25);
}

.card-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 4px;
}

.card-kode {
    font-size: 15px;
    font-weight: 700;
    color: #1565c0;
}

.card-idpel {
    font-size: 13px;
    color: #374151;
}

.card-nama {
    font-size: 15px;
    font-weight: 600;
    color: #1c2438;
}

.card-meta {
    display: flex;
    gap: 12px;
    margin-top: 6px;
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

.table-row.highlight {
    background: #f0f7ff;
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
