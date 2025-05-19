# Additional Wines Installation Guide

This guide will help you add additional wine data to your Wine Recommender application with prices in Philippine Peso (PHP).

## Overview

The additional wine dataset includes:
- 12 new wines with verified sources and links
- Conversion of all existing prices from USD to PHP
- Documentation of pricing and sources

## Prerequisites

- Laravel application installed and working
- Database connection configured and working
- Basic knowledge of Laravel commands

## Installation Steps

### 1. Run the Custom Artisan Command

The simplest way to add the new wines is to run the custom Artisan command:

```bash
php artisan seed:additional-wines
```

This command will:
- Convert all existing wine prices from USD to PHP
- Add 12 new wines with prices already in PHP
- Provide feedback on the process

### 2. Alternative: Run the Seeder Directly

If you prefer to run the seeder directly:

```bash
php artisan db:seed --class=Database\\Seeders\\AdditionalWinesSeeder
```

### 3. Verify the Installation

After running the command, you should:

1. Check the admin wine listing page to confirm:
   - Existing wine prices now show in PHP (₱)
   - New wines have been added to the database

2. View the documentation:
   - Review `database/seeders/wine_sources.md` for details on all added wines and their verified sources

## What's Included

The additional wines dataset includes:

1. **Don Papa Rum** - A Philippine rum from Negros (₱1,999.00)
2. **Chateau Ste. Michelle Riesling** - White wine from USA (₱845.00)
3. **Campo Viejo Rioja Reserva** - Red wine from Spain (₱1,130.00)
4. **Matua Sauvignon Blanc** - White wine from New Zealand (₱790.00)
5. **Barefoot Moscato** - Sweet white wine from USA (₱565.00)
6. **Marchesi Antinori Chianti Classico Riserva** - Italian red wine (₱2,825.00)
7. **Jansz Premium Cuvée** - Australian sparkling wine (₱1,695.00)
8. **Planeta Etna Rosso** - Sicilian red wine (₱2,260.00)
9. **Yellow Tail Shiraz** - Australian red wine (₱620.00)
10. **Dr. Loosen Riesling Kabinett** - German white wine (₱1,980.00)
11. **Carlo Rossi Sweet Red** - Affordable red wine (₱450.00)
12. **Bodegas Muga Rioja Reserva** - Spanish red wine (₱2,599.00)

## Currency Conversion

The conversion uses an exchange rate of approximately:
- 1 USD = ₱56.55 PHP

Source: [Bangko Sentral ng Pilipinas](https://www.bsp.gov.ph/)

## Troubleshooting

If you encounter any issues during installation:

1. Check your database connection settings
2. Make sure you have proper permissions
3. Verify that the Wine model is correctly defined
4. Look for any error messages in the output

For detailed error information, check the Laravel log file:
```
storage/logs/laravel.log
```

## Further Information

For more details on the wine sources and verified links, refer to:
`database/seeders/wine_sources.md` 