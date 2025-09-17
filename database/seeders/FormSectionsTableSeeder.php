<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FormSectionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('form_sections')->delete();
        
        \DB::table('form_sections')->insert(array (
            0 => 
            array (
                'id' => 5,
                'form_id' => 1,
                'title' => 'Venue Information',
                'order' => 2,
                'created_at' => '2025-09-16 06:58:14',
                'updated_at' => '2025-09-16 06:58:14',
            ),
            1 => 
            array (
                'id' => 6,
                'form_id' => 1,
                'title' => 'Coaches',
                'order' => 3,
                'created_at' => '2025-09-16 06:58:30',
                'updated_at' => '2025-09-16 06:58:30',
            ),
            2 => 
            array (
                'id' => 7,
                'form_id' => 1,
                'title' => 'Training',
                'order' => 4,
                'created_at' => '2025-09-16 06:59:42',
                'updated_at' => '2025-09-16 06:59:42',
            ),
            3 => 
            array (
                'id' => 3,
                'form_id' => 1,
                'title' => 'General Information',
                'order' => 1,
                'created_at' => '2025-09-16 06:33:22',
                'updated_at' => '2025-09-16 07:27:22',
            ),
        ));
        
        
    }
}