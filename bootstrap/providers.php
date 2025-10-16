<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\FortifyServiceProvider::class,
    App\Providers\InertiaServiceProvider::class,
    App\Modules\Admin\Providers\AdminModuleServiceProvider::class,
    App\Modules\Rental\Providers\RentalModuleServiceProvider::class,
    App\Modules\User\Providers\UserModuleServiceProvider::class,
    App\Modules\Vehicle\Providers\VehicleModuleServiceProvider::class,
];
