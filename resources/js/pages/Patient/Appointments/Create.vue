<script setup lang="ts">
import { ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import PatientLayout from '@/layouts/PatientLayout.vue'

defineOptions({ layout: PatientLayout })

const props = defineProps<{
    doctors: { id: number; name: string; specialization: string }[]
    visitTypes: Record<string, string>
}>()

const form = useForm({
    doctor_id: '',
    appointment_date: '',
    appointment_time: '',
    visit_type: '',
    reason: '',
})

const availableSlots = ref<string[]>([])
const loadingSlots = ref(false)

// Fetch available slots when doctor and date are selected
watch([() => form.doctor_id, () => form.appointment_date], async ([doctorId, date]) => {
    form.appointment_time = ''
    availableSlots.value = []

    if (!doctorId || !date) return

    loadingSlots.value = true
    try {
        const res = await fetch(`/patient/appointments/available-slots?doctor_id=${doctorId}&date=${date}`)
        const data = await res.json()
        availableSlots.value = data.slots
    } catch {
        availableSlots.value = []
    } finally {
        loadingSlots.value = false
    }
})

// Minimum date is today
const today = new Date().toISOString().split('T')[0]

function submit() {
    form.post('/patient/appointments')
}
</script>

<template>
    <div>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Book an Appointment</h1>
            <p class="text-sm text-gray-500">Select a doctor, date, and time slot.</p>
        </div>

        <form @submit.prevent="submit" class="max-w-xl space-y-5">
            <!-- Step 1: Doctor -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Doctor</label>
                <select v-model="form.doctor_id" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="" disabled>Select a doctor</option>
                    <option v-for="doc in doctors" :key="doc.id" :value="doc.id">
                        {{ doc.name }} — {{ doc.specialization }}
                    </option>
                </select>
                <p v-if="form.errors.doctor_id" class="text-red-600 text-xs mt-1">{{ form.errors.doctor_id }}</p>
            </div>

            <!-- Step 2: Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" v-model="form.appointment_date" :min="today" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" />
                <p class="text-xs text-gray-400 mt-1">Monday to Friday only.</p>
                <p v-if="form.errors.appointment_date" class="text-red-600 text-xs mt-1">{{ form.errors.appointment_date }}</p>
            </div>

            <!-- Step 3: Time slot -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Time Slot</label>
                <div v-if="!form.doctor_id || !form.appointment_date" class="text-sm text-gray-400">
                    Select a doctor and date first.
                </div>
                <div v-else-if="loadingSlots" class="text-sm text-gray-400">Loading available slots...</div>
                <div v-else-if="availableSlots.length === 0" class="text-sm text-gray-400">
                    No available slots for this date. Try a different day.
                </div>
                <div v-else class="grid grid-cols-4 gap-2">
                    <button
                        v-for="slot in availableSlots"
                        :key="slot"
                        type="button"
                        @click="form.appointment_time = slot"
                        :class="form.appointment_time === slot
                            ? 'bg-blue-800 text-white border-blue-800'
                            : 'bg-white text-gray-700 border-gray-300 hover:border-blue-400'"
                        class="px-3 py-2 text-sm font-medium rounded-lg border transition-colors"
                    >
                        {{ slot }}
                    </button>
                </div>
                <p v-if="form.errors.appointment_time" class="text-red-600 text-xs mt-1">{{ form.errors.appointment_time }}</p>
            </div>

            <!-- Step 4: Visit type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Visit Type</label>
                <select v-model="form.visit_type" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="" disabled>Select visit type</option>
                    <option v-for="(label, key) in visitTypes" :key="key" :value="key">
                        {{ label }}
                    </option>
                </select>
                <p v-if="form.errors.visit_type" class="text-red-600 text-xs mt-1">{{ form.errors.visit_type }}</p>
            </div>

            <!-- Step 5: Reason (optional) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason (optional)</label>
                <textarea v-model="form.reason" rows="3" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Briefly describe your symptoms or reason for visit..." />
                <p v-if="form.errors.reason" class="text-red-600 text-xs mt-1">{{ form.errors.reason }}</p>
            </div>

            <div class="flex gap-3">
                <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-blue-800 text-white text-sm font-medium rounded-lg hover:bg-blue-900 transition-colors disabled:opacity-50">
                    {{ form.processing ? 'Booking...' : 'Book Appointment' }}
                </button>
                <a href="/patient/appointments" class="px-6 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</template>
