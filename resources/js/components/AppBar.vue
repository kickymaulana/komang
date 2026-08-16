<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { route } from 'ziggy-js';
import type { PageProps } from '@/types';

const props = defineProps<{
    title: string;
    showBack?: boolean;
}>();

const page = usePage<PageProps>();

const user = computed(() => page.props.auth.user);

function goBack() {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        router.visit(route('dashboard'));
    }
}

function logout() {
    router.post(route('logout'));
}
</script>

<template>
    <var-app-bar round :title="props.title" color="#1e88e5" text-color="#ffffff" class="app-bar">
        <template v-if="props.showBack" #left>
            <var-button text text-color="#ffffff" @click="goBack">
                <var-icon name="arrow-left" :size="22" />
            </var-button>
        </template>
        <template #right>
            <span class="app-bar-user">{{ user?.name ?? '' }}</span>
            <var-button text text-color="#ffffff" @click="logout">
                <var-icon name="power" :size="20" />
            </var-button>
        </template>
    </var-app-bar>
</template>

<style scoped>
.app-bar {
    position: sticky;
    top: 0;
    z-index: 10;
}

.app-bar-user {
    margin-right: 8px;
    font-size: 14px;
}
</style>
