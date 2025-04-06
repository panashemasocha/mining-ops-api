<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\JobPosition;
use App\Models\Branch;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('en_ZW');

        $jobPositions = JobPosition::all();

        $zimbabweanLastNames = [
            'Moyo',
            'Ncube',
            'Chikore',
            'Khumalo',
            'Sithole',
            'Dube',
            'Mhlanga',
            'Nyathi',
            'Chireya',
            'Madziva',
            'Chitando',
            'Matope',
            'Mandaza',
            'Mangena',
            'Mangwana',
            'Mudzuri',
            'Sigauke',
            'Ndoro',
            'Ndlovu',
            'Mukoriwo',
            'Chibanda',
            'Mutasa',
            'Rukweza',
            'Chirwa',
            'Hove',
            'Manyame',
            'Gumbo',
            'Charumbira',
            'Shava',
            'Marufu',
            'Runyowa',
            'Taderera',
            'Muchuchu',
            'Chipanga',
            'Masuku',
            'Chidyausiku',
            'Muzenda',
            'Gwangwava',
            'Nyamakura',
            'Sibanda',
        ];
        $karoiAddresses = [
            '123 Chiedza Street, Karoi',
            '456 Magunje Road, Karoi',
            '789 Nyamakuyu Avenue, Karoi',
            '10 Chinhoyi Road, Karoi',
            '20 Mzhanje Street, Karoi',
            '55 Muswere Crescent, Karoi',
            '77 Nyadire Close, Karoi',
            '99 Chisamba Drive, Karoi',
            '11 Mudzimu Street, Karoi',
            '33 Zambezi Road, Karoi',
            '66 Gokwe Avenue, Karoi',
            '88 Chirundu Way, Karoi',
            '22 Dande Close, Karoi',
            '44 Manyame Drive, Karoi',
            '15 Mazowe Street, Karoi',
            '25 Ruya Road, Karoi',
            '35 Mukumbura Avenue, Karoi',
            '45 Kadoma Close, Karoi',
            '55 Sanyati Drive, Karoi',
            '65 Mvurwi Street, Karoi',
        
        ];
        
        $harareAddresses = [
            '10 First Street, Harare',
            '20 Second Avenue, Harare',
            '30 Third Road, Harare',
            '40 Kwame Nkrumah Avenue, Harare',
            '50 Robert Mugabe Road, Harare',
            '60 Julius Nyerere Way, Harare',
            '70 Samora Machel Avenue, Harare',
            '80 Chinhoyi Street, Harare',
            '90 Jason Moyo Avenue, Harare',
            '100 Simon Muzenda Street, Harare',
            '110 Herbert Chitepo Avenue, Harare',
            '120 Leopold Takawira Avenue, Harare',
            '130 Mazowe Street, Harare',
            '140 Seke Road, Harare',
            '150 Mutare Road, Harare',
            '160 Bulawayo Road, Harare',
            '170 Beatrice Road, Harare',
            '180 Lomagundi Road, Harare',
            '190 Enterprise Road, Harare',
            '200 Arcturus Road, Harare',
        ];

        foreach ($jobPositions as $jobPosition) {
            for ($i = 0; $i < 2; $i++) {
                $firstName = $faker->firstName;
                $lastName = $faker->randomElement($zimbabweanLastNames);
                $branchId = $faker->numberBetween(1, 2);

                $branch = Branch::find($branchId);
                $branchName = $branch ? $branch->name : null;

                $physicalAddress = null;
                if ($branchName === 'Karoi') {
                    $physicalAddress = $faker->randomElement($karoiAddresses);
                } elseif ($branchName === 'Harare') {
                    $physicalAddress = $faker->randomElement($harareAddresses);
                } else {
                    $physicalAddress = $faker->address;
                }

                User::factory()->create([
                    'employee_code' => 'EMP' . $faker->unique()->numberBetween(1000, 9999),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'phone_number' => '+263' . $faker->unique()->numberBetween(712000000, 779999999),
                    'pin' => '1234',
                    'status' => $faker->randomElement([0, 1, 2]),
                    'job_position_id' => $jobPosition->id,
                    'branch_id' => $branchId,
                    'department_id' => $faker->numberBetween(1, 2),
                    'role_id' => $jobPosition->role_id,
                    'physical_address' => $physicalAddress,
                    'date_of_birth' => $faker->date('Y-m-d', '2000-12-31'),
                    'national_id' => $faker->numerify('##########'),
                    'gender' => $faker->randomElement(['male', 'female']),
                    'email' => strtolower($firstName . '.' . $lastName) . '@mwamiresources.com',
                ]);
            }
        }

        $driverPosition = JobPosition::where('name', 'Driver')->first();
        if ($driverPosition) {
            for ($i = 0; $i < 5; $i++) {
                $firstName = $faker->firstName;
                $lastName = $faker->randomElement($zimbabweanLastNames);
                $branchId = $faker->numberBetween(1, 2);

                $branch = Branch::find($branchId);
                $branchName = $branch ? $branch->name : null;

                $physicalAddress = null;
                if ($branchName === 'Karoi') {
                    $physicalAddress = $faker->randomElement($karoiAddresses);
                } elseif ($branchName === 'Harare') {
                    $physicalAddress = $faker->randomElement($harareAddresses);
                } else {
                    $physicalAddress = $faker->address;
                }

                User::factory()->create([
                    'employee_code' => 'EMP' . $faker->unique()->numberBetween(1000, 9999),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'phone_number' => '+263' . $faker->unique()->numberBetween(712000000, 779999999),
                    'pin' => '1234',
                    'status' => $faker->randomElement([0, 1, 2]),
                    'job_position_id' => $driverPosition->id,
                    'branch_id' => $branchId,
                    'department_id' => $faker->numberBetween(1, 2),
                    'role_id' => $jobPosition->role_id,
                    'physical_address' => $physicalAddress,
                    'date_of_birth' => $faker->date('Y-m-d', '2000-12-31'),
                    'national_id' => $faker->numerify('##########'),
                    'gender' => $faker->randomElement(['male', 'female']),
                    'email' => strtolower($firstName . '.' . $lastName) . '@mwamiresources.com',
                ]);
            }
        }
    }
}