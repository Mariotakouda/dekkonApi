<?php

namespace Database\Seeders;

use App\Enums\EmployeeStatus;
use App\Enums\UserStatus;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Sécurité : on ne permet le seed admin qu'en local/staging par défaut.
        // En prod, il faut forcer explicitement avec --force pour éviter un écrasement accidentel.
        if (app()->environment('production') && ! $this->command->option('force')) {
            $this->command->warn('AdminUserSeeder ignoré en production. Utilisez --force si intentionnel.');
            return;
        }

        $email = env('ADMIN_SEED_EMAIL');
        $password = env('ADMIN_SEED_PASSWORD');

        if (! $email) {
            $this->command->error('ADMIN_SEED_EMAIL manquant dans .env — seeder admin annulé.');
            return;
        }

        $generatedPassword = false;
        if (! $password) {
            $password = Str::password(16);
            $generatedPassword = true;
        }

        $adminRole = Role::where('code', 'ADMIN')->first();

        if (! $adminRole) {
            $this->command->error('Rôle ADMIN introuvable — lancez RolePermissionSeeder avant AdminUserSeeder.');
            return;
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'role_id' => $adminRole->id,
                'name' => 'Administrateur DEKKON',
                'code' => 'ADM-' . Str::upper(Str::random(6)),
                'phone' => env('ADMIN_SEED_PHONE', '+228' . random_int(90000000, 99999999)),
                'password' => Hash::make($password),
                'status' => UserStatus::ACTIVE,
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        Employee::firstOrCreate(
            ['user_id' => $user->id],
            [
                'employee_number' => 'EMP-0001',
                'first_name' => 'Admin',
                'last_name' => 'DEKKON',
                'position' => 'Administrateur système',
                'date_hired_at' => now(),
                'status' => EmployeeStatus::ACTIVE,
            ]
        );

        $this->command->info("Compte admin prêt : {$email}");

        if ($generatedPassword) {
            $this->command->warn("Mot de passe généré (à noter, ne sera plus affiché) : {$password}");
        }
    }
}
