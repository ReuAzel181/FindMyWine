<?php

namespace App\Services;

use App\Models\Wine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class WineDatasetService
{
    /**
     * Import wines from a CSV file into the database
     *
     * @param string $filePath Path to the CSV file
     * @param bool $clearExisting Whether to clear existing wines before import
     * @return array Import statistics
     */
    public function importWinesFromCSV(string $filePath, bool $clearExisting = false): array
    {
        $stats = [
            'imported' => 0,
            'skipped' => 0,
            'errors' => 0,
        ];
        
        if (!Storage::exists($filePath)) {
            Log::error("Wine dataset file not found: {$filePath}");
            return $stats;
        }
        
        // Optionally clear existing wines
        if ($clearExisting) {
            Wine::truncate();
        }
        
        $file = Storage::get($filePath);
        $lines = explode("\n", $file);
        
        // Get header row and remove it
        $header = str_getcsv(array_shift($lines));
        $headerMap = $this->mapCSVHeadersToFields($header);
        
        DB::beginTransaction();
        
        try {
            foreach ($lines as $line) {
                if (empty(trim($line))) {
                    continue; // Skip empty lines
                }
                
                $row = str_getcsv($line);
                $wineData = [];
                
                // Map CSV data to wine fields
                foreach ($headerMap as $columnIndex => $fieldName) {
                    if (isset($row[$columnIndex])) {
                        $wineData[$fieldName] = $row[$columnIndex];
                    }
                }
                
                // Skip if missing required fields
                if (!isset($wineData['name']) || empty($wineData['name'])) {
                    $stats['skipped']++;
                    continue;
                }
                
                try {
                    Wine::updateOrCreate(
                        ['name' => $wineData['name'], 'vintage' => $wineData['vintage'] ?? null],
                        $wineData
                    );
                    
                    $stats['imported']++;
                } catch (\Exception $e) {
                    Log::error("Error importing wine: " . $e->getMessage());
                    $stats['errors']++;
                }
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error during wine import: " . $e->getMessage());
            throw $e;
        }
        
        return $stats;
    }
    
    /**
     * Export all wines to a CSV file
     *
     * @return string Path to the exported file
     */
    public function exportWinesToCSV(): string
    {
        $fileName = 'wine_dataset_' . now()->format('Ymd_His') . '.csv';
        $filePath = 'exports/' . $fileName;
        
        $wines = Wine::all();
        $headers = [
            'id', 'name', 'type', 'vintage', 'price', 'grape_variety', 
            'region', 'country', 'flavor_profile', 'food_pairings', 
            'tasting_notes', 'alcohol_content', 'image_path'
        ];
        
        $csvContent = implode(',', $headers) . "\n";
        
        foreach ($wines as $wine) {
            $row = [];
            foreach ($headers as $header) {
                $value = $wine->$header;
                
                // Format arrays as semicolon-separated strings
                if (is_array($value)) {
                    $value = implode(';', $value);
                }
                
                // Escape commas and quotes
                $value = str_replace('"', '""', $value);
                if (strpos($value, ',') !== false) {
                    $value = '"' . $value . '"';
                }
                
                $row[] = $value;
            }
            
            $csvContent .= implode(',', $row) . "\n";
        }
        
        Storage::put($filePath, $csvContent);
        
        return $filePath;
    }
    
    /**
     * Map CSV headers to database fields
     *
     * @param array $headers
     * @return array
     */
    private function mapCSVHeadersToFields(array $headers): array
    {
        $map = [];
        $standardizedHeaders = [];
        
        // Standardize header names (lowercase, remove spaces)
        foreach ($headers as $index => $header) {
            $standardizedHeaders[$index] = strtolower(str_replace(' ', '_', trim($header)));
        }
        
        // Define mapping of expected headers to database fields
        $fieldMappings = [
            'name' => ['name', 'wine_name', 'title'],
            'type' => ['type', 'wine_type', 'category'],
            'vintage' => ['vintage', 'year'],
            'price' => ['price', 'cost', 'retail_price'],
            'grape_variety' => ['grape_variety', 'grape', 'varietal', 'grapes'],
            'region' => ['region', 'wine_region'],
            'country' => ['country', 'origin', 'country_of_origin'],
            'flavor_profile' => ['flavor_profile', 'flavor', 'flavors', 'profile'],
            'food_pairings' => ['food_pairings', 'food_pairing', 'pairing', 'pairings'],
            'tasting_notes' => ['tasting_notes', 'notes', 'description'],
            'alcohol_content' => ['alcohol_content', 'alcohol', 'abv'],
            'image_path' => ['image_path', 'image', 'picture']
        ];
        
        // Map each header to a database field
        foreach ($standardizedHeaders as $index => $header) {
            foreach ($fieldMappings as $fieldName => $possibleHeaders) {
                if (in_array($header, $possibleHeaders)) {
                    $map[$index] = $fieldName;
                    break;
                }
            }
            
            // If no mapping found, use the standardized header as is
            if (!isset($map[$index])) {
                $map[$index] = $header;
            }
        }
        
        return $map;
    }
    
    /**
     * Get available wine attributes for filtering
     *
     * @return array
     */
    public function getWineAttributes(): array
    {
        return [
            'types' => Wine::distinct()->pluck('type')->filter()->values()->toArray(),
            'regions' => Wine::distinct()->pluck('region')->filter()->values()->toArray(),
            'countries' => Wine::distinct()->pluck('country')->filter()->values()->toArray(),
            'grapes' => Wine::distinct()->pluck('grape_variety')->filter()->values()->toArray(),
            'price_range' => [
                'min' => Wine::min('price'),
                'max' => Wine::max('price')
            ]
        ];
    }
} 