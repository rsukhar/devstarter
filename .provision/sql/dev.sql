-- TEST DATA
-- email: admin@devstarter.local / Password: admin
INSERT INTO `users` SET `password` = '43926e247594b4a42cb9b9d94ba0471e1d3e704aae9ecb7c75f10d141af10aba',
    `email` = 'admin@devstarter.local', `username` = 'admin', `full_name` = 'admin', `role` = 'owner', `status` = 'active', `created` = NOW();
