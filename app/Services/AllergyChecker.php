<?php

namespace App\Services;

use App\Models\FamilyMember;
use App\Models\Ingredient;
use App\Models\Product;

class AllergyChecker
{
    public function check(Product $product, FamilyMember $member): array
    {
        $triggered = [];
        $rawIngredients = mb_strtolower($product->raw_ingredients ?? '');
        $allergyTypes = $member->allergyProfiles->pluck('allergy_type')->toArray();

        foreach ($allergyTypes as $allergyType) {
            if ($allergyType === 'halal' && $product->halal_status === 'haram') {
                $triggered[] = 'halal';
                continue;
            }

            $keywords = Ingredient::query()
                ->where('aller_name', $allergyType)
                ->pluck('name')
                ->toArray();

            foreach ($keywords as $keyword) {
                if ($keyword !== '' && mb_stripos($rawIngredients, mb_strtolower($keyword)) !== false) {
                    $triggered[] = $allergyType;
                    break;
                }
            }
        }

        return [
            'safe' => empty($triggered),
            'triggered_allergens' => array_values(array_unique($triggered)),
        ];
    }
}
