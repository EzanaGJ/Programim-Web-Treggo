<?php
require_once 'vendor/stripe-php/init.php';

$private_key = "sk_test_51SqvzNBphMflaAAwgC8naPtvzXlt0umeIWRNrqQOVUXvbh2gTEZv35j05PosYty9GXDQaSuc0n1yND4V25QJAvzN00MtrjnzGu";
$public_key = "pk_test_51SqvzNBphMflaAAwIGHwDNuP7XcKcDbQ2Ovohrso1iJ3p10H52m3UaEJR4xX3WBk3WUWGVM0bwIANK1ON4eTqbsZ00Q4akb2T0";

$stripe_account = "Treggo";
$businessName = "Treggo";
$company_name = "Treggo";

/**
 * Inicializimi i Stripe
 */
\Stripe\Stripe::setApiKey($private_key);
// \Stripe\Stripe::setMaxNetworkRetries(2);

$stripe = new \Stripe\StripeClient($private_key);