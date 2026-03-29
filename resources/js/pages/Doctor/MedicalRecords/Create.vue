<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import DoctorLayout from '@/layouts/DoctorLayout.vue'

defineOptions({ layout: DoctorLayout })

const props = defineProps<{
    patient: { id: number; name: string }
    visitTypes: Record<string, string>
    symptomOptions: Record<string, string>
}>()

const form = useForm({
    visit_date: new Date().toISOString().split('T')[0],
    visit_type: '',
    symptoms: [] as string[],
    bp_systolic: null as number | null,
    bp_diastolic: null as number | null,
    heart_rate: null as number | null,
    oxygen_saturation: null as number | null,
    diagnosis: '',
    notes: '',
    recommendations: '',
    follow_up_date: '',
    files: [] as File[],
    file_descriptions: [] as string[],
})

function toggleSymptom(key: string) {
    const idx = form.symptoms.indexOf(key)
    if (idx >= 0) {
        form.symptoms.splice(idx, 1)
    } else {
        form.symptoms.push(key)
    }
}

function addFiles(event: Event) {
    const target = event.target as HTMLInputElement
    if (target.files) {
        for (const file of Array.from(target.files)) {
            form.files.push(file)
            form.file_descriptions.push('')
        }
    }
    target.value = ''
}

function removeFile(index: number) {
    form.files.splice(index, 1)
    form.file_descriptions.splice(index, 1)
}

function formatSize(bytes: number) {
    if (bytes < 1024) return bytes + ' B'
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB'
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB'
}

function submit() {
    form.post(`/doctor/patients/${props.patient.id}/records`, {
        forceFormData: true,
    })
}
</script>

<template>
    <div>
        <div class="mb-6">
            <a :href="`/doctor/patients/${patient.id}/records`" class="text-sm text-gray-400 hover:text-gray-600">&larr; Back to Records</a>
            <h1 class="text-2xl font-bold text-gray-900 mt-1">New Medical Record</h1>
            <p class="text-sm text-gray-500">Patient: <strong>{{ patient.name }}</strong></p>
        </div>

        <form @submit.prevent="submit" class="bg-white rounded-xl border border-gray-200 p-6 space-y-6">
            <!-- Visit Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Visit Date *</label>
                    <input v-model="form.visit_date" type="date" required class="w-full rounded-lg border-gray-300 text-sm" />
                    <p v-if="form.errors.visit_date" class="text-xs text-red-600 mt-1">{{ form.errors.visit_date }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Visit Type *</label>
                    <select v-model="form.visit_type" required class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="" disabled>Select type...</option>
                        <option v-for="(label, key) in visitTypes" :key="key" :value="key">{{ label }}</option>
                    </select>
                    <p v-if="form.errors.visit_type" class="text-xs text-red-600 mt-1">{{ form.errors.visit_type }}</p>
                </div>
            </div>

            <!-- Symptoms -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Symptoms</label>
                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="(label, key) in symptomOptions"
                        :key="key"
                        type="button"
                        @click="toggleSymptom(key)"
                        :class="form.symptoms.includes(key)
                            ? 'bg-emerald-700 text-white border-emerald-700'
                            : 'bg-white text-gray-600 border-gray-300 hover:border-emerald-400'"
                        class="px-3 py-1.5 text-sm rounded-lg border transition-colors"
                    >
                        {{ label }}
                    </button>
                </div>
            </div>

            <!-- Vitals -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Vitals</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Systolic (mmHg)</label>
                        <input v-model.number="form.bp_systolic" type="number" min="50" max="300" placeholder="120" class="w-full rounded-lg border-gray-300 text-sm" />
                        <p v-if="form.errors.bp_systolic" class="text-xs text-red-600 mt-1">{{ form.errors.bp_systolic }}</p>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Diastolic (mmHg)</label>
                        <input v-model.number="form.bp_diastolic" type="number" min="30" max="200" placeholder="80" class="w-full rounded-lg border-gray-300 text-sm" />
                        <p v-if="form.errors.bp_diastolic" class="text-xs text-red-600 mt-1">{{ form.errors.bp_diastolic }}</p>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Heart Rate (bpm)</label>
                        <input v-model.number="form.heart_rate" type="number" min="20" max="300" placeholder="72" class="w-full rounded-lg border-gray-300 text-sm" />
                        <p v-if="form.errors.heart_rate" class="text-xs text-red-600 mt-1">{{ form.errors.heart_rate }}</p>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">SpO2 (%)</label>
                        <input v-model.number="form.oxygen_saturation" type="number" min="50" max="100" placeholder="98" class="w-full rounded-lg border-gray-300 text-sm" />
                        <p v-if="form.errors.oxygen_saturation" class="text-xs text-red-600 mt-1">{{ form.errors.oxygen_saturation }}</p>
                    </div>
                </div>
            </div>

            <!-- Clinical Notes -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Diagnosis</label>
                    <textarea v-model="form.diagnosis" rows="2" class="w-full rounded-lg border-gray-300 text-sm" placeholder="Clinical diagnosis..."></textarea>
                    <p v-if="form.errors.diagnosis" class="text-xs text-red-600 mt-1">{{ form.errors.diagnosis }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea v-model="form.notes" rows="3" class="w-full rounded-lg border-gray-300 text-sm" placeholder="Clinical notes..."></textarea>
                    <p v-if="form.errors.notes" class="text-xs text-red-600 mt-1">{{ form.errors.notes }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Recommendations</label>
                    <textarea v-model="form.recommendations" rows="2" class="w-full rounded-lg border-gray-300 text-sm" placeholder="Treatment recommendations..."></textarea>
                    <p v-if="form.errors.recommendations" class="text-xs text-red-600 mt-1">{{ form.errors.recommendations }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Follow-up Date</label>
                    <input v-model="form.follow_up_date" type="date" class="w-full sm:w-64 rounded-lg border-gray-300 text-sm" />
                    <p v-if="form.errors.follow_up_date" class="text-xs text-red-600 mt-1">{{ form.errors.follow_up_date }}</p>
                </div>
            </div>

            <!-- File Attachments -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Attachments</label>
                <p class="text-xs text-gray-400 mb-2">PDF, JPG, PNG — max 10 MB each, up to 5 files.</p>

                <div v-if="form.files.length" class="space-y-2 mb-3">
                    <div v-for="(file, idx) in form.files" :key="idx" class="flex items-center gap-3 bg-gray-50 rounded-lg p-3">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-700 truncate">{{ file.name }} <span class="text-gray-400">({{ formatSize(file.size) }})</span></p>
                            <input v-model="form.file_descriptions[idx]" type="text" placeholder="Description (optional)" class="mt-1 w-full rounded border-gray-300 text-xs" />
                        </div>
                        <button type="button" @click="removeFile(idx)" class="text-red-500 hover:text-red-700 text-xs font-medium shrink-0">Remove</button>
                    </div>
                </div>

                <label v-if="form.files.length < 5" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 cursor-pointer transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Add File
                    <input type="file" accept=".pdf,.jpg,.jpeg,.png" multiple @change="addFiles" class="hidden" />
                </label>
            </div>

            <!-- Submit -->
            <div class="flex justify-end pt-2">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-6 py-2.5 bg-emerald-700 text-white text-sm font-medium rounded-lg hover:bg-emerald-800 disabled:opacity-50 transition-colors"
                >
                    {{ form.processing ? 'Saving...' : 'Create Record' }}
                </button>
            </div>
        </form>
    </div>
</template>
