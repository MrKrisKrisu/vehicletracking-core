<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Company;

class CompanyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Company::create(['name' => 'OSI 4 Transport UG (haftungsbeschränkt)']);
        Company::create(['name' => 'Beispiel-Verkehrsunternehmen AG']);
    }
}
