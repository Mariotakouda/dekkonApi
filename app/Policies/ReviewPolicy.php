<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function create(User $user, string $orderItemId): bool
    {
        if (! $user->customer) {
            return false;
        }

        // Le customer doit être propriétaire de la commande liée à cette ligne
        return \App\Models\OrderItem::where('id', $orderItemId)
            ->whereHas('order', fn ($q) => $q->where('customer_id', $user->customer->id)->where('status', 'DELIVERED'))
            ->exists();
    }

    public function view(User $user, Review $review): bool
    {
        return $user->customer && $review->customer_id === $user->customer->id;
    }
}
