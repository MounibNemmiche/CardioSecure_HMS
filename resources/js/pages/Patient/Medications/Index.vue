<script setup lang="ts">
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import PatientLayout from '@/layouts/PatientLayout.vue'
import type { MedicationReminder } from '@/types'

defineOptions({ layout: PatientLayout })

const props = defineProps<{
    reminders: MedicationReminder[]
}>()

const showForm = ref(false)
const editingId = ref<number | null>(null)
const confirmDeleteId = ref<number | null>(null)

const form = useForm({
    medication_name: '',
    dosage: '',
    reminder_time: '08:00',
    notes: '',
})

function openNew() {
    editingId.value = null
    confirmDeleteId.value = null
    form.reset()
    form.reminder_time = '08:00'
    showForm.value = true
}

function openEdit(r: MedicationReminder) {
    editingId.value = r.id
    confirmDeleteId.value = null
    showForm.value = false
    form.medication_name = r.medication_name
    form.dosage = r.dosage
    form.reminder_time = r.reminder_time.substring(0, 5)
    form.notes = r.notes ?? ''
    showForm.value = true
}

function cancelForm() {
    showForm.value = false
    editingId.value = null
    form.reset()
}

function submit() {
    if (editingId.value) {
        form.put(`/patient/medications/${editingId.value}`, {
            onSuccess: () => { showForm.value = false; editingId.value = null; form.reset() },
        })
    } else {
        form.post('/patient/medications', {
            onSuccess: () => { showForm.value = false; form.reset() },
        })
    }
}

function toggleActive(r: MedicationReminder) {
    router.patch(`/patient/medications/${r.id}/toggle`, {}, { preserveScroll: true })
}

function askDelete(r: MedicationReminder) {
    confirmDeleteId.value = r.id
}

function confirmDelete(r: MedicationReminder) {
    confirmDeleteId.value = null
    router.delete(`/patient/medications/${r.id}`, { preserveScroll: true })
}

function cancelDelete() {
    confirmDeleteId.value = null
}

function formatTime(time: string) {
    return time.substring(0, 5)
}
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">My Medications</h1>
                <p class="text-sm text-gray-500 mt-1">Manage your medication reminders.</p>
            </div>
            <button
                class="px-4 py-2 bg-blue-800 text-white text-sm font-medium rounded-lg hover:bg-blue-900 transition-colors"
                @click="openNew"
            >
                + Add Medication
            </button>
        </div>

        <!-- Add/Edit Form -->
        <div v-if="showForm" class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                {{ editingId ? 'Edit Medication' : 'New Medication Reminder' }}
            </h2>
            <form @submit.prevent="submit" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Medication Name *</label>
                    <input
                        v-model="form.medication_name"
                        type="text"
                        placeholder="e.g. Ramipril"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    />
                    <p v-if="form.errors.medication_name" class="text-red-500 text-xs mt-1">{{ form.errors.medication_name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dosage *</label>
                    <input
                        v-model="form.dosage"
                        type="text"
                        placeholder="e.g. 5mg"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    />
                    <p v-if="form.errors.dosage" class="text-red-500 text-xs mt-1">{{ form.errors.dosage }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reminder Time *</label>
                    <input
                        v-model="form.reminder_time"
                        type="time"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    />
                    <p v-if="form.errors.reminder_time" class="text-red-500 text-xs mt-1">{{ form.errors.reminder_time }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <input
                        v-model="form.notes"
                        type="text"
                        placeholder="Take with food..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    />
                </div>
                <div class="sm:col-span-2 flex gap-3 justify-end">
                    <button
                        type="button"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900"
                        @click="cancelForm"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-4 py-2 bg-blue-800 text-white text-sm font-medium rounded-lg hover:bg-blue-900 disabled:opacity-50"
                    >
                        {{ form.processing ? 'Saving...' : (editingId ? 'Update' : 'Add Reminder') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Reminders List -->
        <div v-if="reminders.length === 0 && !showForm" class="text-center py-16 bg-white border border-gray-200 rounded-xl">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg>
            <p class="text-gray-500 text-sm">No medication reminders set.</p>
            <button class="mt-3 text-sm text-blue-700 hover:text-blue-800 font-medium" @click="openNew">
                Add your first medication
            </button>
        </div>

        <div v-else class="space-y-3">
            <div
                v-for="r in reminders"
                :key="r.id"
                class="bg-white border border-gray-200 rounded-xl p-4 transition-opacity"
                :class="{ 'opacity-60': !r.is_active }"
            >
                <!-- Confirm delete row -->
                <div v-if="confirmDeleteId === r.id" class="flex items-center justify-between gap-3">
                    <p class="text-sm text-gray-700">Delete <strong>{{ r.medication_name }}</strong>?</p>
                    <div class="flex gap-2">
                        <button
                            class="px-3 py-1.5 text-xs font-medium text-gray-600 hover:text-gray-900 border border-gray-300 rounded-lg transition-colors"
                            @click="cancelDelete"
                        >Cancel</button>
                        <button
                            class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors"
                            @click="confirmDelete(r)"
                        >Delete</button>
                    </div>
                </div>

                <!-- Normal row -->
                <div v-else class="flex items-center gap-4">
                    <!-- Time badge -->
                    <div class="shrink-0 w-16 h-16 bg-blue-50 rounded-lg flex flex-col items-center justify-center">
                        <span class="text-lg font-bold text-blue-800">{{ formatTime(r.reminder_time) }}</span>
                    </div>

                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <h3 class="font-semibold text-gray-900">{{ r.medication_name }}</h3>
                            <span class="text-xs px-2 py-0.5 rounded-full" :class="r.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'">
                                {{ r.is_active ? 'Active' : 'Paused' }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600">{{ r.dosage }}</p>
                        <p v-if="r.notes" class="text-xs text-gray-400 mt-0.5">{{ r.notes }}</p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 shrink-0">
                        <button
                            class="p-2 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors"
                            :title="r.is_active ? 'Pause' : 'Activate'"
                            @click="toggleActive(r)"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path v-if="r.is_active" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            </svg>
                        </button>
                        <button
                            class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-50 transition-colors"
                            title="Edit"
                            @click="openEdit(r)"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button
                            class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors"
                            title="Delete"
                            @click="askDelete(r)"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
