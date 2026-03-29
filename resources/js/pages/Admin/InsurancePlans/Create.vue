<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const form = useForm({
    name: '',
    code: '',
    description: '',
    is_active: true,
})

function submit() {
    form.post('/admin/insurance-plans')
}
</script>

<template>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 mb-1">Add Insurance Plan</h1>
        <p class="text-sm text-gray-500 mb-6">Create a new insurance plan for the master list.</p>

        <form @submit.prevent="submit" class="bg-white rounded-xl border border-gray-200 p-6 space-y-5 max-w-xl">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Plan Name</label>
                <input v-model="form.name" type="text" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500" />
                <p v-if="form.errors.name" class="text-xs text-red-600 mt-1">{{ form.errors.name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                <input v-model="form.code" type="text" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm lowercase focus:ring-2 focus:ring-red-500 focus:border-red-500" />
                <p v-if="form.errors.code" class="text-xs text-red-600 mt-1">{{ form.errors.code }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea v-model="form.description" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
            </div>
            <div class="flex items-center gap-2">
                <input v-model="form.is_active" type="checkbox" id="is_active" class="rounded border-gray-300 text-red-600 focus:ring-red-500" />
                <label for="is_active" class="text-sm text-gray-700">Active</label>
            </div>
            <div class="flex gap-3">
                <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 disabled:opacity-50 transition-colors">
                    {{ form.processing ? 'Creating...' : 'Create Plan' }}
                </button>
                <a href="/admin/insurance-plans" class="px-5 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">Cancel</a>
            </div>
        </form>
    </div>
</template>
