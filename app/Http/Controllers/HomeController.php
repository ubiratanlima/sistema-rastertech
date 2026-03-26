<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Customer;
use App\Models\GsmCard;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $stats = [
            'devices' => Device::count(),
            'customers' => Customer::count(),
            'gsm_cards' => GsmCard::count(),
            'vehicles' => Vehicle::count(),
        ];

        return view('home', compact('stats'));
    }
}
<!-- slide -->
<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceModel;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
    {
        // Eager loading para evitar o N+1 e carregar rápido os 1.000 registros
        $devices = Device::with(['customer', 'gsmCard', 'deviceModel', 'platform'])
                         ->latest()
                         ->paginate(20);

        return view('devices.index', compact('devices'));
    }

    public function show(Device $device)
    {
        $device->load(['deviceModel.commands', 'gsmCard', 'platform']);
        return view('devices.show', compact('device'));
    }
}
