<?php

namespace Tests\Unit;

use App\Models\AllergyProfile;
use App\Models\FamilyMember;
use App\Models\Product;
use App\Services\AllergyChecker;
use Mockery;
use Tests\TestCase;

class AllergyCheckerTest extends TestCase
{
    public function test_it_detects_matching_allergens_from_raw_ingredients()
    {
        $ingredientAlias = Mockery::mock('alias:App\Models\Ingredient');
        $ingredientAlias->shouldReceive('query')->once()->andReturnSelf();
        $ingredientAlias->shouldReceive('where')->once()->with('aller_name', 'lactose')->andReturnSelf();
        $ingredientAlias->shouldReceive('pluck')->once()->with('name')->andReturn(collect(['Milk']));

        $member = new FamilyMember();
        $member->setRelation('allergyProfiles', collect([
            new AllergyProfile(['allergy_type' => 'lactose']),
        ]));

        $product = new Product([
            'raw_ingredients' => 'Water, Milk, Sugar',
            'halal_status' => 'unknown',
        ]);

        $result = (new AllergyChecker())->check($product, $member);

        $this->assertFalse($result['safe']);
        $this->assertSame(['lactose'], $result['triggered_allergens']);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
