<?php

namespace Tests\Feature;

use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ImportVehiclesCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_vehicles_creates_and_updates_records()
    {
        $vehiclesData = [
            [
                "titulo" => "Nissan 350Z Roadster",
                "marca" => "Nissan",
                "modelo" => "350Z",
                "ano" => 2008,
                "preco" => 85000,
                "cor" => "Vermelho",
                "combustivel" => "Gasolina",
                "url_imagem" => "https://exemplo.com/imagem.jpg"
            ]
        ];

        $jsonPath = storage_path('app/test_vehicles.json');
        file_put_contents($jsonPath, json_encode($vehiclesData));

        $exitCode = Artisan::call('import:vehicles', ['file' => $jsonPath]);

        $this->assertEquals(0, $exitCode, "O comando deve retornar sucesso (0)");

        $this->assertDatabaseHas('vehicles', [
            'titulo' => 'Nissan 350Z Roadster',
            'marca' => 'Nissan',
        ]);

        $vehiclesData[0]['preco'] = 90000;
        file_put_contents($jsonPath, json_encode($vehiclesData));

        Artisan::call('import:vehicles', ['file' => $jsonPath]);

        $this->assertDatabaseHas('vehicles', [
            'titulo' => 'Nissan 350Z Roadster',
            'preco' => 90000,
        ]);
    }
}
