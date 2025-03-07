<template>
    <Head title="Autentificare" />

    <AuthenticationCard>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div class="space-y-2">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="block w-full"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="space-y-2 mt-4">
                <InputLabel for="password" value="Parolă" />
                <div class="relative">
                    <!-- Keep your existing TextInput component -->
                    <TextInput
                        id="password"
                        :type="passwordVisible ? 'text' : 'password'"
                        v-model="form.password"
                        class="mt-1 block w-full"
                        required
                        autocomplete="current-password"
                    />

                    <!-- Add this eye icon button -->
                    <button type="button" @click="togglePasswordVisibility" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg v-if="!passwordVisible" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block mt-4">

            </div>

            <div class="flex items-center justify-between mt-6">
                <label class="flex items-center">
                    <Checkbox
                        id="remember"
                        v-model:checked="form.remember"
                        name="remember"
                    />
                    <span class="ml-2 text-sm text-gray-600">Ține-mă minte</span>
                </label>

                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Intră în cont
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>

<script setup>

import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

import AuthenticationCard from '@/Components/elements/AuthenticationCard.vue'
import Checkbox from '@/Components/elements/Checkbox.vue'
import InputError from '@/Components/elements/InputError.vue'
import InputLabel from '@/Components/elements/InputLabel.vue'
import PrimaryButton from '@/Components/elements/PrimaryButton.vue'
import TextInput from '@/Components/elements/TextInput.vue'

defineProps({
    canResetPassword: Boolean,
    status: String,
})

const passwordVisible = ref(false)
const togglePasswordVisibility = () => {
    passwordVisible.value = !passwordVisible.value
}

const form = useForm({
    email: null,
    password: null,
    remember: false,
})

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    })
}

</script>
