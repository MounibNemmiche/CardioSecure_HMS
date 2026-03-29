<script setup lang="ts">
import { ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import StaffLayout from '@/layouts/StaffLayout.vue'
import CalendarView from '@/components/CalendarView.vue'
import type { Appointment } from '@/types'

defineOptions({ layout: StaffLayout })

const props = defineProps<{
    appointments: {
        data: Appointment[]
        links: { url: string | null; label: string; active: boolean }[]
    }
    allAppointments: Appointment[]
    doctors: { id: number; name: string }[]
    visitTypes: Record<string, string>
    filters: {
        status?: string
        doctor_id?: string
        date_from?: string
        date_to?: string
    }
}>()

// Filters
const filterStatus = ref(props.filters.status || '')
const filterDoctor = ref(props.filters.doctor_id || '')
const filterDateFrom = ref(props.filters.date_from || '')
const filterDateTo = ref(props.filters.date_to || '')

function applyFilters() {
    const params: Record<string, string> = {}
    if (filterStatus.value) params.status = filterStatus.value
    if (filterDoctor.value) params.doctor_id = filterDoctor.value
    if (filterDateFrom.value) params.date_from = filterDateFrom.value
    if (filterDateTo.value) params.date_to = filterDateTo.value
    router.get('/staff/appointments', params, { preserveState: true })
}

function clearFilters() {
    filterStatus.value = ''
    filterDoctor.value = ''
    filterDateFrom.value = ''
    filterDateTo.value = ''
    router.get('/staff/appointments')
}

// Reschedule modal
const rescheduleTarget = ref<Appointment | null>(null)
const rescheduleSlots = ref<string[]>([])
const loadingSlots = ref(false)
const rescheduleForm = useForm({
    appointment_date: '',
    appointment_time: '',
})

function openReschedule(appt: Appointment) {
    rescheduleTarget.value = appt
    rescheduleForm.appointment_date = ''
    rescheduleForm.appointment_time = ''
    rescheduleSlots.value = []
}

function closeReschedule() {
    rescheduleTarget.value = null
}

async function fetchRescheduleSlots() {
    if (!rescheduleTarget.value || !rescheduleForm.appointment_date) return
    rescheduleForm.appointment_time = ''
    loadingSlots.value = true
    try {
        const res = await fetch(`/staff/appointments/available-slots?doctor_id=${rescheduleTarget.value.doctor_id}&date=${rescheduleForm.appointment_date}&exclude_id=${rescheduleTarget.value.id}`)
        const data = await res.json()
        rescheduleSlots.value = data.slots
    } catch {
        rescheduleSlots.value = []
    } finally {
        loadingSlots.value = false
    }
}

function submitReschedule() {
    if (!rescheduleTarget.value) return
    rescheduleForm.patch(`/staff/appointments/${rescheduleTarget.value.id}/reschedule`, {
        onSuccess: () => closeReschedule(),
    })
}

function cancelAppointment(id: number) {
    if (confirm('Are you sure you want to cancel this appointment?')) {
        router.patch(`/staff/appointments/${id}/cancel`)
    }
}

function markStatus(id: number, status: string) {
    router.patch(`/staff/appointments/${id}/status`, { status })
}

function formatDate(date: string) {
    return new Date(date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })
}

function formatTime(time: string) {
    return time.substring(0, 5)
}

function statusBadge(status: string) {
    const map: Record<string, string> = {
        scheduled: 'bg-blue-100 text-blue-700',
        completed: 'bg-green-100 text-green-700',
        cancelled: 'bg-red-100 text-red-600',
        no_show: 'bg-yellow-100 text-yellow-700',
    }
    return map[status] || 'bg-gray-100 text-gray-500'
}

function statusLabel(status: string) {
    const map: Record<string, string> = {
        scheduled: 'Scheduled',
        completed: 'Completed',
        cancelled: 'Cancelled',
        no_show: 'No Show',
    }
    return map[status] || status
}

const today = new Date().toISOString().split('T')[0]

