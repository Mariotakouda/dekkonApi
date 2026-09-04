<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    /**
     * Un employé ne peut pas se désactiver/modifier son propre rôle lui-même
     * (évite qu'un admin se retire accidentellement ses propres droits).
     */
    public function update(User $user, Employee $employee): bool
    {
        return $user->hasPermission('employees.manage');
    }

    public function delete(User $user, Employee $employee): bool
    {
        if ($employee->user_id === $user->id) {
            return false; // on ne peut jamais se supprimer soi-même
        }

        return $user->hasPermission('employees.manage');
    }
}
