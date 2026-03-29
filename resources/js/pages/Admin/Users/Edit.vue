<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps<{
    editUser: {
        id: number
        name: string
        email: string
        role: string
    }
    roles: string[]
}>()

const form = useForm({
    name: props.editUser.name,
    email: props.editUser.email,
    role: props.editUser.role,
    password: '',
})

function submit() {
    form.put(`/admin/users/${props.editUser.id}`)
}
</script>

<template>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 mb-1">Edit User</h1>
        <p class="text-sm text-gray-500 mb-6">Update user details and role.</p>

        <form @submit.prevent="submit" class="bg-white rounded-xl border border-gray-200 p-6 space-y-5 max-w-xl">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input v-model="form.name" type="text" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500" />
                <p v-if="form.errors.name" class="text-xs text-red-600 mt-1">{{ form.errors.name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input v-model="form.email" type="email" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500" />
                <p v-if="form.errors.email" class="text-xs text-red-600 mt-1">{{ form.errors.email }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select v-model="form.role" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option v-for="role in roles" :key="role" :value="role" class="capitalize">{{ role }}</option>
                </select>
                <p v-if="form.errors.role" class="text-xs text-red-600 mt-1">{{ form.errors.role }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">New Password (leave blank to keep current)</label>
                <input v-model="form.password" type="password" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500" />
                <p v-if="form.errors.password" class="text-xs text-red-600 mt-1">{{ form.errors.password }}</p>
            </div>
            <div class="flex gap-3">
                <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 disabled:opacity-50 transition-colors">
                    {{ form.processing ? 'Saving...' : 'Save Changes' }}
                </button>
                <a href="/admin/users" class="px-5 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">Cancel</a>
            </div>
        </form>
    </div>
</template>
