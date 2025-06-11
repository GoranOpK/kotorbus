<?php

return [

    // Modeli koje Spatie koristi za permissions i role
    'models' => [

        // Model koji koristi trait HasPermissions
        'permission' => Spatie\Permission\Models\Permission::class,

        // Model koji koristi trait HasRoles
        'role' => Spatie\Permission\Models\Role::class,
    ],

    // Guard-ovi na kojima je ovaj paket aktivan (za tebe je samo web!)
    'guards' => ['web'],

    // Nazivi tabela koje koristi Spatie za permissions i role
    'table_names' => [
        'roles' => 'roles',
        'permissions' => 'permissions',
        'model_has_permissions' => 'model_has_permissions',
        'model_has_roles' => 'model_has_roles',
        'role_has_permissions' => 'role_has_permissions',
    ],

    'column_names' => [
        'role_pivot_key' => null, // default 'role_id'
        'permission_pivot_key' => null, // default 'permission_id'
        'model_morph_key' => 'model_id', // ako koristiš uuid možeš promijeniti u 'model_uuid'
        'team_foreign_key' => 'team_id',
    ],

    // Da li se metoda za provjeru permisija registruje na Gate
    'register_permission_check_method' => true,

    // Listener za Octane (nije ti potrebno)
    'register_octane_reset_listener' => false,

    // Da li da se šalju eventi kad se doda/ukloni rola ili permission
    'events_enabled' => false,

    // Teams feature - koristiš li timove? (obično false)
    'teams' => false,
    'team_resolver' => \Spatie\Permission\DefaultTeamResolver::class,

    // Passport Client Credentials Grant (nije ti potrebno)
    'use_passport_client_credentials' => false,

    // Da li prikazivati ime permisije u exception poruci (security razlog)
    'display_permission_in_exception' => false,
    'display_role_in_exception' => false,

    // Wildcard permissions (ako želiš npr. 'user.*' permisije)
    'enable_wildcard_permission' => false,
    // 'wildcard_permission' => Spatie\Permission\WildcardPermission::class,

    // Cache podešavanja za Spatie permissions
    'cache' => [
        'expiration_time' => \DateInterval::createFromDateString('24 hours'),
        'key' => 'spatie.permission.cache',
        'store' => 'default',
    ],
];