<?php

use App\Core\Env;

return [
    'jwt_key' => Env::get('JWT_SECRET'),
    'jwt_alg' => Env::get('JWT_ALG', 'HS256'),
];
