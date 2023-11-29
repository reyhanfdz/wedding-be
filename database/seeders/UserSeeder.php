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
        $data = [
            [ 'email' => 'mugnirusmana95@gmail.com', 'password' => 'p@ssw0rd123', 'role' => 'admin', 'name' => 'Ade Mugni Rusmana', 'phone' => '+6282216599824'],
            [ 'email' => 'm.rhuzmana@gmail.com', 'password' => 'p@ssw0rd123', 'role' => 'staff', 'name' => 'Ade Mugni Rusmana', 'phone' => '+628980500453'],
        ];

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
