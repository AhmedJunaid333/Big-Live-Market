<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Core permissions
        $permissions = [
            // Listings
            'listings.create',
            'listings.edit',
            'listings.delete',
            'listings.publish',
            'listings.moderate',

            // Chats & Offers
            'chats.participate',
            'offers.create',
            'offers.respond',

            // Business & Storefront
            'business.manage',
            'business.team.manage',

            // Support & Reports
            'support.tickets.manage',
            'reports.review',

            // Finance
            'finance.view',
            'finance.refund',

            // Admin & System
            'admin.access',
            'admin.settings.manage',
            'admin.roles.manage',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'sanctum']);
        }

        // Roles
        $roles = [
            'buyer' => [
                'chats.participate',
                'offers.create',
            ],
            'seller' => [
                'chats.participate',
                'offers.create',
                'offers.respond',
                'listings.create',
                'listings.edit',
                'listings.delete',
                'listings.publish',
            ],
            'verified_seller' => [
                'chats.participate',
                'offers.create',
                'offers.respond',
                'listings.create',
                'listings.edit',
                'listings.delete',
                'listings.publish',
            ],
            'business_seller' => [
                'chats.participate',
                'offers.create',
                'offers.respond',
                'listings.create',
                'listings.edit',
                'listings.delete',
                'listings.publish',
                'business.manage',
                'business.team.manage',
            ],
            'moderator' => [
                'listings.moderate',
                'reports.review',
                'admin.access',
            ],
            'support' => [
                'support.tickets.manage',
                'admin.access',
            ],
            'finance' => [
                'finance.view',
                'finance.refund',
                'admin.access',
            ],
            'admin' => [
                'listings.moderate',
                'reports.review',
                'support.tickets.manage',
                'finance.view',
                'admin.access',
                'admin.settings.manage',
            ],
            'super_admin' => $permissions,
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $roleWeb = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $roleSanctum = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'sanctum']);

            $roleWeb->syncPermissions($rolePermissions);
            $roleSanctum->syncPermissions($rolePermissions);
        }
    }
}
