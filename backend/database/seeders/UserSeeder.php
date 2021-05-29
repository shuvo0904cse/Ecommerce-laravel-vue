<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            "id"                => Str::uuid()->toString(),
            "name"              => "Admin",
            "email"             => "admin@gmail.com",
            "password"          => bcrypt("123456"),
            "email_verified_at" => now()->toDateTimeString(),
            "role"              => config("settings.admin_role")
        ];

        $this->userModel()->storeData($array);
    }

    protected function userModel(){
        return new User();
    }
}