const view = ref<'list' | 'calendar'>('list')
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Appointments</h1>
                <p class="text-sm text-gray-500">Manage all clinic appointments.</p>
            </div>
            <div class="flex bg-gray-100 rounded-lg p-0.5">
                <button
                    @click="view = 'list'"
                    :class="view === 'list' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700'"
                    class="px-3 py-1.5 text-xs font-medium rounded-md transition-all"
                >
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                    List
                </button>
                <button
                    @click="view = 'calendar'"
                    :class="view === 'calendar' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700'"
                    class="px-3 py-1.5 text-xs font-medium rounded-md transition-all"
                >
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    Calendar
                </button>
            </div>
        </div>

        <!-- Calendar View -->
        <CalendarView
            v-if="view === 'calendar'"
            :appointments="allAppointments"
            :visit-types="visitTypes"
            :show-patient="true"
            :show-doctor="true"
            accent-color="violet"
        />

        <!-- Filters (list view only) -->
        <template v-if="view === 'list'">
        <!-- Filters -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
            <div class="grid grid-cols-1 sm:grid-cols-5 gap-3 items-end">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select v-model="filterStatus" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">All</option>
                        <option value="scheduled">Scheduled</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="no_show">No Show</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Doctor</label>
                    <select v-model="filterDoctor" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">All</option>
                        <option v-for="doc in doctors" :key="doc.id" :value="doc.id">{{ doc.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                    <input type="date" v-model="filterDateFrom" class="w-full rounded-lg border-gray-300 text-sm" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                    <input type="date" v-model="filterDateTo" class="w-full rounded-lg border-gray-300 text-sm" />
                </div>
                <div class="flex gap-2">
                    <button @click="applyFilters" class="px-4 py-2 bg-violet-700 text-white text-sm font-medium rounded-lg hover:bg-violet-800 transition-colors">Filter</button>
                    <button @click="clearFilters" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">Clear</button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-4 py-3 font-medium text-gray-500">Date</th>
                        <th class="px-4 py-3 font-medium text-gray-500">Time</th>
                        <th class="px-4 py-3 font-medium text-gray-500">Patient</th>
                        <th class="px-4 py-3 font-medium text-gray-500">Doctor</th>
                        <th class="px-4 py-3 font-medium text-gray-500">Type</th>
                        <th class="px-4 py-3 font-medium text-gray-500">Status</th>
                        <th class="px-4 py-3 font-medium text-gray-500 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="appt in appointments.data" :key="appt.id">
                        <td class="px-4 py-3 text-gray-900 font-medium">{{ formatDate(appt.appointment_date) }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ formatTime(appt.appointment_time) }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ appt.patient?.user?.name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ appt.doctor?.user?.name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ visitTypes[appt.visit_type] || appt.visit_type }}</td>
                        <td class="px-4 py-3">
                            <span :class="statusBadge(appt.status)" class="text-xs font-medium px-2 py-1 rounded-full">
                                {{ statusLabel(appt.status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-1">
                            <template v-if="appt.status === 'scheduled'">
                                <button @click="openReschedule(appt)" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Reschedule</button>
                                <button @click="markStatus(appt.id, 'completed')" class="text-green-600 hover:text-green-800 text-xs font-medium">Complete</button>
                                <button @click="markStatus(appt.id, 'no_show')" class="text-yellow-600 hover:text-yellow-800 text-xs font-medium">No Show</button>
                                <button @click="cancelAppointment(appt.id)" class="text-red-600 hover:text-red-800 text-xs font-medium">Cancel</button>
                            </template>
                        </td>
                    </tr>
                    <tr v-if="appointments.data.length === 0">
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">No appointments found.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="appointments.links.length > 3" class="mt-4 flex justify-center gap-1">
            <template v-for="link in appointments.links" :key="link.label">
                <a
                    v-if="link.url"
                    :href="link.url"
                    class="px-3 py-1 text-sm rounded-md border"
                    :class="link.active ? 'bg-violet-700 text-white border-violet-700' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'"
                    v-html="link.label"
                />
                <span v-else class="px-3 py-1 text-sm text-gray-300" v-html="link.label" />
            </template>
        </div>

        </template>

        <!-- Reschedule Modal -->
        <div v-if="rescheduleTarget" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-1">Reschedule Appointment</h2>
                <p class="text-sm text-gray-500 mb-4">
                    {{ rescheduleTarget.patient?.user?.name }} with {{ rescheduleTarget.doctor?.user?.name }}
                </p>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Date</label>
                        <input type="date" v-model="rescheduleForm.appointment_date" :min="today" @change="fetchRescheduleSlots" class="w-full rounded-lg border-gray-300 text-sm" />
                        <p v-if="rescheduleForm.errors.appointment_date" class="text-red-600 text-xs mt-1">{{ rescheduleForm.errors.appointment_date }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Time</label>
                        <div v-if="!rescheduleForm.appointment_date" class="text-sm text-gray-400">Select a date first.</div>
                        <div v-else-if="loadingSlots" class="text-sm text-gray-400">Loading...</div>
                        <div v-else-if="rescheduleSlots.length === 0" class="text-sm text-gray-400">No available slots.</div>
                        <div v-else class="grid grid-cols-4 gap-2">
                            <button
                                v-for="slot in rescheduleSlots"
                                :key="slot"
                                type="button"
                                @click="rescheduleForm.appointment_time = slot"
                                :class="rescheduleForm.appointment_time === slot
                                    ? 'bg-violet-700 text-white border-violet-700'
                                    : 'bg-white text-gray-700 border-gray-300 hover:border-violet-400'"
                                class="px-3 py-2 text-sm font-medium rounded-lg border transition-colors"
                            >
                                {{ slot }}
                            </button>
                        </div>
                        <p v-if="rescheduleForm.errors.appointment_time" class="text-red-600 text-xs mt-1">{{ rescheduleForm.errors.appointment_time }}</p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button @click="closeReschedule" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancel</button>
                    <button @click="submitReschedule" :disabled="rescheduleForm.processing || !rescheduleForm.appointment_time" class="px-4 py-2 bg-violet-700 text-white text-sm font-medium rounded-lg hover:bg-violet-800 disabled:opacity-50">
                        {{ rescheduleForm.processing ? 'Saving...' : 'Reschedule' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
