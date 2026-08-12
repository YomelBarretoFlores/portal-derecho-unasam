<?php

namespace App\Policies;

use App\Models\User;

class ContentPolicy
{
    public function before(User $user): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isEditor();
    }

    public function view(User $user): bool
    {
        return $user->isEditor();
    }

    public function create(User $user): bool
    {
        return $user->isEditor();
    }

    public function update(User $user): bool
    {
        return $user->isEditor();
    }

    public function delete(User $user): bool
    {
        return $user->isEditor();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isEditor();
    }

    public function restore(User $user): bool
    {
        return $user->isEditor();
    }

    public function restoreAny(User $user): bool
    {
        return $user->isEditor();
    }

    public function forceDelete(User $user): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function replicate(User $user): bool
    {
        return $user->isEditor();
    }

    public function reorder(User $user): bool
    {
        return $user->isEditor();
    }
}
