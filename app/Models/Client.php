<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\Client as BaseClient;

class Client extends BaseClient implements AuthorizableContract
{
    use HasRoles;
    use Authorizable;

    public $guard_name = 'api';
}
