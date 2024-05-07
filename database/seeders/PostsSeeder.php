<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class PostsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('posts')->insert([
            [
                'id' => Str::uuid(),
                'slug' => 'siaran-pers-test',
                'title' => 'Siaran Pers Test',
                'description' => 'Excepteur laboris ut reprehenderit dolor ullamco officia consectetur cupidatat. Tempor laborum laborum cillum aliquip ad in anim aliqua mollit sunt duis officia. Minim irure ipsum commodo irure nostrud Lorem ullamco nisi sit elit ut ad dolor veniam. Sunt reprehenderit voluptate ipsum incididunt exercitation ullamco nisi deserunt quis. Proident elit do amet veniam. Veniam velit ea Lorem laboris esse aliquip ex ea ut cillum pariatur.',
                'thumbnail' => 'rKCJC9kbQVXrjK2kod56UVg0t6YMbjRjrGrSV91N.jpg',
                'posted' => true,
                'banner' => true,
                'categories_id' => 1,
                'users_id' => 1,
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s'),
            ],
        ]);
    }
}
