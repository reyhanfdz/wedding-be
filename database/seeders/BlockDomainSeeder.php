<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BlockDomain;

class BlockDomainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = ['mailinator', 'mailtrap', 'yogmail', 'mailosaur'];
        foreach($data as $item) {
            $data = BlockDomain::where('name', $item)->first();
            if(!$data) BlockDomain::create(['name' => $item]);
        }
    }
}
