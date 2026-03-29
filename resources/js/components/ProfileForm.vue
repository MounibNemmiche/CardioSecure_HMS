<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import type { Profile } from '@/types'

const props = defineProps<{
    userName: string
    profile: Profile | null
    updateUrl: string
}>()

const form = useForm({
    name: props.userName ?? '',
    phone: props.profile?.phone ?? '',
    date_of_birth: props.profile?.date_of_birth ?? '',
    codice_fiscale: props.profile?.codice_fiscale ?? '',
    address: props.profile?.address ?? '',
    city: props.profile?.city ?? '',
    province: props.profile?.province ?? '',
})

function submit() {
    form.put(props.updateUrl)
}
</script>

<template>
    <form @submit.prevent="submit" class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input v-model="form.name" type="text" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                <p v-if="form.errors.name" class="text-xs text-red-600 mt-1">{{ form.errors.name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input v-model="form.phone" type="tel" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                <p v-if="form.errors.phone" class="text-xs text-red-600 mt-1">{{ form.errors.phone }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                <input v-model="form.date_of_birth" type="date" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                <p v-if="form.errors.date_of_birth" class="text-xs text-red-600 mt-1">{{ form.errors.date_of_birth }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Codice Fiscale</label>
                <input v-model="form.codice_fiscale" type="text" maxlength="16" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm uppercase focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                <p v-if="form.errors.codice_fiscale" class="text-xs text-red-600 mt-1">{{ form.errors.codice_fiscale }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Province</label>
                <input v-model="form.province" type="text" maxlength="2" placeholder="PD" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm uppercase focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                <p v-if="form.errors.province" class="text-xs text-red-600 mt-1">{{ form.errors.province }}</p>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <input v-model="form.address" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                <p v-if="form.errors.address" class="text-xs text-red-600 mt-1">{{ form.errors.address }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                <input v-model="form.city" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                <p v-if="form.errors.city" class="text-xs text-red-600 mt-1">{{ form.errors.city }}</p>
            </div>
        </div>

        <div class="flex justify-end">
            <button
                type="submit"
                :disabled="form.processing"
                class="px-5 py-2 bg-blue-700 text-white text-sm font-medium rounded-lg hover:bg-blue-800 disabled:opacity-50 transition-colors"
            >
                {{ form.processing ? 'Saving...' : 'Save Profile' }}
            </button>
        </div>
    </form>
</template>
