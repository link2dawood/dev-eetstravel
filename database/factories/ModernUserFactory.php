<?php
namespace Database\Factories;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
class ModernUserFactory extends Factory {
    protected $model = User::class;
    public function definition() { return ['email_server'=>1, 'name'=>$this->faker->name(), 'email'=>$this->faker->unique()->safeEmail(), 'password'=>bcrypt('test-password')]; }
}
