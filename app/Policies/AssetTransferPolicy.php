<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AssetTransfer;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssetTransferPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_asset::transfer');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AssetTransfer $assetTransfer): bool
    {
        return $user->can('view_asset::transfer');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_asset::transfer');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AssetTransfer $assetTransfer): bool
    {
        // Jika pengguna adalah admin, selalu izinkan
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Jika bukan admin, periksa izin dan document
        return $user->can('update_asset::transfer') && $assetTransfer->document === null;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AssetTransfer $assetTransfer): bool
    {
        return $user->can('delete_asset::transfer');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_asset::transfer');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, AssetTransfer $assetTransfer): bool
    {
        return $user->can('{{ ForceDelete }}');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('{{ ForceDeleteAny }}');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, AssetTransfer $assetTransfer): bool
    {
        return $user->can('{{ Restore }}');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('{{ RestoreAny }}');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, AssetTransfer $assetTransfer): bool
    {
        return $user->can('{{ Replicate }}');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('{{ Reorder }}');
    }
}
