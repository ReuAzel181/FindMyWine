<?php

namespace Database\Seeders;

use App\Models\Wine;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class WineSeeder extends Seeder
{
    public function run(): void
    {
        $csvFile = file_get_contents(__DIR__ . '/sample_wines.csv');
        $lines = explode("\n", $csvFile);
        
        // Remove header row
        $header = str_getcsv(array_shift($lines));
        
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            
            $data = str_getcsv($line);
            if (count($data) !== count($header)) continue;
            
            $wineData = array_combine($header, $data);
            
            Wine::create([
                'name' => $wineData['name'],
                'type' => $wineData['type'],
                'vintage' => $wineData['vintage'],
                'price' => $wineData['price'],
                'grape_variety' => $wineData['grape_variety'],
                'region' => $wineData['region'],
                'country' => $wineData['country'],
                'flavor_profile' => $wineData['flavor_profile'],
                'food_pairings' => $wineData['food_pairings'],
                'tasting_notes' => $wineData['tasting_notes'],
                'alcohol_content' => $wineData['alcohol_content'],
            ]);
        }
    }
} 