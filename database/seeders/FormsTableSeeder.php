<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FormsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('forms')->delete();
        
        \DB::table('forms')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Club Registration Form',
                'slug' => 'crf',
                'description' => 'Register your Club',
                'is_active' => true,
                'created_at' => '2025-09-16 05:55:34',
                'updated_at' => '2025-09-16 05:55:34',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Tournament Registration Form',
                'slug' => 'trf',
                'description' => 'Register for a Tournament',
                'is_active' => true,
                'created_at' => '2025-09-17 05:15:58',
                'updated_at' => '2025-09-17 05:15:58',
            ),
        ));
        
        
    }
}