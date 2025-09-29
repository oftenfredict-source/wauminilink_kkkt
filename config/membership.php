<?php

return [
    // Maximum age for a child before auto-conversion to independent person
    'child_max_age' => env('MEMBERSHIP_CHILD_MAX_AGE', 18),
    // Optional reference date mode for age calculations (e.g., 'today', 'end_of_year')
    'age_reference' => env('MEMBERSHIP_AGE_REFERENCE', 'today'),
];


