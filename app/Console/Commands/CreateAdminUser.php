<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * Le nom et la signature de la commande.
     *
     * @var string
     */
    protected $signature = 'app:create-admin';

    /**
     * La description de la commande.
     *
     * @var string
     */
    protected $description = 'Créer un compte administrateur de façon interactive et sécurisée';

    /**
     * Exécute la commande.
     */
    public function handle(): int
    {
        $this->info("=== Création d'un administrateur Educ'Academy ===");

        // 1. Nom complet
        $name = trim($this->ask('Nom complet de l\'administrateur'));
        if (empty($name)) {
            $this->error('Le nom complet est obligatoire.');
            return self::FAILURE;
        }

        // 2. Numéro de téléphone
        $rawPhone = $this->ask('Numéro de téléphone (identifiant de connexion)');
        $phone = User::normalizePhone($rawPhone);

        if (empty($phone)) {
            $this->error('Le numéro de téléphone est obligatoire.');
            return self::FAILURE;
        }

        if (User::where('phone', $phone)->exists()) {
            $this->error("Un compte existe déjà avec le numéro {$phone}.");
            return self::FAILURE;
        }

        // 3. E-mail (facultatif)
        $email = trim($this->ask('Adresse e-mail (facultative, appuyez sur Entrée pour ignorer)'));
        if (!empty($email)) {
            $validator = Validator::make(['email' => $email], [
                'email' => ['email', 'unique:users,email'],
            ]);

            if ($validator->fails()) {
                $this->error($validator->errors()->first('email'));
                return self::FAILURE;
            }
        } else {
            $email = null;
        }

        // 4. Mot de passe masqué
        $password = $this->secret('Mot de passe (8 caractères minimum, saisie masquée)');
        if (strlen($password ?? '') < 8) {
            $this->error('Le mot de passe doit comporter au moins 8 caractères.');
            return self::FAILURE;
        }

        $passwordConfirmation = $this->secret('Confirmez le mot de passe');
        if ($password !== $passwordConfirmation) {
            $this->error('Les deux mots de passe ne correspondent pas.');
            return self::FAILURE;
        }

        // 5. Création sécurisée de l'administrateur
        $user = new User([
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'password' => $password, // automatiquement haché grâce au cast 'password' => 'hashed'
            'approved_at' => now(),
        ]);

        // Attribution directe des privilèges (protégés contre l'assignation de masse)
        $user->role = UserRole::Admin;
        $user->status = UserStatus::Approved;
        $user->must_change_password = false;
        $user->save();

        $this->newLine();
        $this->info("Compte administrateur créé avec succès !");
        $this->table(
            ['Identifiant (Téléphone)', 'Nom', 'E-mail', 'Rôle', 'Statut'],
            [[$user->phone, $user->name, $user->email ?? 'Non renseigné', $user->role->label(), $user->status->label()]]
        );

        return self::SUCCESS;
    }
}
