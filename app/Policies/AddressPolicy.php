<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\User;

class AddressPolicy
{
    /**
     * Un client ne peut voir/modifier/supprimer que ses propres adresses (section 45 : sécurité).
     */
    public function view(User $user, Address $address): bool
    {
        return $user->customer && $address->customer_id === $user->customer->id;
    }

    public function update(User $user, Address $address): bool
    {
        return $this->view($user, $address);
    }

    public function delete(User $user, Address $address): bool
    {
        return $this->view($user, $address);
    }
}
