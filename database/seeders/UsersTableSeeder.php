<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $userData =   [
            [
                'name' => 'Admin User',
                'type' => 'admin',
                'username' => 'admin',
                'email' => 'admin@ferjina.com',
                'phone' => "+201234567890",
                'password' => Hash::make('12345678'), // default password
                'login_type' => 'normal',
                'image' => asset('logo.png'),
                'fcm' => null,
                'gander' => 'male', // or use $faker->randomElement(['male', 'female'])
                'date' => $faker->date(),
                'code' => null,
                'status' => true,
                'invitation_code' => null,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Insert sample regular users
            [
                'name' => $faker->name,
                'type' => 'user',
                'username' => $faker->unique()->userName,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->unique()->phoneNumber,
                'password' => Hash::make('password'), // default password
                'login_type' => 'normal',
                'image' => null,
                'fcm' => null,
                'gander' => $faker->randomElement(['male', 'female']),
                'date' => $faker->date(),
                'code' => null,
                'status' => true,
                'invitation_code' => null,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => $faker->name,
                'type' => 'user',
                'username' => $faker->unique()->userName,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->unique()->phoneNumber,
                'password' => Hash::make('password'), // default password
                'login_type' => 'normal',
                'image' => null,
                'fcm' => null,
                'gander' => $faker->randomElement(['male', 'female']),
                'date' => $faker->date(),
                'code' => null,
                'status' => true,
                'invitation_code' => null,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => $faker->name,
                'type' => 'user',
                'username' => $faker->unique()->userName,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->unique()->phoneNumber,
                'password' => Hash::make('password'), // default password
                'login_type' => 'normal',
                'image' => null,
                'fcm' => null,
                'gander' => $faker->randomElement(['male', 'female']),
                'date' => $faker->date(),
                'code' => null,
                'status' => true,
                'invitation_code' => null,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more users as needed
        ];
        $userPermission = [
            'admin',
            'vendor',
            'user',
        ];
        $PermissionAdmin = [
            'الصفحه الرئيسيه',
            'الصفحه الرئيسيه للتاجر',
            'عام',
            'الاقسام',
            'جميع الاقسام',
            'البنرات الإعلانية',
            'اضافة قسم',
            'تعديل قسم',
            'حذف قسم',
            'الاقسام الفرعيه',
            'جميع الاقسام الفرعيه',
            'اضافة الاقسام الفرعيه',
            'حذف الاقسام الفرعيه',
            'تعديل الاقسام الفرعيه',
            'تسوق',
            'المنتجات',
            'جميع المنتجات',
            'المنتجات الغير مفعله',
            'اضافة منتج',
            'تعديل منتج',
            'حذف منتج',
            'حالة منتج',
            'نسخ المنتج',
            'الالوان و الاحجام',
            'بوبات الدفع',
            'الالوان',
            'اضافة لون',
            'تعديل لون',
            'حذف لون',
            'الاحجام',
            'اضافة حجم',
            'تعديل حجم',
            'حذف حجم',
            'القسائم',
            'جميع القسائم',
            'اضافة قسيمه',
            'تعديل قسيمه',
            'حذف قسيمه',
            'اعدادت الهدايا',
            'الطلبيات',
            'جميع الطلبيات',
            'عرض الطلبيه',
            'حذف الطلبيه',
            'طباعة الطلبيه',
            'شكاوي المستخدمين',
            'المستخدمين',
            'رؤية المستخدمين',
            'صلاحيات المستخدمين',
            'الدول و الضرائب',
            // 'رؤية الدول',
            // 'رؤية المدن',
            // 'الابلاغات',
            'التقارير و الاستعلامات',
            'الاعدادات',
            'اعدادت الصفحات',
            'الاعدادت الرئيسيه',
            'الاعدادت العامه',
            'الصفحه الرئيسيه للبائع',
            'المتتجات الخاصه',


        ];

        $Permissionvendor = [
            'الصفحه الرئيسيه للبائع',
            'الصفحه الرئيسيه للتاجر',
            'المتتجات الخاصه',
            'الاعدادات',
            'الاعدادت العامه',
            'الاعدادت الرئيسيه',
            'المنتجات',
            'اضافة منتج',
            'تعديل منتج',
            'حذف منتج',
            'تسوق',
        ];


        $roleList = [];
        foreach ($userPermission as $permissionName) {
            $role = Role::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);

            if ($role->name == 'admin') {
                $role->syncPermissions($PermissionAdmin);
            } else {
                $role->syncPermissions($Permissionvendor);
            }

            $roleList[] = $role->id;
        }

        foreach ($userData as $data) {
            $user = User::create($data);
            if ($user->id == 1) {
                $user->assignRole([$roleList[0]]);
            } elseif ($user->id == 2) {
                $user->assignRole([$roleList[1]]);
            } else {
                $user->assignRole([$roleList[2]]);
            }
        }
    }
}
