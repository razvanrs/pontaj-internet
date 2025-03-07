<?php

namespace App\Http\Controllers;

use App\Models\DayLimit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WorkingDaysController extends Controller
{
    public function calculate(Request $request, $year, $month)
    {
        // Romanian month names
        $romanianMonths = [
            1 => 'IANUARIE',
            2 => 'FEBRUARIE',
            3 => 'MARTIE',
            4 => 'APRILIE',
            5 => 'MAI',
            6 => 'IUNIE',
            7 => 'IULIE',
            8 => 'AUGUST',
            9 => 'SEPTEMBRIE',
            10 => 'OCTOMBRIE',
            11 => 'NOIEMBRIE',
            12 => 'DECEMBRIE'
        ];
        
        $monthName = $romanianMonths[(int)$month];
        
        // Get the date range for the month
        $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
        $endOfMonth = Carbon::create($year, $month)->endOfMonth()->endOfDay();
        
        // Fetch holiday events from the database
        $holidayEvents = DayLimit::whereBetween('start', [$startOfMonth, $endOfMonth])
            ->orWhereBetween('finish', [$startOfMonth, $endOfMonth])
            ->orWhere(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->where('start', '<=', $startOfMonth)
                    ->where('finish', '>=', $endOfMonth);
            })
            ->get();
        
        // Extract the dates that are holidays
        $holidayDates = [];
        foreach ($holidayEvents as $event) {
            $eventStart = Carbon::parse($event->start);
            $eventEnd = Carbon::parse($event->finish);
            
            $currentDate = $eventStart->copy();
            while ($currentDate->lte($eventEnd)) {
                if ($currentDate->year == $year && $currentDate->month == $month) {
                    $holidayDates[] = $currentDate->format('Y-m-d');
                }
                $currentDate->addDay();
            }
        }
        
        // Calculate working days for the month
        $totalDays = $endOfMonth->day;
        $workingDays = 0;
        
        for ($day = 1; $day <= $totalDays; $day++) {
            $currentDate = Carbon::create($year, $month, $day);
            
            // A day is a working day if:
            // 1. It's not a weekend (Saturday or Sunday)
            // 2. It's not in our list of holidays
            if (!$currentDate->isWeekend() && !in_array($currentDate->format('Y-m-d'), $holidayDates)) {
                $workingDays++;
            }
        }
        
        // Calculate working hours (8 hours per working day)
        $workingHours = $workingDays * 8;
        
        // Return the result
        return response()->json([
            'month' => $monthName,
            'year' => (int)$year,
            'working_days' => $workingDays,
            'working_hours' => $workingHours
        ]);
    }

    /**
     * Fetch holidays from API and store them in the database
     * This method is separate from the calculation for the report page
     */
    public function fetchHolidays($year)
    {
        try {
            // Call the external API
            $url = "https://zilelibere.webventure.ro/api-day-counter/{$year}";
            $response = Http::timeout(10)->get($url);
            
            if ($response->successful()) {
                $data = $response->json();
                $importedCount = 0;
                $updatedCount = 0;
                
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
                                
                                // Check if a holiday with this name already exists in this date range
                                $existingHoliday = DayLimit::where('name', $name)
                                    ->where(function($query) use ($startDate, $endDate) {
                                        // Check for any overlap
                                        $query->whereBetween('start', [$startDate, $endDate])
                                            ->orWhereBetween('finish', [$startDate, $endDate])
                                            ->orWhere(function($q) use ($startDate, $endDate) {
                                                $q->where('start', '<=', $startDate)
                                                  ->where('finish', '>=', $endDate);
                                            });
                                    })
                                    ->first();
                                
                                if (!$existingHoliday) {
                                    // Create new entry
                                    DayLimit::create([
                                        'name' => $name,
                                        'start' => $startDate->format('Y-m-d'),
                                        'finish' => $endDate->format('Y-m-d'),
                                    ]);
                                    $importedCount++;
                                } else if ($existingHoliday->start->format('Y-m-d') != $startDate->format('Y-m-d') || 
                                           $existingHoliday->finish->format('Y-m-d') != $endDate->format('Y-m-d')) {
                                    // Update if dates have changed
                                    $existingHoliday->start = $startDate->format('Y-m-d');
                                    $existingHoliday->finish = $endDate->format('Y-m-d');
                                    $existingHoliday->save();
                                    $updatedCount++;
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
                                }
                            }
                        }
                    }
                }
                
                return response()->json([
                    'success' => true,
                    'message' => "Import completed. Added {$importedCount} new holidays and updated {$updatedCount} existing holidays in the database."
                ]);
                
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Failed to fetch holidays: " . $response->status()
                ], 500);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Error fetching holidays: " . $e->getMessage()
            ], 500);
        }
    }
}