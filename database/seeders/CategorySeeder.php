<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $tree = [
            'Électronique' => [
                'Téléphones',
                'Ordinateurs',
                'Accessoires',
                'Télévisions',
            ],
            'Mode' => [
                'Vêtements Homme',
                'Vêtements Femme',
                'Chaussures',
            ],
            'Maison & Cuisine' => [
                'Électroménager',
                'Ustensiles de cuisine',
                'Décoration',
            ],
            'Beauté & Santé' => [
                'Soins du visage',
                'Parfums',
            ],
        ];

        $sortOrder = 0;

        foreach ($tree as $parentName => $children) {
            $parent = Category::firstOrCreate(
                ['slug' => Str::slug($parentName)],
                [
                    'name' => $parentName,
                    'description' => "Catégorie {$parentName}",
                    'is_active' => true,
                    'sort_order' => $sortOrder++,
                ]
            );

            $childSortOrder = 0;

            foreach ($children as $childName) {
                Category::firstOrCreate(
                    ['slug' => Str::slug($parentName . '-' . $childName)],
                    [
                        'parent_id' => $parent->id,
                        'name' => $childName,
                        'description' => "{$childName} — {$parentName}",
                        'is_active' => true,
                        'sort_order' => $childSortOrder++,
                    ]
                );
            }
        }

        $this->command->info('Catégories créées : ' . Category::count());
    }
}
