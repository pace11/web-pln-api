<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('users')->insert([
            [
                'name' => 'admin',
                'email' => 'admin@pln.com',
                'password' => bcrypt('12345'),
                'type' => 'superadmin',
                'placement' => null,
                'unit_id' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // unit pelaksana
            [
                'name' => 'creator upt banda aceh',
                'email' => 'creatoraceh@pln.com',
                'password' => bcrypt('12345'),
                'type' => 'creator',
                'placement' => 'executor_unit',
                'unit_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'checker upt banda aceh',
                'email' => 'checkeraceh@pln.com',
                'password' => bcrypt('12345'),
                'type' => 'checker',
                'placement' => 'executor_unit',
                'unit_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'approver upt banda aceh',
                'email' => 'approveraceh@pln.com',
                'password' => bcrypt('12345'),
                'type' => 'approver',
                'placement' => 'executor_unit',
                'unit_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
            // kantor induk
            [
                'name' => 'creator kantor induk',
                'email' => 'creatorinduk@pln.com',
                'password' => bcrypt('12345'),
                'type' => 'creator',
                'placement' => 'main_office',
                'unit_id' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'checker kantor induk',
                'email' => 'checkerinduk@pln.com',
                'password' => bcrypt('12345'),
                'type' => 'checker',
                'placement' => 'main_office',
                'unit_id' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'approver kantor induk',
                'email' => 'approverinduk@pln.com',
                'password' => bcrypt('12345'),
                'type' => 'approver',
                'placement' => 'main_office',
                'unit_id' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
