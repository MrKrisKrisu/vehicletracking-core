<?php

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
        $company = new Company;
        $company->name = "OSI 4 Transport UG (haftungsbeschränkt)";
        $company->save();

        $company = new Company;
        $company->name = "Beispiel-Verkehrsunternehmen AG";
        $company->save();
    }
}
