export interface AuthUser {
    id: number
    name: string
    email: string
    roles: string[]
}

export interface Profile {
    phone: string | null
    date_of_birth: string | null
    codice_fiscale: string | null
    address: string | null
    city: string | null
    province: string | null
}

export interface InsurancePlan {
    id: number
    name: string
    code: string
    description: string | null
    is_active: boolean
}

export interface PatientInsurance {
    id: number
    insurance_plan_id: number
    policy_number: string
    holder_name: string | null
    tessera_sanitaria_number: string | null
    notes: string | null
    plan: InsurancePlan
}

export interface PatientData {
    blood_type: string | null
    allergies: string | null
    emergency_contact_name: string | null
    emergency_contact_phone: string | null
}

export interface User {
    id: number
    name: string
    email: string
    email_verified_at: string | null
    roles: string[]
    profile: Profile | null
}

export interface Appointment {
    id: number
    patient_id: number
    doctor_id: number
    appointment_date: string
    appointment_time: string
    visit_type: string
    status: 'scheduled' | 'completed' | 'cancelled' | 'no_show'
    reason: string | null
    notes: string | null
    cancelled_at: string | null
    patient?: { id: number; user: { name: string } }
    doctor?: { id: number; user: { name: string }; specialization: string }
}

export interface MedicalRecordFile {
    id: number
    medical_record_id: number
    file_name: string
    file_path: string
    file_type: string
    file_size: number
    uploaded_by: number
    description: string | null
}

export interface MedicalRecord {
    id: number
    patient_id: number
    doctor_id: number
    visit_date: string
    visit_type: string
    symptoms: string[] | null
    bp_systolic: number | null
    bp_diastolic: number | null
    heart_rate: number | null
    oxygen_saturation: number | null
    diagnosis: string | null
    notes: string | null
    recommendations: string | null
    follow_up_date: string | null
    created_at: string
    files: MedicalRecordFile[]
    doctor?: { id: number; user: { name: string } }
    patient?: { id: number; user: { name: string } }
}

export interface AuditLog {
    id: number
    user_id: number | null
    role: string | null
    action: string
    target_type: string | null
    target_id: number | null
    ip_address: string | null
    user_agent: string | null
    success: boolean
    metadata: Record<string, unknown> | null
    created_at: string
    user?: { id: number; name: string; email: string }
}

export interface MedicationReminder {
    id: number
    patient_id: number
    medication_name: string
    dosage: string
    reminder_time: string
    notes: string | null
    is_active: boolean
}

export interface PaginatedData<T> {
    data: T[]
    links: { url: string | null; label: string; active: boolean }[]
    meta: {
        current_page: number
        last_page: number
        per_page: number
        total: number
    }
}
