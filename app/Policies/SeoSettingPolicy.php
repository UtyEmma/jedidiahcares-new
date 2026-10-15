<?php

namespace App\Policies;

use App\Models\SeoSetting;
use App\Models\User;

class SeoSettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAccessCms();
    }

    public function view(User $user, SeoSetting $setting): bool
    {
        return $user->canAccessCms();
    }

    public function update(User $user, SeoSetting $setting): bool
    {
        return $user->canAccessCms();
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function delete(User $user, SeoSetting $setting): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
