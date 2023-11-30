<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Profile;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [];
        $email_admin = env("EMAIL_ADMIN");
        $phone_admin = env("PHONE_ADMIN");
        $name_admin = env("NAME_ADMIN");
        $email_staff = env("EMAIL_STAFF");
        $phone_staff = env("PHONE_STAFF");
        $name_staff = env("NAME_STAFF");

        if ($email_admin && $phone_admin && $name_admin) $data[] = ['email' => $email_admin, 'password' => 'p@ssw0rd123', 'role' => 'admin', 'name' => $name_admin, 'phone' => $phone_admin];
        if ($email_staff && $phone_staff && $name_staff) $data[] = ['email' => $email_staff, 'password' => 'p@ssw0rd123', 'role' => 'staff', 'name' => $name_staff, 'phone' => $phone_staff];

        foreach($data as $item) {
            $data = User::where('email', $item['email'])->first();

            if (!$data) {
                $user = User::create([
                    'email' => $item['email'],
                    'status' => 2,
                    'password' => Hash::make($item['password']),
                    'role' => $item['role'],
                ]);

                Profile::create([
                    'name' => $item['name'],
                    'phone' => $item['phone'],
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}
