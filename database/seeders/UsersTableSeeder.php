<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Test User',
                'email' => 'testuser@youth.gov',
                'email_verified_at' => NULL,
                'password' => '$2y$12$mtjf0CAAwOfhITujpw/vXuSZ2ITCuTx241JAmVEvuYg4cyb5HYu5q',
                'remember_token' => NULL,
                'created_at' => '2025-09-16 05:54:34',
                'updated_at' => '2025-09-16 05:54:34',
            ),
        ));
        
        
    }
}