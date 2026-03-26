<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['plate', 'brand', 'model', 'customer_id'];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function devices() { return $this->hasMany(Device::class); }
}
<!-- slide -->
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Provider extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'type'];

    public function devices() { return $this->hasMany(Device::class); }
    public function gsmCards() { return $this->hasMany(GsmCard::class); }
}
<!-- slide -->
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeviceModel extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'manufacturer'];

    public function commands() { return $this->hasMany(DeviceCommand::class); }
    public function devices() { return $this->hasMany(Device::class); }
}
<!-- slide -->
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceCommand extends Model
{
    use HasFactory;
    protected $fillable = ['device_model_id', 'description', 'command_template', 'execution_order'];

    public function deviceModel() { return $this->belongsTo(DeviceModel::class); }
}
<!-- slide -->
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Platform extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'url', 'server_ip', 'supplier_name'];

    public function devices() { return $this->hasMany(Device::class); }
}
<!-- slide -->
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerSubUser extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['customer_id', 'name', 'email', 'role', 'permissions', 'external_username', 'external_password'];

    public function customer() { return $this->belongsTo(Customer::class); }
}
