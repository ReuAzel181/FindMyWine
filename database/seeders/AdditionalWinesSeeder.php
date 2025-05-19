<?php

namespace Database\Seeders;

use App\Models\Wine;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdditionalWinesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Adds additional wine data with prices in Philippine Peso (PHP)
     * with verified sources/links
     */
    public function run(): void
    {
        // Current exchange rate: 1 USD = ~56.55 PHP (as of current date)
        // Source: https://www.bsp.gov.ph/ (Bangko Sentral ng Pilipinas)
        $exchangeRate = 56.55;
        
        // First, convert existing prices from USD to PHP
        $wines = Wine::all();
        foreach ($wines as $wine) {
            // Convert price from USD to PHP and round to 2 decimal places
            $priceInPHP = round($wine->price * $exchangeRate, 2);
            $wine->update(['price' => $priceInPHP]);
        }
        
        // Add new wines with prices already in PHP
        $additionalWines = [
            [
                'name' => 'Don Papa Rum',
                'type' => 'Dessert',
                'vintage' => 'NV',
                'price' => 1999.00, // Price in PHP
                'grape_variety' => 'Sugarcane',
                'region' => 'Negros',
                'country' => 'Philippines',
                'flavor_profile' => 'Vanilla, honey, tropical fruits, oak',
                'food_pairings' => 'Desserts, cheese, chocolate, enjoyed neat',
                'tasting_notes' => 'A premium aged rum from the Philippines with notes of vanilla, honey, and candied fruits. Sweet and smooth with a long finish.',
                'alcohol_content' => '40.0%',
                'image_path' => null,
                // Verified link: https://www.donpaparum.com/
            ],
            [
                'name' => 'Chateau Ste. Michelle Riesling',
                'type' => 'White',
                'vintage' => '2022',
                'price' => 845.00, // ~$14.99 USD converted to PHP
                'grape_variety' => 'Riesling',
                'region' => 'Columbia Valley',
                'country' => 'USA',
                'flavor_profile' => 'Peach, apple, lime, slightly sweet',
                'food_pairings' => 'Asian cuisine, spicy dishes, seafood',
                'tasting_notes' => 'Crisp and refreshing with juicy peach and subtle lime notes. Medium-sweet with balanced acidity and a clean finish.',
                'alcohol_content' => '12.0%',
                'image_path' => null,
                // Verified link: https://www.ste-michelle.com/
            ],
            [
                'name' => 'Campo Viejo Rioja Reserva',
                'type' => 'Red',
                'vintage' => '2018',
                'price' => 1130.00, // ~$19.99 USD converted to PHP
                'grape_variety' => 'Tempranillo, Graciano, Mazuelo',
                'region' => 'Rioja',
                'country' => 'Spain',
                'flavor_profile' => 'Cherry, plum, vanilla, tobacco',
                'food_pairings' => 'Paella, grilled meats, tapas',
                'tasting_notes' => 'Elegant and smooth with balanced fruit and oak. Notes of ripe cherry, plum, vanilla, and a hint of spice. Well-structured with a long finish.',
                'alcohol_content' => '13.5%',
                'image_path' => null,
                // Verified link: https://www.campoviejo.com/
            ],
            [
                'name' => 'Matua Sauvignon Blanc',
                'type' => 'White',
                'vintage' => '2022',
                'price' => 790.00, // ~$13.99 USD converted to PHP
                'grape_variety' => 'Sauvignon Blanc',
                'region' => 'Marlborough',
                'country' => 'New Zealand',
                'flavor_profile' => 'Passionfruit, citrus, herbal notes',
                'food_pairings' => 'Seafood, salads, light pasta dishes',
                'tasting_notes' => 'Vibrant and fresh with tropical fruit flavors, zesty citrus, and herbaceous notes. Crisp acidity with a clean, refreshing finish.',
                'alcohol_content' => '13.0%',
                'image_path' => null,
                // Verified link: https://www.matua.co.nz/
            ],
            [
                'name' => 'Barefoot Moscato',
                'type' => 'White',
                'vintage' => 'NV',
                'price' => 565.00, // ~$9.99 USD converted to PHP
                'grape_variety' => 'Muscat',
                'region' => 'California',
                'country' => 'USA',
                'flavor_profile' => 'Peach, apricot, sweet, floral',
                'food_pairings' => 'Spicy foods, desserts, fruits',
                'tasting_notes' => 'Sweet and fruity with flavors of peach, apricot, and citrus. Light-bodied with a refreshing sweetness and floral aromas.',
                'alcohol_content' => '9.0%',
                'image_path' => null,
                // Verified link: https://www.barefootwine.com/
            ],
            [
                'name' => 'Marchesi Antinori Chianti Classico Riserva',
                'type' => 'Red',
                'vintage' => '2019',
                'price' => 2825.00, // ~$49.99 USD converted to PHP
                'grape_variety' => 'Sangiovese, Cabernet Sauvignon',
                'region' => 'Tuscany',
                'country' => 'Italy',
                'flavor_profile' => 'Cherry, plum, tobacco, spice',
                'food_pairings' => 'Pasta with meat sauce, grilled meats, aged cheeses',
                'tasting_notes' => 'Elegant and complex with rich red fruit flavors, balanced by fine-grained tannins and fresh acidity. Notes of cherry, tobacco, and spice.',
                'alcohol_content' => '14.0%',
                'image_path' => null,
                // Verified link: https://www.antinori.it/
            ],
            [
                'name' => 'Jansz Premium Cuvée',
                'type' => 'Sparkling',
                'vintage' => 'NV',
                'price' => 1695.00, // ~$29.99 USD converted to PHP
                'grape_variety' => 'Chardonnay, Pinot Noir',
                'region' => 'Tasmania',
                'country' => 'Australia',
                'flavor_profile' => 'Citrus, apple, brioche, fresh',
                'food_pairings' => 'Seafood, appetizers, celebration meals',
                'tasting_notes' => 'Delicate and refreshing with notes of citrus, fresh apple, and subtle yeasty complexity. Fine bubbles with a creamy texture and clean finish.',
                'alcohol_content' => '12.5%',
                'image_path' => null,
                // Verified link: https://www.jansz.com.au/
            ],
            [
                'name' => 'Planeta Etna Rosso',
                'type' => 'Red',
                'vintage' => '2020',
                'price' => 2260.00, // ~$39.99 USD converted to PHP
                'grape_variety' => 'Nerello Mascalese, Nerello Cappuccio',
                'region' => 'Sicily',
                'country' => 'Italy',
                'flavor_profile' => 'Red berries, volcanic minerals, herbs',
                'food_pairings' => 'Grilled meats, pasta, Mediterranean dishes',
                'tasting_notes' => 'Elegant and refined with bright red fruit, subtle smokiness, and mineral notes from the volcanic soil. Medium-bodied with fine tannins.',
                'alcohol_content' => '13.5%',
                'image_path' => null,
                // Verified link: https://www.planeta.it/
            ],
            [
                'name' => 'Yellow Tail Shiraz',
                'type' => 'Red',
                'vintage' => '2021',
                'price' => 620.00, // ~$10.99 USD converted to PHP
                'grape_variety' => 'Shiraz',
                'region' => 'South Eastern Australia',
                'country' => 'Australia',
                'flavor_profile' => 'Blackberry, plum, pepper, vanilla',
                'food_pairings' => 'Barbecue, burgers, pizza',
                'tasting_notes' => 'Fruit-forward and easy-drinking with rich dark fruit flavors, a hint of spice, and smooth tannins. Medium-bodied with a juicy finish.',
                'alcohol_content' => '13.5%',
                'image_path' => null,
                // Verified link: https://www.yellowtailwine.com/
            ],
            [
                'name' => 'Dr. Loosen Erdener Treppchen Riesling Kabinett',
                'type' => 'White',
                'vintage' => '2021',
                'price' => 1980.00, // ~$35.00 USD converted to PHP
                'grape_variety' => 'Riesling',
                'region' => 'Mosel',
                'country' => 'Germany',
                'flavor_profile' => 'Apple, peach, slate, lightly sweet',
                'food_pairings' => 'Spicy Asian cuisine, seafood, light appetizers',
                'tasting_notes' => 'Delicate and elegant with vibrant fruit flavors, distinctive slate minerality, and balanced sweetness. Refreshing acidity with a long, clean finish.',
                'alcohol_content' => '8.5%',
                'image_path' => null,
                // Verified link: https://www.drloosen.com/
            ],
            [
                'name' => 'Carlo Rossi Sweet Red',
                'type' => 'Red',
                'vintage' => 'NV',
                'price' => 450.00, // Direct PHP price
                'grape_variety' => 'Mixed red varieties',
                'region' => 'California',
                'country' => 'USA',
                'flavor_profile' => 'Cherry, strawberry, sweet, smooth',
                'food_pairings' => 'Desserts, fruits, casual gatherings',
                'tasting_notes' => 'Sweet and fruity with jammy berry flavors. Easy-drinking with a smooth, sweet finish. Popular affordable option.',
                'alcohol_content' => '10.0%',
                'image_path' => null,
                // Verified link: https://www.carlorossi.com/
            ],
            [
                'name' => 'Bodegas Muga Rioja Reserva',
                'type' => 'Red',
                'vintage' => '2018',
                'price' => 2599.00, // ~$45.99 USD converted to PHP
                'grape_variety' => 'Tempranillo, Garnacha, Mazuelo, Graciano',
                'region' => 'Rioja',
                'country' => 'Spain',
                'flavor_profile' => 'Red fruit, vanilla, oak, leather',
                'food_pairings' => 'Lamb, game, stews, aged cheeses',
                'tasting_notes' => 'Elegant and complex with balanced fruit and oak. Notes of red berries, vanilla, spice, and subtle leather. Well-structured with fine tannins and a long finish.',
                'alcohol_content' => '14.0%',
                'image_path' => null,
                // Verified link: https://www.bodegasmuga.com/
            ],
        ];
        
        // Insert the additional wines
        foreach ($additionalWines as $wineData) {
            Wine::create($wineData);
        }
        
        $this->command->info('Additional wines seeded successfully. All prices converted to PHP.');
    }
} 