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
    public function importForm()
    {
        return view('admin.dataset.import');
    }
    
    /**
     * Import wines from CSV
     */
    public function importWines(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wine_file' => 'required|file|mimes:csv,txt',
            'clear_existing' => 'nullable|boolean',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        
        $file = $request->file('wine_file');
        $path = $file->store('imports');
        
        $clearExisting = $request->has('clear_existing') && $request->clear_existing;
        
        try {
            $stats = $this->wineDatasetService->importWinesFromCSV($path, $clearExisting);
            
            return redirect()->route('admin.dataset.index')
                ->with('success', "Import completed: {$stats['imported']} wines imported, {$stats['skipped']} skipped, {$stats['errors']} errors");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Import failed: ' . $e->getMessage());
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