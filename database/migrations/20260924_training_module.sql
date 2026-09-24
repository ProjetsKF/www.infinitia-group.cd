CREATE TABLE IF NOT EXISTS administrators (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_administrators_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS training_sessions (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    formation_code VARCHAR(80) NOT NULL,
    start_date DATE NULL,
    end_date DATE NULL,
    schedule VARCHAR(190) NULL,
    location VARCHAR(190) NULL,
    capacity INT UNSIGNED NULL,
    prerequisites TEXT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'draft',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_training_sessions_formation (formation_code),
    KEY idx_training_sessions_status_date (status, start_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS training_registrations (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    session_id INT UNSIGNED NOT NULL,
    formation_code VARCHAR(80) NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    email VARCHAR(190) NOT NULL,
    current_level VARCHAR(190) NOT NULL,
    availability VARCHAR(255) NULL,
    message TEXT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'new',
    submitted_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_training_registrations_session (session_id),
    KEY idx_training_registrations_formation (formation_code),
    KEY idx_training_registrations_status_date (status, submitted_at),
    CONSTRAINT fk_training_registrations_session FOREIGN KEY (session_id) REFERENCES training_sessions (id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
