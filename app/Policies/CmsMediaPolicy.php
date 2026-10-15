<?php

namespace App\Policies;

use App\Models\CmsMedia;
use App\Models\User;

class CmsMediaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAccessCms();
    }

    public function view(User $user, CmsMedia $media): bool
    {
        return $user->canAccessCms();
    }

    public function update(User $user, CmsMedia $media): bool
    {
        return $user->canAccessCms();
    }

    public function delete(User $user, CmsMedia $media): bool
    {
        return $user->canAccessCms() && $user->role === 'admin';
    }

    public function reorder(User $user): bool
    {
        return $user->canAccessCms();
    }
}
