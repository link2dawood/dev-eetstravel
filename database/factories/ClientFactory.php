<?php
namespace Database\Factories;
use App\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
class ClientFactory extends Factory {
    protected $model = Client::class;
    public function definition() { return ['name'=>$this->faker->company(), 'country'=>'GB', 'city'=>null, 'address'=>$this->faker->address(), 'work_phone'=>'123456789', 'contact_phone'=>null, 'work_email'=>$this->faker->unique()->safeEmail(), 'contact_email'=>null, 'work_fax'=>null]; }
}
