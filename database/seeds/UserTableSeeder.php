<?php

use App\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name'      => 'Luthfi Baehaqi',
            'email'     => 'admin@admin.com',
            'password'  => bcrypt('12345678'),
            'phone'     => '08123456789',
            'role'      => 1, // System Administrator
        ]);
    }
}
