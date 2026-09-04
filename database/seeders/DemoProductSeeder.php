<?php

namespace Database\Seeders;

use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoProductSeeder extends Seeder
{
    public function run(): void
    {
        $telephones = Category::where('slug', 'electronique-telephones')->first();
        $vetementsHomme = Category::where('slug', 'mode-vetements-homme')->first();
        $electromenager = Category::where('slug', 'maison-cuisine-electromenager')->first();

        if (! $telephones || ! $vetementsHomme || ! $electromenager) {
            $this->command->error('Catégories manquantes — lancez CategorySeeder avant DemoProductSeeder.');
            return;
        }

        // Produit 1 — Téléphone avec variantes de couleur (pas de taille)
        $this->createProductWithVariants(
            category: $telephones,
            name: 'Smartphone Kalo X12',
            description: 'Smartphone double SIM, écran 6.5", 128 Go de stockage, batterie longue durée.',
            price: 89000,
            comparePrice: 99000,
            costPrice: 65000,
            brand: 'Kalo',
            isFeatured: true,
            variants: [
                ['label' => 'Noir', 'attributes' => ['couleur' => 'Noir'], 'stock' => 25],
                ['label' => 'Bleu', 'attributes' => ['couleur' => 'Bleu'], 'stock' => 15],
                ['label' => 'Or', 'attributes' => ['couleur' => 'Or'], 'stock' => 8],
            ]
        );

        // Produit 2 — T-shirt avec variantes couleur + taille (section 10 du cahier des charges)
        $this->createProductWithVariants(
            category: $vetementsHomme,
            name: 'T-shirt Coton DEKKON',
            description: 'T-shirt 100% coton, coupe classique, disponible en plusieurs couleurs et tailles.',
            price: 6500,
            comparePrice: null,
            costPrice: 3000,
            brand: 'DEKKON Essentials',
            isFeatured: false,
            variants: [
                ['label' => 'Noir / M', 'attributes' => ['couleur' => 'Noir', 'taille' => 'M'], 'stock' => 30],
                ['label' => 'Noir / L', 'attributes' => ['couleur' => 'Noir', 'taille' => 'L'], 'stock' => 20],
                ['label' => 'Blanc / M', 'attributes' => ['couleur' => 'Blanc', 'taille' => 'M'], 'stock' => 25],
                ['label' => 'Blanc / L', 'attributes' => ['couleur' => 'Blanc', 'taille' => 'L'], 'stock' => 3], // stock faible volontaire
            ]
        );

        // Produit 3 — Électroménager sans variantes (une seule variante par défaut)
        $this->createProductWithVariants(
            category: $electromenager,
            name: 'Bouilloire électrique 1.7L',
            description: 'Bouilloire électrique inox, arrêt automatique, 1.7 litre.',
            price: 15000,
            comparePrice: 18000,
            costPrice: 9500,
            brand: 'HomeTech',
            isFeatured: true,
            variants: [
                ['label' => 'Standard', 'attributes' => [], 'stock' => 40],
            ]
        );

        $this->command->info('Produits créés : ' . Product::count() . ' | Variantes : ' . ProductVariant::count());
    }

    private function createProductWithVariants(
        Category $category,
        string $name,
        string $description,
        float $price,
        ?float $comparePrice,
        float $costPrice,
        string $brand,
        bool $isFeatured,
        array $variants,
    ): void {
        $slug = Str::slug($name);
        $baseSku = Str::upper(Str::slug($name, '')) ;
        $baseSku = substr($baseSku, 0, 10);

        $product = Product::firstOrCreate(
            ['slug' => $slug],
            [
                'category_id' => $category->id,
                'name' => $name,
                'description' => $description,
                'sku' => $baseSku . '-' . strtoupper(Str::random(4)),
                'price' => $price,
                'compare_at_price' => $comparePrice,
                'cost_price' => $costPrice,
                'status' => ProductStatus::ACTIVE,
                'brand' => $brand,
                'weight' => null,
                'is_featured' => $isFeatured,
            ]
        );

        // Image principale placeholder
        ProductImage::firstOrCreate(
            ['product_id' => $product->id, 'sort_order' => 0],
            [
                'url' => 'https://placehold.co/600x600?text=' . urlencode($name),
                'alt_text' => $name,
                'is_primary' => true,
            ]
        );

        foreach ($variants as $index => $variantData) {
            $variant = ProductVariant::firstOrCreate(
                [
                    'product_id' => $product->id,
                    'sku' => $product->sku . '-V' . ($index + 1),
                ],
                [
                    'name' => $variantData['label'],
                    'price' => null, // hérite du prix produit (section 10)
                    'attributes' => $variantData['attributes'],
                    'is_active' => true,
                    'is_default' => $index === 0,
                ]
            );

            Inventory::firstOrCreate(
                ['product_variant_id' => $variant->id],
                [
                    'quantity' => $variantData['stock'],
                    'reserved_quantity' => 0,
                    'low_stock_threshold' => 5,
                ]
            );
        }
    }
}
