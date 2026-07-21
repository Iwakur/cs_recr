CREATE TABLE applications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) DEFAULT NULL,
    phone VARCHAR(25) DEFAULT NULL,
    telegram VARCHAR(100) DEFAULT NULL,
    discord VARCHAR(100) DEFAULT NULL,
    instagram VARCHAR(100) DEFAULT NULL,
    preferred_contact VARCHAR(50) NOT NULL,
    class VARCHAR(50) NOT NULL,
    age TINYINT UNSIGNED NOT NULL,
    gender VARCHAR(50) NOT NULL,
    preferred_role VARCHAR(100) NOT NULL,
    second_choice VARCHAR(100) DEFAULT NULL,
    motivation TEXT NOT NULL,
    programming_level VARCHAR(50) DEFAULT NULL,
    electronics_level VARCHAR(50) DEFAULT NULL,
    cad_level VARCHAR(50) DEFAULT NULL,
    science_level VARCHAR(50) DEFAULT NULL,
    known_skills TEXT DEFAULT NULL,
    problem_solving TEXT DEFAULT NULL,
    role_flexibility VARCHAR(50) DEFAULT NULL,
    programming_experience TEXT DEFAULT NULL,
    electronics_experience TEXT DEFAULT NULL,
    cad_experience TEXT DEFAULT NULL,
    science_experience TEXT DEFAULT NULL,
    other_projects TEXT DEFAULT NULL,
    availability TEXT NOT NULL,
    time_commitment TEXT NOT NULL,
    consent TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);


# cansat-site/
# ├── compose.yaml
# ├── Dockerfile
# ├── .env
# ├── .gitignore
# │
# ├── index.php
# ├── apply.php
# ├── success.php
# │
# ├── includes/
# │   ├── header.php
# │   ├── footer.php
# │   └── db.php
# │
# ├── assets/
# │   ├── css/
# │   └── images/
# │
# └── docker/
#     └── mysql/
#         └── init.sql
