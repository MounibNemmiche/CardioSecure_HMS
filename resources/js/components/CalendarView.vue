<script setup lang="ts">
import { ref, computed } from 'vue'
import type { Appointment } from '@/types'

const props = defineProps<{
    appointments: Appointment[]
    visitTypes: Record<string, string>
    showPatient?: boolean
    showDoctor?: boolean
    accentColor?: string
}>()

const emit = defineEmits<{
    (e: 'select-date', date: string): void
}>()

const accent = computed(() => props.accentColor || 'blue')

const today = new Date()
const currentMonth = ref(today.getMonth())
const currentYear = ref(today.getFullYear())

const monthNames = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December',
]

const dayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']

function prevMonth() {
    if (currentMonth.value === 0) {
        currentMonth.value = 11
        currentYear.value--
    } else {
        currentMonth.value--
    }
}

function nextMonth() {
    if (currentMonth.value === 11) {
        currentMonth.value = 0
        currentYear.value++
    } else {
        currentMonth.value++
    }
}

function goToToday() {
    currentMonth.value = today.getMonth()
    currentYear.value = today.getFullYear()
}

// Build calendar grid (6 weeks max)
const calendarDays = computed(() => {
    const year = currentYear.value
    const month = currentMonth.value

    // First day of month (0=Sun, convert to Mon=0)
    const firstDay = new Date(year, month, 1).getDay()
    const startOffset = firstDay === 0 ? 6 : firstDay - 1 // Mon-based

    const daysInMonth = new Date(year, month + 1, 0).getDate()
    const daysInPrevMonth = new Date(year, month, 0).getDate()

    const days: { date: number; month: 'prev' | 'current' | 'next'; fullDate: string; isToday: boolean; isWeekend: boolean }[] = []

    // Previous month trailing days
    for (let i = startOffset - 1; i >= 0; i--) {
        const d = daysInPrevMonth - i
        const m = month === 0 ? 11 : month - 1
        const y = month === 0 ? year - 1 : year
        const dateStr = `${y}-${String(m + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`
        const dayOfWeek = new Date(y, m, d).getDay()
        days.push({ date: d, month: 'prev', fullDate: dateStr, isToday: false, isWeekend: dayOfWeek === 0 || dayOfWeek === 6 })
    }

    // Current month days
    for (let d = 1; d <= daysInMonth; d++) {
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`
        const isToday = d === today.getDate() && month === today.getMonth() && year === today.getFullYear()
        const dayOfWeek = new Date(year, month, d).getDay()
        days.push({ date: d, month: 'current', fullDate: dateStr, isToday, isWeekend: dayOfWeek === 0 || dayOfWeek === 6 })
    }

    // Next month leading days (fill to 42 = 6 rows)
    const remaining = 42 - days.length
    for (let d = 1; d <= remaining; d++) {
        const m = month === 11 ? 0 : month + 1
        const y = month === 11 ? year + 1 : year
        const dateStr = `${y}-${String(m + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`
        const dayOfWeek = new Date(y, m, d).getDay()
        days.push({ date: d, month: 'next', fullDate: dateStr, isToday: false, isWeekend: dayOfWeek === 0 || dayOfWeek === 6 })
    }

    return days
})

// Group appointments by date (YYYY-MM-DD)
const appointmentsByDate = computed(() => {
    const map: Record<string, Appointment[]> = {}
    for (const appt of props.appointments) {
        const dateKey = appt.appointment_date.substring(0, 10)
        if (!map[dateKey]) map[dateKey] = []
        map[dateKey].push(appt)
    }
    // Sort each day's appointments by time
    for (const key of Object.keys(map)) {
        map[key].sort((a, b) => a.appointment_time.localeCompare(b.appointment_time))
    }
    return map
})

const selectedDate = ref<string | null>(null)

function selectDate(fullDate: string) {
    selectedDate.value = selectedDate.value === fullDate ? null : fullDate
    emit('select-date', fullDate)
}

const selectedAppointments = computed(() => {
    if (!selectedDate.value) return []
    return appointmentsByDate.value[selectedDate.value] || []
})

function formatTime(time: string) {
    return time.substring(0, 5)
}

function statusDot(status: string) {
    const map: Record<string, string> = {
        scheduled: 'bg-blue-500',
        completed: 'bg-green-500',
        cancelled: 'bg-red-400',
        no_show: 'bg-yellow-500',
    }
    return map[status] || 'bg-gray-400'
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
</script>

<template>
    <div>
        <!-- Month nav -->
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900">
                {{ monthNames[currentMonth] }} {{ currentYear }}
            </h2>
            <div class="flex gap-1">
                <button @click="goToToday" class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Today
                </button>
                <button @click="prevMonth" class="p-1.5 text-gray-500 hover:text-gray-900 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <button @click="nextMonth" class="p-1.5 text-gray-500 hover:text-gray-900 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>
            </div>
        </div>

        <!-- Calendar grid -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <!-- Day headers -->
            <div class="grid grid-cols-7 border-b border-gray-200 bg-gray-50">
                <div v-for="day in dayNames" :key="day" class="px-2 py-2 text-xs font-medium text-gray-500 text-center">
                    {{ day }}
                </div>
            </div>

            <!-- Day cells -->
            <div class="grid grid-cols-7">
                <div
                    v-for="(day, idx) in calendarDays"
                    :key="idx"
                    @click="selectDate(day.fullDate)"
                    class="min-h-[80px] border-b border-r border-gray-100 px-1.5 py-1 cursor-pointer transition-colors"
                    :class="[
                        day.month !== 'current' ? 'bg-gray-50/50' : '',
                        day.isToday ? 'bg-blue-50/50' : '',
                        day.isWeekend && day.month === 'current' ? 'bg-gray-50/30' : '',
                        selectedDate === day.fullDate ? 'ring-2 ring-inset ring-blue-500 bg-blue-50/30' : 'hover:bg-gray-50',
                    ]"
                >
                    <!-- Date number -->
                    <div class="flex items-center justify-between mb-0.5">
                        <span
                            class="text-xs font-medium leading-none"
                            :class="[
                                day.month !== 'current' ? 'text-gray-300' : 'text-gray-700',
                                day.isToday ? 'bg-blue-600 text-white rounded-full w-5 h-5 flex items-center justify-center' : '',
                            ]"
                        >
                            {{ day.date }}
                        </span>
                        <span
                            v-if="appointmentsByDate[day.fullDate]?.length"
                            class="text-[10px] font-medium text-gray-400"
                        >
                            {{ appointmentsByDate[day.fullDate].length }}
                        </span>
                    </div>

                    <!-- Appointment dots/pills -->
                    <div class="space-y-0.5">
                        <div
                            v-for="appt in (appointmentsByDate[day.fullDate] || []).slice(0, 3)"
                            :key="appt.id"
                            class="flex items-center gap-1 px-1 py-0.5 rounded text-[10px] leading-tight truncate"
                            :class="appt.status === 'cancelled' ? 'opacity-50' : ''"
                        >
                            <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="statusDot(appt.status)" />
                            <span class="text-gray-600 truncate">
                                {{ formatTime(appt.appointment_time) }}
                                <template v-if="showPatient">{{ appt.patient?.user?.name?.split(' ')[0] }}</template>
                                <template v-if="showDoctor">Dr. {{ appt.doctor?.user?.name?.split(' ')[0] }}</template>
                            </span>
                        </div>
                        <div
                            v-if="(appointmentsByDate[day.fullDate] || []).length > 3"
                            class="text-[10px] text-gray-400 px-1"
                        >
                            +{{ appointmentsByDate[day.fullDate].length - 3 }} more
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="flex gap-4 mt-3 text-xs text-gray-500">
            <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500" /> Scheduled</div>
            <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-500" /> Completed</div>
            <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-yellow-500" /> No Show</div>
            <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-400" /> Cancelled</div>
        </div>

        <!-- Selected date detail panel -->
        <div v-if="selectedDate && selectedAppointments.length > 0" class="mt-4 bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                <h3 class="text-sm font-bold text-gray-900">
                    {{ new Date(selectedDate + 'T12:00:00').toLocaleDateString('en-GB', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) }}
                </h3>
                <p class="text-xs text-gray-500">{{ selectedAppointments.length }} appointment{{ selectedAppointments.length > 1 ? 's' : '' }}</p>
            </div>
            <div class="divide-y divide-gray-100">
                <div v-for="appt in selectedAppointments" :key="appt.id" class="px-4 py-3 flex items-center gap-4">
                    <div class="text-sm font-mono font-medium text-gray-900 w-12">
                        {{ formatTime(appt.appointment_time) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-gray-900">
                            {{ visitTypes[appt.visit_type] || appt.visit_type }}
                        </div>
                        <div class="text-xs text-gray-500 truncate">
                            <template v-if="showPatient">Patient: {{ appt.patient?.user?.name }}</template>
                            <template v-if="showPatient && showDoctor"> · </template>
                            <template v-if="showDoctor">Doctor: {{ appt.doctor?.user?.name }}</template>
                        </div>
                        <div v-if="appt.reason" class="text-xs text-gray-400 truncate mt-0.5">{{ appt.reason }}</div>
                    </div>
                    <span :class="statusBadge(appt.status)" class="text-xs font-medium px-2 py-1 rounded-full shrink-0">
                        {{ statusLabel(appt.status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Selected date but no appointments -->
        <div v-else-if="selectedDate" class="mt-4 bg-white rounded-xl border border-gray-200 px-4 py-6 text-center">
            <p class="text-sm text-gray-400">No appointments on this date.</p>
        </div>
    </div>
</template>
