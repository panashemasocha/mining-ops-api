<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\User;
use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class SupplierSeeder extends Seeder
{
    protected $faker;

    public function __construct(Faker $faker)
    {
        $this->faker = $faker;
    }

    public function run()
    {
        $paymentMethods = PaymentMethod::all();
        $users = User::whereHas('role', function ($query) {
            $query->where('name', 'management');
        })->get();

        $zimbabweanFirstNames = [
            'Tawanda',
            'Tinashe',
            'Chiedza',
            'Rudo',
            'Farai',
            'Tendai',
            'Munyaradzi',
            'Fungai',
            'Tariro',
            'Nokutenda',
            'Tapiwa',
            'Anesu',
            'Rumbidzai'
        ];

        $zimbabweanLastNames = [
            'Moyo',
            'Ncube',
            'Chikore',
            'Khumalo',
            'Sithole',
            'Dube',
            'Mhlanga',
            'Nyathi',
            'Mandaza',
            'Mangena',
            'Zondo',
            'Mupindu',
            'Mpofu'
        ];

        for ($i = 0; $i < 5; $i++) {
            Supplier::create([
                'first_name'       => $this->faker->randomElement($zimbabweanFirstNames),
                'last_name'        => $this->faker->randomElement($zimbabweanLastNames),
                'national_id'      => '63-' . $this->faker->unique()->numberBetween(1000000, 9999999) . '-A00',
                'physical_address' => $this->faker->streetAddress . ', ' . $this->faker->randomElement(['Karoi', 'Harare']),
                'created_by'       => $users->random()->id,
                'payment_method_id'=> $paymentMethods->random()->id,
                'phone_number'     => '+263' . $this->faker->unique()->numberBetween(712000000, 779999999),
            ]);
        }
    }
}
