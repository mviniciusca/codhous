<?php

namespace Database\Seeders;

use Database\Factories\PageFactory;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Seed the pages table with the index page (slug /) for the site to work when built.
     */
    public function run(): void
    {
        PageFactory::new()->index()->create();
    }
}
