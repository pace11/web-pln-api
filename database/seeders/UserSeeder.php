<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
                'unit_id' => null,
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s'),
            ],
            [
                'name' => 'creator upt banda aceh',
                'email' => 'creatoraceh@pln.com',
                'password' => bcrypt('12345'),
                'type' => 'creator',
                'unit_id' => 1,
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s'),
            ],
            [
                'name' => 'checker upt banda aceh',
                'email' => 'checkeraceh@pln.com',
                'password' => bcrypt('12345'),
                'type' => 'checker',
                'unit_id' => 1,
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s'),
            ],
            [
                'name' => 'approver upt banda aceh',
                'email' => 'approveraceh@pln.com',
                'password' => bcrypt('12345'),
                'type' => 'approver',
                'unit_id' => 1,
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s'),
            ],
            [
                'name' => 'admin upt banda aceh',
                'email' => 'adminaceh@pln.com',
                'password' => bcrypt('12345'),
                'type' => 'admin',
                'unit_id' => 1,
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s'),
            ],
            [
                'name' => 'creator upt medan',
                'email' => 'creatormedan@pln.com',
                'password' => bcrypt('12345'),
                'type' => 'creator',
                'unit_id' => 2,
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s'),
            ],
            [
                'name' => 'checker upt medan',
                'email' => 'checkermedan@pln.com',
                'password' => bcrypt('12345'),
                'type' => 'checker',
                'unit_id' => 2,
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s'),
            ],
            [
                'name' => 'approver upt medan',
                'email' => 'approvermedan@pln.com',
                'password' => bcrypt('12345'),
                'type' => 'approver',
                'unit_id' => 2,
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s'),
            ],
            [
                'name' => 'admin upt medan',
                'email' => 'adminmedan@pln.com',
                'password' => bcrypt('12345'),
                'type' => 'admin',
                'unit_id' => 2,
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s'),
            ],
        ]);
    }
}
