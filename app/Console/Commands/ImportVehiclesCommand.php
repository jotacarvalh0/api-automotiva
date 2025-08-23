<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use Illuminate\Console\Command;

class ImportVehiclesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:vehicles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa e atualiza a base de dados de veículos a partir de um JSON.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $jsonPath = storage_path('app/dados_simulados.json');
        $jsonContent = file_get_contents($jsonPath);
        $vehicles = json_decode($jsonContent, true);

        if (empty($vehicles)) {
            $this->error('Nenhum dado de veículo encontrado no JSON.');
            return Command::FAILURE;
        }

        $this->info('Importação de veículo iniciada...');
        $importedCount = 0;

        foreach ($vehicles as $vehicleData) {
            $vehicle = Vehicle::updateOrCreate(
                ['titulo' => $vehicleData['titulo']], // Verificar qual seria o melhor parametro de comparação.
                $vehicleData
            );

            if ($vehicle->wasRecentlyCreated) {
                $this->info("Veículo criado: {$vehicle->titulo} (ID: {$vehicle->id})");
            } else {
                $this->info("Veículo atualizado: {$vehicle->titulo} (ID: {$vehicle->id})");
            }

            $importedCount++;
        }
        
        $this->info("Importação concluída. {$importedCount} veículos processados.");
        return Command::SUCCESS;
    }
}
