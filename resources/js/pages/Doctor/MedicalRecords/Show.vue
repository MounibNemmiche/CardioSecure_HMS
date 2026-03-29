<script setup lang="ts">
import DoctorLayout from '@/layouts/DoctorLayout.vue'
import type { MedicalRecord } from '@/types'

defineOptions({ layout: DoctorLayout })

const props = defineProps<{
    patient: { id: number; name: string }
    record: MedicalRecord
    visitTypes: Record<string, string>
    symptomOptions: Record<string, string>
}>()

function formatDate(date: string) {
    return new Date(date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })
}

function formatSize(bytes: number) {
    if (bytes < 1024) return bytes + ' B'
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB'
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB'
}
</script>

<template>
    <div>
        <div class="mb-6">
            <a :href="`/doctor/patients/${patient.id}/records`" class="text-sm text-gray-400 hover:text-gray-600">&larr; Back to Records</a>
            <h1 class="text-2xl font-bold text-gray-900 mt-1">Medical Record</h1>
            <p class="text-sm text-gray-500">Patient: <strong>{{ patient.name }}</strong> &middot; {{ formatDate(record.visit_date) }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-6">
            <!-- Visit Info -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase">Visit Date</p>
                    <p class="text-sm text-gray-900 font-medium">{{ formatDate(record.visit_date) }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase">Visit Type</p>
                    <p class="text-sm text-gray-900">{{ visitTypes[record.visit_type] || record.visit_type }}</p>
                </div>
                <div v-if="record.follow_up_date">
                    <p class="text-xs font-medium text-gray-400 uppercase">Follow-up</p>
                    <p class="text-sm text-gray-900">{{ formatDate(record.follow_up_date) }}</p>
                </div>
            </div>

            <!-- Vitals -->
            <div v-if="record.bp_systolic || record.heart_rate || record.oxygen_saturation">
                <p class="text-xs font-medium text-gray-400 uppercase mb-2">Vitals</p>
                <div class="flex flex-wrap gap-4">
                    <div v-if="record.bp_systolic" class="bg-gray-50 rounded-lg px-4 py-3 text-center">
                        <p class="text-lg font-bold text-gray-900">{{ record.bp_systolic }}/{{ record.bp_diastolic }}</p>
                        <p class="text-xs text-gray-500">mmHg</p>
                    </div>
                    <div v-if="record.heart_rate" class="bg-gray-50 rounded-lg px-4 py-3 text-center">
                        <p class="text-lg font-bold text-gray-900">{{ record.heart_rate }}</p>
                        <p class="text-xs text-gray-500">bpm</p>
                    </div>
                    <div v-if="record.oxygen_saturation" class="bg-gray-50 rounded-lg px-4 py-3 text-center">
                        <p class="text-lg font-bold text-gray-900">{{ record.oxygen_saturation }}%</p>
                        <p class="text-xs text-gray-500">SpO2</p>
                    </div>
                </div>
            </div>

            <!-- Symptoms -->
            <div v-if="record.symptoms && record.symptoms.length">
                <p class="text-xs font-medium text-gray-400 uppercase mb-2">Symptoms</p>
                <div class="flex flex-wrap gap-2">
                    <span v-for="s in record.symptoms" :key="s" class="text-xs bg-amber-50 text-amber-700 px-2 py-1 rounded-full font-medium">
                        {{ symptomOptions[s] || s }}
                    </span>
                </div>
            </div>

            <!-- Diagnosis -->
            <div v-if="record.diagnosis">
                <p class="text-xs font-medium text-gray-400 uppercase mb-1">Diagnosis</p>
                <p class="text-sm text-gray-900">{{ record.diagnosis }}</p>
            </div>

            <!-- Notes -->
            <div v-if="record.notes">
                <p class="text-xs font-medium text-gray-400 uppercase mb-1">Notes</p>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ record.notes }}</p>
            </div>

            <!-- Recommendations -->
            <div v-if="record.recommendations">
                <p class="text-xs font-medium text-gray-400 uppercase mb-1">Recommendations</p>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ record.recommendations }}</p>
            </div>

            <!-- Files -->
            <div v-if="record.files.length">
                <p class="text-xs font-medium text-gray-400 uppercase mb-2">Attachments</p>
                <div class="space-y-2">
                    <a v-for="file in record.files" :key="file.id"
                       :href="`/files/${file.id}/download`"
                       class="flex items-center gap-3 bg-gray-50 rounded-lg p-3 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-700 truncate">{{ file.file_name }}</p>
                            <p class="text-xs text-gray-400">{{ formatSize(file.file_size) }}{{ file.description ? ' — ' + file.description : '' }}</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>
