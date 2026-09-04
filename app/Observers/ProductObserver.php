<?php

namespace App\Observers;

use App\Models\Product;
use App\Services\Notification\ActivityLoggerService;

class ProductObserver
{
    public function __construct(
        private readonly ActivityLoggerService $logger
    ) {}

    public function created(Product $product): void
    {
        $this->logger->log('product.created', $product, null, $product->only([
            'name', 'sku', 'price', 'category_id',
        ]));
    }

    public function updated(Product $product): void
    {
        $this->logger->log(
            'product.updated',
            $product,
            $product->getOriginal(),
            $product->getChanges()
        );
    }

    public function deleted(Product $product): void
    {
        $this->logger->log('product.deleted', $product, $product->only(['name', 'sku']), null);
    }
}
