<script setup lang="ts">
import PatientLayout from '@/layouts/PatientLayout.vue'
import type { MedicalRecord } from '@/types'

defineOptions({ layout: PatientLayout })

const props = defineProps<{
    records: MedicalRecord[]
    visitTypes: Record<string, string>
    symptomOptions: Record<string, string>
}>()

function formatDate(date: string) {
    return new Date(date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })
}
</script>

<template>
    <div>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">My Medical Records</h1>
            <p class="text-sm text-gray-500">View your cardiology records and test results.</p>
        </div>

        <div v-if="records.length === 0" class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-400">
            No medical records yet.
        </div>

        <div v-else class="space-y-4">
            <a v-for="record in records" :key="record.id"
               :href="`/patient/records/${record.id}`"
               class="block bg-white rounded-xl border border-gray-200 p-5 hover:border-blue-300 transition-colors">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-sm font-bold text-gray-900">{{ formatDate(record.visit_date) }}</span>
                            <span class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full font-medium">
                                {{ visitTypes[record.visit_type] || record.visit_type }}
                            </span>
                            <span v-if="record.files.length" class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full font-medium">
                                {{ record.files.length }} file{{ record.files.length > 1 ? 's' : '' }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Dr. {{ record.doctor?.user?.name }}</p>
                        <p v-if="record.diagnosis" class="text-sm text-gray-700">{{ record.diagnosis }}</p>
                        <div v-if="record.bp_systolic" class="flex gap-4 text-xs text-gray-500 mt-1">
                            <span>BP: {{ record.bp_systolic }}/{{ record.bp_diastolic }} mmHg</span>
                            <span v-if="record.heart_rate">HR: {{ record.heart_rate }} bpm</span>
                            <span v-if="record.oxygen_saturation">SpO2: {{ record.oxygen_saturation }}%</span>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </div>
            </a>
        </div>
    </div>
</template>
