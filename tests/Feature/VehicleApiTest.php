<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VehicleApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
    */

    public function test_can_list_all_vehicles()
    {
        $response = $this->get('/api/vehicles');

        $response->assertStatus(200);
        $response->assertJson([]); 
    }

    public function test_can_create_a_vehicle()
    {
        $vehicle = \App\Models\Vehicle::factory()->raw();

        $response = $this->postJson('/api/vehicles', $vehicle);

        $response->assertStatus(201)->assertJson($vehicle);

        $this->assertDatabaseHas('vehicles', $vehicle);
    }

    public function test_can_update_a_vehicle()
    {
        $vehicle = \App\Models\Vehicle::factory()->create();

        $updatedData = [
            'preco' => 330000,
            'cor' => 'Prata'
        ];

        $response = $this->putJson('/api/vehicles/' . $vehicle->id, $updatedData);

        $response->assertStatus(200)->assertJson($updatedData);
    }

    public function test_can_delete_a_vehicle()
    {
        $vehicle = \App\Models\Vehicle::factory()->create();

        $response = $this->deleteJson('/api/vehicles/' . $vehicle->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('vehicles', ['id' => $vehicle->id]);
    }
}