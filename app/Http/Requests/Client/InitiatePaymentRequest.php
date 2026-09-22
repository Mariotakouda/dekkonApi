<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class InitiatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Requis uniquement pour Flooz / Mixx by YAS — vérifié dans le
            // contrôleur (dépend du moyen de paiement choisi à la commande),
            // pas ici, pour garder un message d'erreur explicite.
            'phone_number' => ['nullable', 'string', 'max:20'],
        ];
    }
}
