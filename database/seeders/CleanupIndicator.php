<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CleanupIndicator extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('media_item')->truncate();
        \DB::table('account_influencer_item')->truncate();
        \DB::table('internal_communication_item')->truncate();
        \DB::table('scoring_item')->truncate();
        \DB::table('news_item')->truncate();
        \DB::table('public_information_item')->truncate();
    }
}
