<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use Illuminate\Console\Command;

class ImportVehiclesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Aceita um argumento opcional "file" para o caminho do JSON
     */
    protected $signature = 'import:vehicles {file?}';

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
        $filePath = $this->argument('file') ?? storage_path('app/dados_simulados.json');

        if (!file_exists($filePath)) {
            $this->error("Arquivo JSON não encontrado em: {$filePath}");
            return Command::FAILURE;
        }

        $jsonContent = file_get_contents($filePath);
        $vehicles = json_decode($jsonContent, true);

        if (empty($vehicles)) {
            $this->error('Nenhum dado de veículo encontrado no JSON.');
            return Command::FAILURE;
        }

        $this->info('Importação de veículos iniciada...');
        $importedCount = 0;

        foreach ($vehicles as $vehicleData) {
            $vehicle = Vehicle::updateOrCreate(
                ['titulo' => $vehicleData['titulo']], // Comparar pelo título
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
