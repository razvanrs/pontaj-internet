<?php

namespace App\Console\Commands;

use App\Models\DayLimit;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchRomanianHolidaysCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-romanian-holidays {year? : The year to fetch holidays for (defaults to current year)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Romanian holidays from API and store them in day_limits table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the year from command argument or use current year
        $year = $this->argument('year') ?: now()->year;
        
        $this->info("Fetching Romanian holidays for {$year}...");
        
        try {
            // Call the external API with the correct endpoint
            $url = "https://zilelibere.webventure.ro/api-day-counter/{$year}";
            $response = Http::timeout(10)->get($url);
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Count for imported holidays
                $importedCount = 0;
                
                // Process each holiday category
                foreach ($data as $holidayGroup) {
                    $name = $holidayGroup['name'] ?? 'Zi liberă';
                    
                    // Process each date in this holiday group
                    if (isset($holidayGroup['date']) && is_array($holidayGroup['date'])) {
                        // Handle multi-day holidays
                        $dates = [];
                        foreach ($holidayGroup['date'] as $dateInfo) {
                            if (isset($dateInfo['date'])) {
                                $dates[] = Carbon::createFromFormat('Y/m/d', $dateInfo['date']);
                            }
                        }
                        
                        // Sort dates chronologically
                        usort($dates, function($a, $b) {
                            return $a->getTimestamp() - $b->getTimestamp();
                        });
                        
                        if (count($dates) > 0) {
                            // If it's a multi-day holiday, use start-end approach
                            if (count($dates) > 1) {
                                $startDate = $dates[0];
                                $endDate = $dates[count($dates) - 1];
                                
                                // Check if holiday already exists
                                $existingHoliday = DayLimit::where('name', $name)
                                    ->whereDate('start', $startDate)
                                    ->whereDate('finish', $endDate)
                                    ->first();
                                
                                if (!$existingHoliday) {
                                    // Create entry
                                    DayLimit::create([
                                        'name' => $name,
                                        'start' => $startDate->format('Y-m-d'),
                                        'finish' => $endDate->format('Y-m-d'),
                                    ]);
                                    
                                    $importedCount++;
                                    $this->info("Added holiday: {$name} from {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}");
                                } else {
                                    $this->line("Holiday already exists: {$name} from {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}");
                                }
                            } else {
                                // Single day holiday
                                $holidayDate = $dates[0];
                                
                                // Check if holiday already exists
                                $existingHoliday = DayLimit::where('name', $name)
                                    ->whereDate('start', $holidayDate)
                                    ->whereDate('finish', $holidayDate)
                                    ->first();
                                
                                if (!$existingHoliday) {
                                    // Create entry
                                    DayLimit::create([
                                        'name' => $name,
                                        'start' => $holidayDate->format('Y-m-d'),
                                        'finish' => $holidayDate->format('Y-m-d'),
                                    ]);
                                    
                                    $importedCount++;
                                    $this->info("Added holiday: {$name} on {$holidayDate->format('Y-m-d')}");
                                } else {
                                    $this->line("Holiday already exists: {$name} on {$holidayDate->format('Y-m-d')}");
                                }
                            }
                        }
                    }
                }
                
                $this->info("Import completed. Added {$importedCount} new holidays to the database.");
                
            } else {
                $this->error("Failed to fetch holidays: " . $response->status());
                return Command::FAILURE;
            }
            
        } catch (\Exception $e) {
            $this->error("Error fetching holidays: " . $e->getMessage());
            Log::error("Error fetching holidays: " . $e->getMessage());
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }
}