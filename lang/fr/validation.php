<?php

return [
    'required' => 'Le champ :attribute est obligatoire.',
    'email' => 'Le champ :attribute doit être une adresse email valide.',
    'unique' => 'Cette valeur pour :attribute est déjà utilisée.',
    'min' => [
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
        'numeric' => 'Le champ :attribute doit être au moins :min.',
    ],
    'max' => [
        'string' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
        'numeric' => 'Le champ :attribute ne doit pas dépasser :max.',
    ],
    'numeric' => 'Le champ :attribute doit être un nombre.',
    'integer' => 'Le champ :attribute doit être un entier.',
    'boolean' => 'Le champ :attribute doit être vrai ou faux.',
    'date' => 'Le champ :attribute doit être une date valide.',
    'exists' => 'La valeur sélectionnée pour :attribute est invalide.',
    'confirmed' => 'La confirmation de :attribute ne correspond pas.',
    'in' => 'La valeur sélectionnée pour :attribute est invalide.',
    'uuid' => 'Le champ :attribute doit être un UUID valide.',
    'attributes' => [
        'email' => 'email',
        'password' => 'mot de passe',
        'phone' => 'téléphone',
        'first_name' => 'prénom',
        'last_name' => 'nom',
        'quantity' => 'quantité',
        'price' => 'prix',
    ],
];
