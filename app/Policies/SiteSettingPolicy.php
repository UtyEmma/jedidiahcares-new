<?php

namespace App\Policies;

use App\Models\SiteSetting;
use App\Models\User;

class SiteSettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAccessCms() && $user->role === 'admin';
    }

    public function update(User $user, SiteSetting $setting): bool
    {
        return $this->viewAny($user) && $setting->key === 'site';
    }
}
