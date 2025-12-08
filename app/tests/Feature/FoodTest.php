<?php

namespace Tests\Feature;

use App\Models\Food;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

//tests food management functionality
class FoodTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        //create a test user for authentication
        $this->user = User::factory()->create();
    }

    //test that foods index page loads
    public function test_foods_index_page_loads(): void
    {
        $response = $this->get('/foods');
        $response->assertStatus(200);
        $response->assertViewIs('foods.index');
    }

    //test that authenticated users can view foods index
    public function test_authenticated_users_can_view_foods_index(): void
    {
        $response = $this->withSession(['user_id' => $this->user->id])
            ->get('/foods');
        
        $response->assertStatus(200);
        $response->assertViewIs('foods.index');
    }

    //test that authenticated users can view create food form
    public function test_authenticated_users_can_view_create_food_form(): void
    {
        $response = $this->withSession(['user_id' => $this->user->id])
            ->get('/foods/create');
        
        $response->assertStatus(200);
        $response->assertViewIs('foods.create');
    }

    //test that authenticated users can create a food
    public function test_authenticated_users_can_create_food(): void
    {
        $foodData = [
            'name' => 'Test Apple',
            'calories' => 52,
            'protein' => 0.3,
            'carbs' => 14,
            'fat' => 0.2,
            'fiber' => 2.4,
            'sugar' => 10,
            'sodium_mg' => 1,
            'carbon_footprint_gco2e' => 20,
        ];

        $response = $this->withSession(['user_id' => $this->user->id])
            ->post('/foods', $foodData);
        
        $response->assertRedirect('/foods');
        $this->assertDatabaseHas('foods', [
            'name' => 'Test Apple',
            'user_id' => $this->user->id,
        ]);
    }

    //test that food name is required
    public function test_food_name_is_required(): void
    {
        $response = $this->withSession(['user_id' => $this->user->id])
            ->post('/foods', [
                'calories' => 100,
            ]);
        
        $response->assertSessionHasErrors('name');
    }

    //test that authenticated users can update a food
    public function test_authenticated_users_can_update_food(): void
    {
        $food = Food::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withSession(['user_id' => $this->user->id])
            ->put("/foods/{$food->id}", [
                'name' => 'Updated Food Name',
                'calories' => 100,
                'protein' => 5,
                'carbs' => 20,
                'fat' => 2,
                'fiber' => 3,
                'sugar' => 5,
                'sodium_mg' => 10,
                'carbon_footprint_gco2e' => 50,
            ]);
        
        $response->assertRedirect('/foods');
        $this->assertDatabaseHas('foods', [
            'id' => $food->id,
            'name' => 'Updated Food Name',
        ]);
    }

    //test that authenticated users can delete a food
    public function test_authenticated_users_can_delete_food(): void
    {
        $food = Food::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withSession(['user_id' => $this->user->id])
            ->delete("/foods/{$food->id}");
        
        $response->assertRedirect('/foods');
        $this->assertDatabaseMissing('foods', ['id' => $food->id]);
    }

    //test that food is created with correct nutritional values
    public function test_food_stores_nutritional_values_correctly(): void
    {
        $foodData = [
            'name' => 'Banana',
            'calories' => 89,
            'protein' => 1.1,
            'carbs' => 23,
            'fat' => 0.3,
            'fiber' => 2.6,
            'sugar' => 12,
            'sodium_mg' => 1,
            'carbon_footprint_gco2e' => 80,
        ];

        $this->withSession(['user_id' => $this->user->id])
            ->post('/foods', $foodData);
        
        $this->assertDatabaseHas('foods', [
            'name' => 'Banana',
            'calories' => 89,
            'protein' => 1.1,
            'carbs' => 23,
            'fat' => 0.3,
            'user_id' => $this->user->id,
        ]);
    }
}