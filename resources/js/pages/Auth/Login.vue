<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { route } from 'ziggy-js';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

function submit() {
    form.post(route('login.store'), {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <div class="login-wrap">
        <div class="login-card">
            <div class="login-brand">
                <var-icon name="weather-lightning" :size="40" color="#1e88e5" />
                <h1 class="login-title">Komang</h1>
                <p class="login-subtitle">Pencarian Pelanggan PLN</p>
            </div>

            <var-form @submit="submit">
                <var-input
                    v-model="form.email"
                    label="Email"
                    type="text"
                    placeholder="contoh@email.com"
                    autocomplete="email"
                    :error="!!form.errors.email"
                    :error-message="form.errors.email"
                />
                <var-input
                    v-model="form.password"
                    label="Password"
                    :type="showPassword ? 'text' : 'password'"
                    placeholder="Masukkan password"
                    autocomplete="current-password"
                    :error="!!form.errors.password"
                    :error-message="form.errors.password"
                >
                    <template #append-icon>
                        <var-button text size="small" @click="showPassword = !showPassword">
                            <var-icon :name="showPassword ? 'view' : 'view-outline'" />
                        </var-button>
                    </template>
                </var-input>

                <div class="login-options">
                    <var-checkbox v-model="form.remember">Ingat saya</var-checkbox>
                </div>

                <var-button
                    type="primary"
                    block
                    size="large"
                    class="login-submit"
                    :loading="form.processing"
                    @click="submit"
                >
                    Masuk
                </var-button>
            </var-form>
        </div>
    </div>
</template>

<style scoped>
.login-wrap {
    min-height: 100dvh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    box-sizing: border-box;
}

.login-card {
    width: 100%;
    max-width: 400px;
    background: #ffffff;
    border-radius: 12px;
    padding: 28px 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    box-sizing: border-box;
}

.login-brand {
    text-align: center;
    margin-bottom: 24px;
}

.login-title {
    margin: 8px 0 0;
    font-size: 24px;
    color: #1c2438;
}

.login-subtitle {
    margin: 4px 0 0;
    color: #6b7280;
    font-size: 14px;
}

.login-options {
    margin-top: 4px;
    margin-bottom: 12px;
}

.login-submit {
    margin-top: 8px;
}
</style>
