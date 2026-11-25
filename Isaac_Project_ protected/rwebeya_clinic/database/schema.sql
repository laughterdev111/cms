CREATE DATABASE IF NOT EXISTS rwebeya_clinic CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE rwebeya_clinic;

CREATE TABLE IF NOT EXISTS users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  avatar_path VARCHAR(255) NULL,
  password_hash VARCHAR(255) NULL,
  role ENUM('Admin','Receptionist','Doctor') NOT NULL,
  is_verified TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  verification_token_hash CHAR(64) NULL,
  verification_expires_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_users_role_status (role, is_active)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS patients (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_number VARCHAR(30) NOT NULL UNIQUE,
  full_name VARCHAR(150) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  date_of_birth DATE NULL,
  gender ENUM('Female','Male','Other','Prefer not to say') NOT NULL,
  insurance_provider ENUM('None','RSSB','RAMA','Other') NOT NULL DEFAULT 'None',
  insurance_number VARCHAR(80) NULL,
  registered_by BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_patients_registered_by FOREIGN KEY (registered_by) REFERENCES users(id),
  INDEX idx_patient_name (full_name),
  INDEX idx_patient_phone (phone)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS appointments (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id BIGINT UNSIGNED NOT NULL,
  doctor_id BIGINT UNSIGNED NULL,
  appointment_at DATETIME NOT NULL,
  reason VARCHAR(255) NULL,
  status ENUM('Scheduled','Arrived','Cancelled','Completed') NOT NULL DEFAULT 'Scheduled',
  created_by BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_appointments_patient FOREIGN KEY (patient_id) REFERENCES patients(id),
  CONSTRAINT fk_appointments_doctor FOREIGN KEY (doctor_id) REFERENCES users(id),
  CONSTRAINT fk_appointments_creator FOREIGN KEY (created_by) REFERENCES users(id),
  INDEX idx_appointments_date (appointment_at, status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS consultations (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id BIGINT UNSIGNED NOT NULL,
  appointment_id BIGINT UNSIGNED NULL,
  doctor_id BIGINT UNSIGNED NOT NULL,
  symptoms TEXT NULL,
  diagnosis TEXT NULL,
  treatment_notes TEXT NULL,
  status ENUM('Waiting','In Progress','Completed') NOT NULL DEFAULT 'Waiting',
  started_at DATETIME NULL,
  completed_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_consult_patient FOREIGN KEY (patient_id) REFERENCES patients(id),
  CONSTRAINT fk_consult_appointment FOREIGN KEY (appointment_id) REFERENCES appointments(id),
  CONSTRAINT fk_consult_doctor FOREIGN KEY (doctor_id) REFERENCES users(id),
  INDEX idx_consult_queue (status, created_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS audit_log (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL,
  action VARCHAR(100) NOT NULL,
  entity_type VARCHAR(60) NULL,
  entity_id BIGINT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;
