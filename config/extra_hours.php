<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Extra Hours Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for extra hours calculation rules.
    |
    */

    // Regular working hours (24-hour format)
    'regular_hours' => [
        'start' => 8,  // 8 AM
        'end' => 16,   // 4 PM
    ],

    // Maximum continuous work period in hours before requiring a break
    'max_continuous_hours' => 24,

    // Segment size for splitting long shifts (in hours)
    'segment_size' => 8,

    // Days of the week considered as weekends (0 = Sunday, 6 = Saturday)
    'weekend_days' => [0, 6],

    // Extra hours expiry period in days
    'expiry_days' => 90,

    // Default extra hours status
    'default_status' => 'available',
];
