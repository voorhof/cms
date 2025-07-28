<?php

namespace App\Models;

use App\Policies\RolePolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Spatie\Permission\Models\Role as SpatieRole;

#[UsePolicy(RolePolicy::class)]
class Role extends SpatieRole
{
    // This class extends Spatie's Role model and adds the UsePolicy attribute
    // No additional functionality needed as we're just adding the policy
}
