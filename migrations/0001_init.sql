CREATE TABLE IF NOT EXISTS cars (
    id INT UNSIGNED NOT NULL,
    seats TINYINT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT chk_cars_seats CHECK (seats BETWEEN 1 AND 8)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS groups_queue (
    id INT UNSIGNED NOT NULL,
    people TINYINT UNSIGNED NOT NULL,
    assigned_car_id INT UNSIGNED NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'waiting',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_groups_queue_status_created_at (status, created_at),
    CONSTRAINT chk_groups_queue_people CHECK (people BETWEEN 1 AND 6),
    CONSTRAINT fk_groups_queue_assigned_car FOREIGN KEY (assigned_car_id) REFERENCES cars(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
