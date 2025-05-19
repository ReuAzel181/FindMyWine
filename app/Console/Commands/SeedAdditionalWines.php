<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SeedAdditionalWines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:additional-wines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed additional wines with Philippine Peso prices';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to seed additional wines and convert prices to PHP...');
        
        try {
            // Run the specific seeder
            Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\AdditionalWinesSeeder',
            ]);
            
            $this->info('Successfully added additional wines and converted prices to PHP!');
            $this->info('Check database/seeders/wine_sources.md for details on wine sources and links.');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error seeding additional wines: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
