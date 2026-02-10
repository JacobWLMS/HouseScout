<?php

return [
    'api' => [
        'epc' => [
            'base_url' => env('EPC_API_BASE_URL', 'https://epc.opendatacommunities.org/api/v1'),
            'email' => env('EPC_API_EMAIL'),
            'key' => env('EPC_API_KEY'),
            'cache_ttl' => env('EPC_CACHE_TTL', 86400),
        ],
        'planning' => [
            'base_url' => env('PLANNING_API_BASE_URL', 'https://www.planning.data.gov.uk/api/v1'),
            'cache_ttl' => env('PLANNING_CACHE_TTL', 86400),
        ],
        'flood' => [
            'base_url' => env('FLOOD_API_BASE_URL', 'https://environment.data.gov.uk/flood-monitoring'),
            'cache_ttl' => env('FLOOD_CACHE_TTL', 3600),
        ],
        'police' => [
            'base_url' => env('POLICE_API_BASE_URL', 'https://data.police.uk/api'),
            'cache_ttl' => env('POLICE_CACHE_TTL', 86400),
        ],
        'land_registry' => [
            'base_url' => env('LAND_REGISTRY_API_BASE_URL', 'https://landregistry.data.gov.uk'),
            'cache_ttl' => env('LAND_REGISTRY_CACHE_TTL', 604800),
        ],
        'postcodes' => [
            'base_url' => env('POSTCODES_API_BASE_URL', 'https://api.postcodes.io'),
        ],
        'google_maps_embed' => [
            'key' => env('GOOGLE_MAPS_EMBED_API_KEY'),
        ],
    ],
    'search' => [
        'cleanup_after_days' => env('SEARCH_CLEANUP_DAYS', 90),
    ],
];
