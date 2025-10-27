<?php

namespace Modules\EBilling\Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::createOrFirst(['name' => 'admin', 'description' => 'Administrator', 'guard_name' => 'ebil']);

        $permissionGroup = [
            'master_data' => [
                'Site' => [
                    'read-sites' => 'Melihat Site',
                    'create-sites' => 'Membuat Site',
                    'update-sites' => 'Memperbarui Site',
                    'delete-sites' => 'Menghapus Site',
                ],

                'Perangkat' => [
                    'read-devices' => 'Melihat Perangkat',
                    'create-devices' => 'Membuat Perangkat',
                    'update-devices' => 'Memperbarui Perangkat',
                    'delete-devices' => 'Menghapus Perangkat',
                ],

                'Paket' => [
                    'read-packages' => 'Melihat Paket',
                    'create-packages' => 'Membuat Paket',
                    'update-packages' => 'Memperbarui Paket',
                    'delete-packages' => 'Menghapus Paket',
                ],

                'Pelanggan' => [
                    'read-customers' => 'Melihat Pelanggan',
                    'create-customers' => 'Membuat Pelanggan',
                    'update-customers' => 'Memperbarui Pelanggan',
                    'delete-customers' => 'Menghapus Pelanggan',
                ],
            ],

            'transaction' => [
                'Tagihan' => [
                    'read-invoices' => 'Melihat Tagihan',
                    'create-invoices' => 'Membuat Tagihan',
                    'update-invoices' => 'Memperbarui Tagihan',
                ],
            ],

            'settings' => [
                'Role' => [
                    'read-roles' => 'Melihat Role',
                    'create-roles' => 'Membuat Role',
                    'update-roles' => 'Memperbarui Role',
                    'delete-roles' => 'Menghapus Role',
                ],
                'User' => [
                    'read-users' => 'Melihat User',
                    'create-users' => 'Membuat User',
                    'update-users' => 'Memperbarui User',
                    'delete-users' => 'Menghapus User',
                ],
            ],
        ];

        foreach ($permissionGroup as $group => $menus) {
            foreach ($menus as $menu => $permissions) {
                foreach ($permissions as $name => $description) {
                    Permission::createOrFirst([
                        'name' => $name,
                        'description' => $description,
                        'menu' => $menu,
                        'menu_group' => $group,
                        'guard_name' => 'ebil',
                    ]);
                }
            }
        }
    }
}
