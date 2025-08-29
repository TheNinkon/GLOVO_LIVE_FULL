<?php

namespace App\Policies;

use App\Models\Forecast;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ForecastPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin(); // Asumimos que todos los admins pueden ver
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Forecast $forecast): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Forecast $forecast): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Forecast $forecast): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Forecast $forecast): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Forecast $forecast): bool
    {
        return $user->isAdmin();
    }
}
