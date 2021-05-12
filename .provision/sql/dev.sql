-- TEST DATA
-- email: admin@devstarter.local / Password: admin
INSERT INTO `users` SET `password` = '81e7e94550dcd7e0bdadd8b68fb1de3452499fb918c1c7330b4e5cad3c51f936',
    `email` = 'admin@devstarter.local', `username` = 'admin', `full_name` = 'admin', `role` = 'owner', `status` = 'active', `created` = NOW();
