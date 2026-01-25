<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Module;
use App\Models\ModuleAccess;
use App\Models\ModuleMedia;

class DevSeeder extends Seeder
{
    public function run(): void
    {
        // Usuarios base de desarrollo
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password123'),
                'is_admin' => true,
            ]
        );

        $user = User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'User',
                'password' => Hash::make('password123'),
                'is_admin' => false,
            ]
        );

        // Módulo de ejemplo
        $module = Module::updateOrCreate(
            ['slug' => 'qr-generator-2'],
            [
                'name' => 'QR Generator',
                'summary' => 'Generador de QR',
                'description' => 'Recurso de ejemplo',
                'version' => '0.1.0',
                'price_cents' => 0,
                'currency' => 'EUR',
                'status' => 'published',
            ]
        );

        // Acceso (grant) al usuario
        ModuleAccess::updateOrCreate(
            [
                'module_id' => $module->id,
                'user_id' => $user->id,
            ],
            [
                'access_type' => 'grant',
                'granted_by_user_id' => $admin->id,
                'revoked_at' => null,
                'metadata' => null,
            ]
        );

        // Media de ejemplo (1 imagen)
        ModuleMedia::updateOrCreate(
            [
                'module_id' => $module->id,
                'url' => 'https://raw.githubusercontent.com/USER/REPO/main/path/to/image.png',
            ],
            [
                'type' => 'image',
                'provider' => 'github',
                'sort_order' => 0,
                'alt_text' => 'Captura del módulo',
            ]
        );
    }
}
