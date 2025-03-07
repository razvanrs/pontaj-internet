<?php

namespace App\Data;

use App\Models\DayLimit;
use Carbon\Carbon;
use Spatie\LaravelData\Data;

class DayLimitData extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public string $start,
        public string $end,
        public string $backgroundColor,
        public string $borderColor,
        public string $allDay,
    ) {
    }

    public static function fromModel(DayLimit $dayLimit): self
    {
        // Add one day to make the end date inclusive for FullCalendar display
        $endDate = Carbon::parse($dayLimit->finish)->addDay()->format('Y-m-d');
        
        return new self(
            $dayLimit->id,
            $dayLimit->name,
            $dayLimit->start->format("Y-m-d"),
            $endDate, // Use the modified end date for display
            '#6466e9',
            '#6466e9',
            'true' 
        );
    }
}