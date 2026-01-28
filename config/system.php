<?php

return [
    /*
    |--------------------------------------------------------------------------
    | System Check Toggle
    |--------------------------------------------------------------------------
    |
    | System validation is always enabled in production.
    | This setting only takes effect in local/development/testing environment.
    |
    */
    'enabled' => env('SYSTEM_CHECK', true),

    /*
    |--------------------------------------------------------------------------
    | System Key
    |--------------------------------------------------------------------------
    |
    | The system configuration key.
    |
    */
    'key' => env('SYSTEM_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Contact Information
    |--------------------------------------------------------------------------
    |
    | Contact information for system support.
    |
    */
    'developer' => [
        'name' => 'Developer',
        'email' => env('SYSTEM_CONTACT', 'developer@example.com'),
        'phone' => '',
    ],

    /*
    |--------------------------------------------------------------------------
    | Public Key for Verification
    |--------------------------------------------------------------------------
    |
    | DO NOT MODIFY THIS VALUE. This public key is used to verify the
    | signature of license keys. Only the developer has the private key
    | to generate valid licenses.
    |
    */
    'public_key' => <<<'KEY'
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA1NkYD0cE/2UvNyXP2T1V
1DKsPL/abckChhjtD2WAjj2oY9tPzVYeU8mo+jZizAZByrJ3T3jneVjPRsCHbq1T
ErGyGz1MVUnxOu/4/UCtc+JDCJDLrVYnt/S2gSi10zrRtuKq3jL2X6yQTssjXvQC
iulCt3K6I1zCrdduXk5HAUQA2GccW/PL3ECvrqXH7e7ik3UjmWye5iWwHrpffc5W
gpZczajDnPLtXQjJ2Y+JWCPUeUMxcSGxIU98fq9icbiSuuCiVs6KUjxyhOr51dx9
sRQ/tXbm7hVkER/8R8JvN+nNkP0/d2Fv5MB8TnuACPdtXht0wtM6BHZtTbrQwyoB
1wIDAQAB
-----END PUBLIC KEY-----
KEY,
];
