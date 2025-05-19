<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wine;
use App\Services\WineDatasetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class WineDatasetController extends Controller
{
    protected $wineDatasetService;
    
    public function __construct(WineDatasetService $wineDatasetService)
    {
        $this->wineDatasetService = $wineDatasetService;
    }
    
    /**
     * Display the dataset management page
     */
    public function index()
    {
        $wineCount = Wine::count();
        return view('admin.dataset.index', [
            'wineCount' => $wineCount
        ]);
    }
    
    /**
     * Show the import form
     */
    public function importForm(Request $request)
    {
        $type = $request->query('type', 'standard');
        $title = $type === 'additional' ? 'Import Additional Wines' : 'Import Wine Dataset';
        $description = $type === 'additional' 
            ? 'Upload additional wines to expand the dataset. This will add wines to the existing database.'
            : 'Upload a CSV file containing wine data. This will replace all existing wines.';
            
        return view('admin.dataset.import', compact('type', 'title', 'description'));
    }
    
    /**
     * Process the import
     */
    public function importWines(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
            'import_type' => 'required|in:standard,additional',
        ]);
        
        try {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();
            $records = array_map('str_getcsv', file($path));
            
            // Get headers
            $headers = array_shift($records);
            $headers = array_map('trim', $headers);
            $headers = array_map('strtolower', $headers);
            
            // Check required headers
            $requiredHeaders = ['name', 'type', 'price'];
            foreach ($requiredHeaders as $requiredHeader) {
                if (!in_array($requiredHeader, $headers)) {
                    return back()
                        ->with('error', "CSV file is missing required header: {$requiredHeader}")
                        ->withInput();
                }
            }
            
            // Clear existing wines if requested (only for standard import)
            if ($request->import_type === 'standard' && $request->has('clear_existing')) {
                Wine::truncate();
            }
            
            // Process records
            $count = 0;
            foreach ($records as $record) {
                if (count($record) !== count($headers)) {
                    continue; // Skip invalid records
                }
                
                $wineData = array_combine($headers, $record);
                
                // Skip empty rows
                if (empty(trim($wineData['name']))) {
                    continue;
                }
                
                // Check if wine already exists for additional imports
                if ($request->import_type === 'additional') {
                    $existingWine = Wine::where('name', $wineData['name'])->first();
                    if ($existingWine) {
                        continue; // Skip existing wines in additional import mode
                    }
                }
                
                // Create new wine
                $wine = new Wine();
                $wine->name = trim($wineData['name']);
                $wine->type = trim($wineData['type'] ?? '');
                $wine->variety = trim($wineData['variety'] ?? '');
                $wine->origin = trim($wineData['origin'] ?? '');
                $wine->price = floatval(str_replace(['₱', ','], '', $wineData['price'] ?? 0));
                $wine->flavor_profile = trim($wineData['flavor_profile'] ?? '');
                $wine->food_pairings = trim($wineData['food_pairings'] ?? '');
                $wine->description = trim($wineData['description'] ?? '');
                $wine->image_url = trim($wineData['image_url'] ?? '');
                $wine->source_url = trim($wineData['source_url'] ?? '');
                
                $wine->save();
                $count++;
            }
            
            $message = $request->import_type === 'additional' 
                ? "{$count} additional wines imported successfully."
                : "{$count} wines imported successfully.";
                
            return redirect()->route('admin.dataset.list')->with('success', $message);
            
        } catch (\Exception $e) {
            return back()
                ->with('error', "Error importing wines: " . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Export wines to CSV
     */
    public function exportWines()
    {
        $filePath = $this->wineDatasetService->exportWinesToCSV();
        return Storage::download($filePath);
    }
    
    /**
     * Show the wine list for manual editing
     */
    public function listWines(Request $request)
    {
        $query = Wine::query();
        
        // Handle search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('type', 'LIKE', "%{$search}%")
                  ->orWhere('grape_variety', 'LIKE', "%{$search}%")
                  ->orWhere('region', 'LIKE', "%{$search}%");
            });
        }
        
        // Handle sorting
        $sortField = $request->sort_by ?? 'name';
        $sortDirection = $request->sort_dir ?? 'asc';
        $query->orderBy($sortField, $sortDirection);
        
        $wines = $query->paginate(20);
        
        return view('admin.dataset.list', [
            'wines' => $wines,
            'search' => $request->search,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection
        ]);
    }
    
    /**
     * Show the form to edit a wine
     */
    public function editWine($id)
    {
        $wine = Wine::findOrFail($id);
        return view('admin.dataset.edit', [
            'wine' => $wine
        ]);
    }
    
    /**
     * Update a wine
     */
    public function updateWine(Request $request, $id)
    {
        $wine = Wine::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'vintage' => 'nullable|string|max:10',
            'price' => 'nullable|numeric|min:0',
            'grape_variety' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'flavor_profile' => 'nullable|string',
            'food_pairings' => 'nullable|string',
            'tasting_notes' => 'nullable|string',
            'alcohol_content' => 'nullable|string|max:20',
            'image_path' => 'nullable|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $wine->update($request->all());
        
        return redirect()->route('admin.dataset.list')
            ->with('success', 'Wine updated successfully');
    }
    
    /**
     * Show the form to add a new wine
     */
    public function createWine()
    {
        return view('admin.dataset.create');
    }
    
    /**
     * Store a new wine
     */
    public function storeWine(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'vintage' => 'nullable|string|max:10',
            'price' => 'nullable|numeric|min:0',
            'grape_variety' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'flavor_profile' => 'nullable|string',
            'food_pairings' => 'nullable|string',
            'tasting_notes' => 'nullable|string',
            'alcohol_content' => 'nullable|string|max:20',
            'image_path' => 'nullable|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        Wine::create($request->all());
        
        return redirect()->route('admin.dataset.list')
            ->with('success', 'Wine added successfully');
    }
    
    /**
     * Delete a wine
     */
    public function deleteWine($id)
    {
        $wine = Wine::findOrFail($id);
        $wine->delete();
        
        return redirect()->route('admin.dataset.list')
            ->with('success', 'Wine deleted successfully');
    }
} 