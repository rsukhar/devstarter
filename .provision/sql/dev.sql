-- TEST DATA
-- email: admin@devstarter.local / Password: admin
INSERT INTO `users` SET `password` = '81e7e94550dcd7e0bdadd8b68fb1de3452499fb918c1c7330b4e5cad3c51f936',
    `email` = 'admin@devstarter.local', `username` = 'admin', `full_name` = 'admin', `role` = 'admin', `status` = 'active', `created` = NOW();

CREATE TABLE `students` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `first_name` VARCHAR(255) NULL,
    `last_name` VARCHAR(255) NULL,
    `birthday` DATE NULL,
    `phone` VARCHAR(100) NULL,
    `created` DATETIME NULL,
    `updated` DATETIME NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `students` (`first_name`, `last_name`, `birthday`, `phone`, `created`, `updated`)
VALUES
('Сергей', 'Иванов', '1991-05-01', '79261234567', NOW(), NOW()),
('Алексей', 'Петров', '1991-04-12', '79261262367', NOW(), NOW()),
('Ирина', 'Фролова', '1991-08-16', '79261266667', NOW(), NOW()),
('Анна', 'Мокшина', '1991-05-21', '79261232167', NOW(), NOW()),
('Виталий', 'Леснов', '1991-02-19', '79267764567', NOW(), NOW()),
('Антон', 'Шмонин', '1991-08-11', '79261235647', NOW(), NOW()),
('Аркадий', 'Донской', '1991-12-12', '79223454567', NOW(), NOW()),
('Василий', 'Петров', '1991-01-17', '79261452567', NOW(), NOW()),
('Анна', 'Васильева', '1991-03-04', '79209834567', NOW(), NOW()),
('Андрей', 'Иванов', '1991-08-11', '79261212367', NOW(), NOW());