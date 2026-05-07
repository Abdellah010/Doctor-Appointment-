// ── Domain Types ──────────────────────────────────────────────

export type UserRole = 'patient' | 'doctor' | 'admin'
export type InsuranceType = 'cnss' | 'ramed' | 'private' | 'none'
export type AppointmentStatus = 'pending' | 'confirmed' | 'completed' | 'cancelled' | 'declined'
export type DoctorStatus = 'pending' | 'verified' | 'rejected'

export interface User {
  id: number
  name: string
  email: string
  phone?: string
  role: UserRole
  insurance_type: InsuranceType
  avatar?: string
  created_at: string
}

export interface Doctor {
  id: number
  user_id: number
  user: User
  specialty: string
  city: string
  license_number: string
  consultation_fee: number
  status: DoctorStatus
  rejection_reason?: string
  bio?: string
  available_days: string[]
  slot_duration: number
  rating: number
  rating_count: number
  accepts_cnss: boolean
  accepts_ramed: boolean
  accepts_private: boolean
  appointments?: Appointment[]
  created_at: string
}

export interface Slot {
  id: number
  doctor_id: number
  date: string        // YYYY-MM-DD
  start_time: string  // HH:MM
  end_time: string
  is_booked: boolean
  is_blocked: boolean
}

export interface Appointment {
  id: number
  patient_id: number
  doctor_id: number
  slot_id: number
  patient: User
  doctor: Doctor
  slot: Slot
  scheduled_at: string
  duration_minutes: number
  status: AppointmentStatus
  reason?: string
  notes?: string
  insurance_type: InsuranceType
  patient_rating?: number
  patient_review?: string
  fee: number
  sms_sent: boolean
  created_at: string
}

// ── Page Props ────────────────────────────────────────────────

export interface PaginatedResult<T> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export interface DoctorsIndexProps {
  doctors: PaginatedResult<Doctor>
  filters: Record<string, string>
  specialties: string[]
  cities: string[]
}

export interface PatientDashboardProps {
  user: User
  upcoming: Appointment[]
  past: Appointment[]
  stats: {
    upcoming_count: number
    completed_count: number
    doctors_visited: number
  }
}

export interface AdminDashboardProps {
  stats: {
    total_patients: number
    active_doctors: number
    total_appointments: number
    pending_verifications: number
    rejected_doctors: number
  }
  recent_appointments: Appointment[]
  weekly_activity: { label: string; count: number }[]
}

export interface AdminVerificationsProps {
  doctors: PaginatedResult<Doctor>
  status: string
  counts: {
    all: number
    pending: number
    verified: number
    rejected: number
  }
}
