<?php

namespace App\Policies;

use App\Models\ContentEntry;
use App\Models\User;
use App\Support\CmsSchema;

class ContentEntryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAccessCms();
    }

    public function view(User $user, ContentEntry $entry): bool
    {
        return $user->canAccessCms() && array_key_exists($entry->collection, CmsSchema::COLLECTIONS);
    }

    public function create(User $user): bool
    {
        return $user->canAccessCms();
    }

    public function update(User $user, ContentEntry $entry): bool
    {
        return $this->view($user, $entry);
    }

    public function delete(User $user, ContentEntry $entry): bool
    {
        return $this->view($user, $entry);
    }

    public function deleteAny(User $user): bool
    {
        return $user->canAccessCms();
    }

    public function restore(User $user, ContentEntry $entry): bool
    {
        return $this->view($user, $entry);
    }

    public function restoreAny(User $user): bool
    {
        return $user->canAccessCms();
    }

    public function forceDelete(User $user, ContentEntry $entry): bool
    {
        return $this->view($user, $entry) && $user->role === 'admin';
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->canAccessCms() && $user->role === 'admin';
    }

    public function reorder(User $user): bool
    {
        return $user->canAccessCms();
    }
}
