<?php

namespace App\Services\Promotion;

use App\Models\Promotion;
use Illuminate\Support\Facades\DB;

class PromotionService
{
    public function createWithProducts(array $data, array $productIds = []): Promotion
    {
        return DB::transaction(function () use ($data, $productIds) {
            $promotion = Promotion::create($data);

            if (! empty($productIds)) {
                $promotion->products()->sync($productIds);
            }

            return $promotion;
        });
    }

    public function activePromotions()
    {
        return Promotion::active()->get()->filter(fn ($p) => $p->isValid());
    }
}
