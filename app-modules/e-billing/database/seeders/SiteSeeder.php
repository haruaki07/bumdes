<?php

namespace Modules\EBilling\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\EBilling\Models\Site;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Site::factory(2)->create();
    }
}
