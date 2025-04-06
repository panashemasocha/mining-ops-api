<?php
namespace Database\Factories;

use App\Models\Supplier;
use App\Models\User;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition()
    {
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

        $cities = ['Karoi', 'Harare'];
        $city = $this->faker->randomElement($cities);

        $physicalAddress = $this->faker->streetAddress . ', ' . $city;

        return [
            'first_name' => $this->faker->randomElement($zimbabweanFirstNames),
            'last_name' => $this->faker->randomElement($zimbabweanLastNames),
            'national_id' => '63-' . $this->faker->unique()->numberBetween(1000000, 9999999) . '-A00',
            'physical_address' => $physicalAddress,
            'created_by' => User::factory()->create(['role_id' => 2]), // 'management'
            'payment_method_id' => PaymentMethod::factory()->create(),
            'phone_number' => '+263' . $this->faker->unique()->numberBetween(712000000, 779999999),
        ];
    }
}
