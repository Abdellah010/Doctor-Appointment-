-- ═══════════════════════════════════════════════════════════
-- DocAppoint — MySQL 8 Schema
-- ═══════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS docappoint CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE docappoint;

-- ─── USERS (unified auth table) ────────────────────────────
CREATE TABLE users (
  id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(255) NOT NULL,
  email         VARCHAR(255) UNIQUE NOT NULL,
  phone         VARCHAR(20),
  password      VARCHAR(255) NOT NULL,
  role          ENUM('patient','doctor','admin') NOT NULL DEFAULT 'patient',
  avatar_url    VARCHAR(500),
  google_id     VARCHAR(255),
  email_verified_at TIMESTAMP NULL,
  remember_token VARCHAR(100),
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_role (role),
  INDEX idx_email (email)
);

-- ─── SPECIALTIES ───────────────────────────────────────────
CREATE TABLE specialties (
  id    BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name  VARCHAR(100) NOT NULL UNIQUE,
  slug  VARCHAR(100) NOT NULL UNIQUE,
  icon  VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ─── DOCTORS ───────────────────────────────────────────────
CREATE TABLE doctors (
  id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id             BIGINT UNSIGNED NOT NULL,
  specialty_id        BIGINT UNSIGNED NOT NULL,
  license_number      VARCHAR(100) UNIQUE NOT NULL,
  bio                 TEXT,
  city                VARCHAR(100),
  address             VARCHAR(500),
  consultation_fee    DECIMAL(8,2) NOT NULL DEFAULT 0,
  accepts_cnss        BOOLEAN DEFAULT FALSE,
  accepts_ramed       BOOLEAN DEFAULT FALSE,
  verification_status ENUM('pending','verified','rejected') DEFAULT 'pending',
  verified_at         TIMESTAMP NULL,
  verified_by         BIGINT UNSIGNED NULL,
  rejection_reason    TEXT NULL,
  rejection_notes     TEXT NULL,
  rating_avg          DECIMAL(3,2) DEFAULT 0.00,
  rating_count        INT UNSIGNED DEFAULT 0,
  created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (specialty_id) REFERENCES specialties(id),
  FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_verification_status (verification_status),
  INDEX idx_city (city),
  INDEX idx_specialty (specialty_id)
);

-- ─── PATIENTS ──────────────────────────────────────────────
CREATE TABLE patients (
  id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id      BIGINT UNSIGNED NOT NULL UNIQUE,
  date_of_birth DATE,
  gender       ENUM('male','female','other'),
  insurance    ENUM('cnss','ramed','private','none') DEFAULT 'none',
  blood_type   VARCHAR(5),
  allergies    TEXT,
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ─── DOCTOR AVAILABILITY (weekly schedule) ─────────────────
CREATE TABLE doctor_availability (
  id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  doctor_id  BIGINT UNSIGNED NOT NULL,
  day_of_week TINYINT NOT NULL COMMENT '0=Sun,1=Mon,...,6=Sat',
  start_time TIME NOT NULL,
  end_time   TIME NOT NULL,
  slot_duration_minutes TINYINT DEFAULT 30,
  is_active  BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
  INDEX idx_doctor_day (doctor_id, day_of_week)
);

-- ─── DOCTOR EXCEPTIONS (holidays, blocked days) ────────────
CREATE TABLE doctor_exceptions (
  id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  doctor_id  BIGINT UNSIGNED NOT NULL,
  date       DATE NOT NULL,
  reason     VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
  INDEX idx_doctor_date (doctor_id, date)
);

-- ─── APPOINTMENTS ───────────────────────────────────────────
CREATE TABLE appointments (
  id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id      BIGINT UNSIGNED NOT NULL,
  doctor_id       BIGINT UNSIGNED NOT NULL,
  appointment_date DATE NOT NULL,
  start_time      TIME NOT NULL,
  end_time        TIME NOT NULL,
  status          ENUM('pending','confirmed','completed','cancelled','no_show')
                  DEFAULT 'pending',
  reason          TEXT,
  doctor_notes    TEXT,
  insurance_used  ENUM('cnss','ramed','private','none') DEFAULT 'none',
  fee_paid        DECIMAL(8,2),
  cancelled_by    ENUM('patient','doctor','admin') NULL,
  cancelled_at    TIMESTAMP NULL,
  cancellation_reason TEXT NULL,
  reminder_sent   BOOLEAN DEFAULT FALSE,
  created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (patient_id) REFERENCES patients(id),
  FOREIGN KEY (doctor_id) REFERENCES doctors(id),
  INDEX idx_doctor_date (doctor_id, appointment_date),
  INDEX idx_patient (patient_id),
  INDEX idx_status (status),
  UNIQUE KEY unique_slot (doctor_id, appointment_date, start_time)
);

-- ─── REVIEWS ───────────────────────────────────────────────
CREATE TABLE reviews (
  id             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  appointment_id BIGINT UNSIGNED NOT NULL UNIQUE,
  patient_id     BIGINT UNSIGNED NOT NULL,
  doctor_id      BIGINT UNSIGNED NOT NULL,
  rating         TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
  comment        TEXT,
  is_anonymous   BOOLEAN DEFAULT FALSE,
  created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (appointment_id) REFERENCES appointments(id),
  FOREIGN KEY (patient_id) REFERENCES patients(id),
  FOREIGN KEY (doctor_id) REFERENCES doctors(id),
  INDEX idx_doctor (doctor_id)
);

-- ─── NOTIFICATIONS ──────────────────────────────────────────
CREATE TABLE notifications (
  id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id    BIGINT UNSIGNED NOT NULL,
  type       ENUM('appointment_confirmed','appointment_cancelled',
                  'appointment_reminder','verification_approved',
                  'verification_rejected','new_review') NOT NULL,
  data       JSON,
  read_at    TIMESTAMP NULL,
  sent_via   SET('email','sms','push'),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_user_read (user_id, read_at)
);

-- ─── SLOT LOCKS (Redis-backed, but DB fallback) ─────────────
CREATE TABLE slot_locks (
  id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  doctor_id   BIGINT UNSIGNED NOT NULL,
  date        DATE NOT NULL,
  start_time  TIME NOT NULL,
  locked_by   BIGINT UNSIGNED NOT NULL,
  locked_until TIMESTAMP NOT NULL,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
  FOREIGN KEY (locked_by) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY unique_lock (doctor_id, date, start_time),
  INDEX idx_expires (locked_until)
);

-- ─── SEED: Specialties ──────────────────────────────────────
INSERT INTO specialties (name, slug, icon) VALUES
  ('Cardiology', 'cardiology', '❤️'),
  ('Neurology', 'neurology', '🧠'),
  ('Dermatology', 'dermatology', '🫧'),
  ('Pediatrics', 'pediatrics', '👶'),
  ('Orthopedics', 'orthopedics', '🦴'),
  ('Ophthalmology', 'ophthalmology', '👁️'),
  ('Gastroenterology', 'gastroenterology', '🫀'),
  ('Endocrinology', 'endocrinology', '⚗️'),
  ('General Practice', 'general', '🩺');
