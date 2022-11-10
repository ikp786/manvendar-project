<?php

namespace App\library {

    use App\Company;

    class Companyid
    {

        public function get_company_detail()
        {
            if (!empty($_SERVER['HTTP_HOST'])) {
                $host = $_SERVER['HTTP_HOST'];
            } else {
                $host = "localhost:8888";
            }
            $company = Company::where('company_website', $host)
                ->where('status', 1)
                ->first();
            if ($company) {
                return $company;
            } else {
                return $company;
            }
        }
    }

}