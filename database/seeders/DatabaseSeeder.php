<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;



class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            PermissionTableSeeder::class,
            RoleUserSeeder::class,
            UsersTableSeeder::class,
            CountryTableSeeder::class,
            CategorySeeder::class,
            FaqsTableSeeder::class,
            SettingWebTableSeeder::class,
            SettingsTableSeeder::class,
        ]);
    }
}
