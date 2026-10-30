<?php
return [
    'db_path'      => BASE_PATH . '/storage/database.sqlite',
    'users_file'   => BASE_PATH . '/storage/users.txt',
    'upload_dir'   => BASE_PATH . '/public/uploads',
    'upload_url'   => '/uploads',
    'upload_max'   => 2 * 1024 * 1024, // 2 MB
    'per_page'     => 6,
];
