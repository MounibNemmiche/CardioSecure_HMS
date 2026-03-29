<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import PatientLayout from '@/layouts/PatientLayout.vue'

defineOptions({ layout: PatientLayout })

const props = defineProps<{
    insurance: {
        insurance_plan_id: number
        policy_number: string
        holder_name: string | null
        tessera_sanitaria_number: string | null
        notes: string | null
        plan: string | null
    } | null
    plans: { id: number; name: string; code: string }[]
}>()

const form = useForm({
    insurance_plan_id: props.insurance?.insurance_plan_id ?? '',
    policy_number: props.insurance?.policy_number ?? '',
    holder_name: props.insurance?.holder_name ?? '',
    tessera_sanitaria_number: props.insurance?.tessera_sanitaria_number ?? '',
    notes: props.insurance?.notes ?? '',
})

function submit() {
    form.put('/patient/insurance')
}
</script>

<template>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 mb-1">My Insurance</h1>
        <p class="text-sm text-gray-500 mb-6">Manage your insurance information. Sensitive data is encrypted.</p>

        <form @submit.prevent="submit" class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Insurance Plan</label>
                <select v-model="form.insurance_plan_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="" disabled>Select a plan...</option>
                    <option v-for="plan in plans" :key="plan.id" :value="plan.id">{{ plan.name }}</option>
                </select>
                <p v-if="form.errors.insurance_plan_id" class="text-xs text-red-600 mt-1">{{ form.errors.insurance_plan_id }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Policy Number</label>
                    <input v-model="form.policy_number" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    <p v-if="form.errors.policy_number" class="text-xs text-red-600 mt-1">{{ form.errors.policy_number }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tessera Sanitaria Number</label>
                    <input v-model="form.tessera_sanitaria_number" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    <p v-if="form.errors.tessera_sanitaria_number" class="text-xs text-red-600 mt-1">{{ form.errors.tessera_sanitaria_number }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Holder Name (if different from you)</label>
                    <input v-model="form.holder_name" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    <p v-if="form.errors.holder_name" class="text-xs text-red-600 mt-1">{{ form.errors.holder_name }}</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea v-model="form.notes" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-blue-700 text-white text-sm font-medium rounded-lg hover:bg-blue-800 disabled:opacity-50 transition-colors">
                    {{ form.processing ? 'Saving...' : 'Save Insurance' }}
                </button>
            </div>
        </form>
    </div>
</template>
