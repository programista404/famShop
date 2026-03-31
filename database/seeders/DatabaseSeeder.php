<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
      Admin::create([
          'ad_Fname' => 'shagheel',
          'ad_Lname' => 'admin',
          'ad_Email' => 'admin@shagheel.com',
          'password' => Hash::make(123456),
          'ad_Phone' => 12222222222,
      ]);
    }
}
