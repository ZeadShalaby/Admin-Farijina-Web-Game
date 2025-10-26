<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CategoryPermissionsSeeder extends Seeder
{
    public function run()
    {
        $categories = Category::all();
        $adminRole = Role::where('name', 'Admin')->where('guard_name', 'web')->first();

        foreach($categories as $cat){
            $permission = Permission::firstOrCreate([
                'name' => $cat->title,
                'guard_name' => 'web',
                'group' => 'categories',
            ]);

            if($adminRole){
                $adminRole->givePermissionTo($permission);
            }
        }
    }
}
