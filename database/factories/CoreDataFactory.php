<?php

namespace Database\Factories;

use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProviderFactory extends Factory
{
    protected $model = Provider::class;
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'type' => $this->faker->randomElement(['hardware', 'connectivity', 'software']),
        ];
    }
}
<!-- slide -->
<?php

namespace Database\Factories;

use App\Models\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlatformFactory extends Factory
{
    protected $model = Platform::class;
    public function definition(): array
    {
        return [
            'name' => 'Plataforma ' . $this->faker->word(),
            'url' => $this->faker->url(),
            'server_ip' => $this->faker->ipv4(),
            'supplier_name' => $this->faker->name(),
        ];
    }
}
<!-- slide -->
<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'document' => $this->faker->numerify('###########'), 
            'is_default_stock' => false,
        ];
    }
}
<!-- slide -->
<?php

namespace Database\Factories;

use App\Models\GsmCard;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

class GsmCardFactory extends Factory
{
    protected $model = GsmCard::class;
    public function definition(): array
    {
        return [
            'iccid' => $this->faker->numerify('8955################'),
            'phone_number' => $this->faker->phoneNumber(),
            'operator' => $this->faker->randomElement(['Vivo', 'Claro', 'Tim', 'Arqia', 'Links']),
            'apn' => 'apn.m2m.com.br',
            'apn_user' => 'm2m',
            'apn_pass' => 'm2m',
            'provider_id' => Provider::factory(),
            'status' => 'active',
        ];
    }
}
