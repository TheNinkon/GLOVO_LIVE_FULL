<?php

namespace App\Policies;

use App\Models\Rider;
use App\Models\User; // Importar el modelo User (Admin)

class RiderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Un admin siempre puede ver el listado
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Rider $rider): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Rider $rider): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Rider $rider): bool
    {
        return true;
    }
}
