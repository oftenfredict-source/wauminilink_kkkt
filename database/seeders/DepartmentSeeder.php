<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $departments = [
            [
                'name' => 'Department of Youth (Idara ya Vijana)',
                'description' => 'For church members aged between 12 and 34 years.',
                'criteria' => [
                    'min_age' => 12,
                    'max_age' => 34,
                    'membership_status' => 'registered', // Implied by being a member
                ],
            ],
            [
                'name' => 'Department of Women (Kinamama)',
                'description' => 'For married female members aged 35+ with children.',
                'criteria' => [
                    'gender' => 'female',
                    'min_age' => 35,
                    'marital_status' => 'married',
                    'has_children' => true,
                ],
            ],
            [
                'name' => 'Department of Men (Kinababa)',
                'description' => 'For married male members aged 35+ with children.',
                'criteria' => [
                    'gender' => 'male',
                    'min_age' => 35,
                    'marital_status' => 'married',
                    'has_children' => true,
                ],
            ],
        ];

        foreach ($departments as $dept) {
            \App\Models\Department::updateOrCreate(
                ['name' => $dept['name']],
                [
                    'description' => $dept['description'],
                    'criteria' => $dept['criteria'],
                ]
            );
        }
    }
}
