<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case CASH_ON_DELIVERY = 'CASH_ON_DELIVERY';
    case FLOOZ = 'FLOOZ';
    case MIXX_BY_YAS = 'MIXX_BY_YAS';
    case CARD = 'CARD';

    public function label(): string
    {
        return match ($this) {
            self::CASH_ON_DELIVERY => 'Paiement à la livraison',
            self::FLOOZ => 'Flooz (Moov Togo)',
            self::MIXX_BY_YAS => 'Mixx by YAS (Togocom)',
            self::CARD => 'Carte bancaire',
        };
    }

    /**
     * Indique si ce moyen de paiement passe par le gateway Fedapay
     * (par opposition au cash, géré manuellement à la livraison).
     */
    public function requiresGateway(): bool
    {
        return match ($this) {
            self::FLOOZ, self::MIXX_BY_YAS, self::CARD => true,
            self::CASH_ON_DELIVERY => false,
        };
    }

    /**
     * Code du "mode" attendu par l'API Fedapay pour le paiement Mobile
     * Money "sans redirection" (push direct sur le téléphone du client).
     * Référence : https://docs.fedapay.com/payment-methods
     *
     * Important : Fedapay ne permet le paiement sans redirection que pour
     * MTN Bénin, Moov Bénin, Moov Togo et MTN Côte d'Ivoire. Togocom
     * (Mixx by YAS / mode 'togocel') n'y est PAS éligible — toute tentative
     * de push direct renvoie une erreur "Opération non autorisée". Mixx by
     * YAS doit donc passer par le flux "redirect" classique (voir
     * PaymentGatewayService::initiate), d'où le null ci-dessous.
     *
     * Retourne null pour les moyens de paiement qui n'utilisent pas ce
     * flux direct (Mixx by YAS et carte bancaire → redirection vers la
     * page hébergée, cash → pas de gateway du tout).
     */
    public function fedapayMode(): ?string
    {
        return match ($this) {
            self::FLOOZ => 'moov_tg',
            self::CASH_ON_DELIVERY, self::MIXX_BY_YAS, self::CARD => null,
        };
    }

    /**
     * Indique si ce moyen de paiement nécessite un numéro de téléphone
     * mobile money pour être initié (push direct, sans redirection).
     */
    public function requiresPhoneNumber(): bool
    {
        return $this->fedapayMode() !== null;
    }
}
