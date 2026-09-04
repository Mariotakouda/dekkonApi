<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

trait HasUuid
{
    use HasUuids;

    public function uniqueIds(): array
    {
        return ['id'];
    }
}
