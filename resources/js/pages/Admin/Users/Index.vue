<script setup lang="ts">
import { router, useForm, usePage } from '@inertiajs/vue3';
import { Snackbar } from '@varlet/ui';
import { computed, ref, watch } from 'vue';
import { route } from 'ziggy-js';
import AppBar from '@/components/AppBar.vue';
import type { PageProps, UserAdmin } from '@/types';

const props = defineProps<{
    users: UserAdmin[];
    roles: string[];
}>();

const page = usePage<PageProps>();

const currentUserId = computed(() => page.props.auth.user?.id);

watch(
    () => page.props.flash?.success,
    (msg) => {
        if (msg) {
Snackbar.success(msg);
}
    },
);

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: 'user',
});

const showPassword = ref(false);

function simpan() {
    form.post(route('admin.users.store'), {
        onSuccess: () => form.reset('name', 'email', 'password'),
    });
}

function hapus(user: UserAdmin) {
    if (user.id === currentUserId.value) {
        Snackbar.warning('Tidak dapat menghapus akun sendiri.');

        return;
    }

    if (window.confirm(`Hapus user "${user.name}"?`)) {
        router.delete(route('admin.users.destroy', { user: user.id }));
    }
}

function roleLabel(roles: string[]) {
    return roles.length ? roles.join(', ') : '-';
}
</script>

<template>
    <div>
        <AppBar title="Kelola Pengguna" show-back />

        <div class="page">
            <var-card :elevation="1" :radius="8" class="form-card" title="Tambah User">
                <var-space direction="column" :size="10">
                    <var-input v-model="form.name" label="Nama" placeholder="Nama lengkap user" :error="!!form.errors.name" :error-message="form.errors.name" />
                    <var-input
                        v-model="form.email"
                        label="Email"
                        type="email"
                        placeholder="contoh@email.com"
                        :error="!!form.errors.email"
                        :error-message="form.errors.email"
                    />
                    <var-input
                        v-model="form.password"
                        label="Password"
                        :type="showPassword ? 'text' : 'password'"
                        placeholder="Minimal 8 karakter"
                        :error="!!form.errors.password"
                        :error-message="form.errors.password"
                    >
                        <template #append-icon>
                            <var-button text size="small" @click="showPassword = !showPassword">
                                <var-icon :name="showPassword ? 'view' : 'view-outline'" />
                            </var-button>
                        </template>
                    </var-input>
                    <var-select
                        v-model="form.role"
                        label="Role"
                        placeholder="Pilih role"
                        :options="props.roles.map((r) => ({ label: r, value: r }))"
                        :error="!!form.errors.role"
                        :error-message="form.errors.role"
                    />
                    <var-button type="primary" block :loading="form.processing" @click="simpan">
                        Simpan User
                    </var-button>
                </var-space>
            </var-card>

            <div class="list-title">Daftar User ({{ props.users.length }})</div>

            <!-- Mobile: kartu -->
            <div class="mobile-only">
                <div v-for="u in props.users" :key="u.id" class="user-card">
                    <div class="user-top">
                        <span class="user-name">{{ u.name }}</span>
                        <var-tag color="#e3f2fd" text-color="#1565c0" round>{{ roleLabel(u.roles.map((r) => r.name)) }}</var-tag>
                    </div>
                    <div class="user-email">{{ u.email }}</div>
                    <div class="user-actions" v-if="u.id !== currentUserId">
                        <var-button type="danger" text @click="hapus(u)">
                            Hapus
                        </var-button>
                    </div>
                </div>
            </div>

            <!-- Desktop: tabel -->
            <div class="desktop-only">
                <var-card :elevation="1" :radius="8">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="u in props.users" :key="u.id">
                                <td>{{ u.name }}</td>
                                <td>{{ u.email }}</td>
                                <td>{{ roleLabel(u.roles.map((r) => r.name)) }}</td>
                                <td>
                                    <var-button
                                        v-if="u.id !== currentUserId"
                                        type="danger"
                                        size="small"
                                        text
                                        @click="hapus(u)"
                                    >
                                        Hapus
                                    </var-button>
                                    <span v-else class="self-tag">Anda</span>
                                </td>
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

.form-card {
    margin-bottom: 20px;
    padding: 4px;
}

.list-title {
    font-size: 15px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 10px;
}

.user-card {
    background: #ffffff;
    border-radius: 10px;
    padding: 14px;
    margin-bottom: 10px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
}

.user-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.user-name {
    font-size: 15px;
    font-weight: 600;
    color: #1c2438;
}

.user-email {
    margin-top: 4px;
    font-size: 13px;
    color: #6b7280;
}

.user-actions {
    margin-top: 8px;
}

.self-tag {
    color: #9ca3af;
    font-size: 13px;
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

@media (min-width: 768px) {
    .mobile-only {
        display: none;
    }

    .desktop-only {
        display: block;
    }
}
</style>
