<script setup lang="ts">
import StaffLayout from '@/layouts/StaffLayout.vue'

defineOptions({ layout: StaffLayout })

defineProps<{
    patient: {
        id: number
        name: string
        email: string
        phone: string | null
        date_of_birth: string | null
        codice_fiscale: string | null
        address: string | null
        city: string | null
        province: string | null
        blood_type: string | null
        allergies: string | null
        emergency_contact_name: string | null
        emergency_contact_phone: string | null
        insurance: {
            plan: string | null
            policy_number: string
            holder_name: string | null
            tessera_sanitaria_number: string | null
            notes: string | null
        } | null
    }
}>()
</script>

<template>
    <div>
        <a href="/staff/patients" class="text-sm text-gray-500 hover:text-gray-700 mb-2 inline-block">
            &larr; All Patients
        </a>
        <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ patient.name }}</h1>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Personal Information -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Personal Information</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Email</dt>
                        <dd class="text-gray-900">{{ patient.email }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Phone</dt>
                        <dd class="text-gray-900">{{ patient.phone || '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Date of Birth</dt>
                        <dd class="text-gray-900">{{ patient.date_of_birth ? patient.date_of_birth.substring(0, 10) : '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Codice Fiscale</dt>
                        <dd class="text-gray-900 font-mono text-xs">{{ patient.codice_fiscale || '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Address</dt>
                        <dd class="text-gray-900 text-right">
                            {{ [patient.address, patient.city, patient.province].filter(Boolean).join(', ') || '—' }}
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Medical Information -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Medical Information</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Blood Type</dt>
                        <dd class="text-gray-900 font-semibold">{{ patient.blood_type || '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Allergies</dt>
                        <dd class="text-gray-900">{{ patient.allergies || 'None reported' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Emergency Contact</dt>
                        <dd class="text-gray-900">{{ patient.emergency_contact_name || '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Emergency Phone</dt>
                        <dd class="text-gray-900">{{ patient.emergency_contact_phone || '—' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Insurance Information -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 lg:col-span-2">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Insurance Information</h2>
                <div v-if="patient.insurance">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-gray-500">Plan</dt>
                            <dd class="text-gray-900 font-medium">{{ patient.insurance.plan || '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Policy Number</dt>
                            <dd class="text-gray-900 font-mono text-xs">{{ patient.insurance.policy_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Holder Name</dt>
                            <dd class="text-gray-900">{{ patient.insurance.holder_name || 'Same as patient' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Tessera Sanitaria</dt>
                            <dd class="text-gray-900 font-mono text-xs">{{ patient.insurance.tessera_sanitaria_number || '—' }}</dd>
                        </div>
                        <div v-if="patient.insurance.notes" class="sm:col-span-2">
                            <dt class="text-gray-500">Notes</dt>
                            <dd class="text-gray-900">{{ patient.insurance.notes }}</dd>
                        </div>
                    </dl>
                </div>
                <p v-else class="text-sm text-gray-400">No insurance information on file.</p>
            </div>
        </div>
    </div>
</template>
