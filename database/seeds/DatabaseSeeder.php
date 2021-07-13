<?php

use App\Models\Customer;
use App\Models\Method;
use App\Models\Job;
use App\Models\Type;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'role' => 'admin',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ]);
        factory(User::class, 100)->create();
        factory(Type::class, 100)->create();
        factory(Method::class, 100)->create();
        factory(Customer::class, 100)->create();

        factory(Job::class, 100)->create();

        DB::table('users')->where('username', 'admin')->update(['role' => 'admin']);
        DB::table('users')->update(['active' => true]);
    }
}
