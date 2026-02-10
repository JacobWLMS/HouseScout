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
    'checklist' => [
        'items' => [
            ['key' => 'epc_rating', 'label' => 'Energy Rating', 'category' => 'energy', 'is_deal_breaker' => false, 'auto' => true],
            ['key' => 'epc_costs', 'label' => 'Running Costs', 'category' => 'energy', 'is_deal_breaker' => false, 'auto' => true],
            ['key' => 'flood_risk', 'label' => 'Flood Risk Level', 'category' => 'flood', 'is_deal_breaker' => true, 'auto' => true],
            ['key' => 'flood_warnings', 'label' => 'Active Flood Warnings', 'category' => 'flood', 'is_deal_breaker' => true, 'auto' => true],
            ['key' => 'crime_level', 'label' => 'Crime Level', 'category' => 'crime', 'is_deal_breaker' => false, 'auto' => true],
            ['key' => 'planning_issues', 'label' => 'Nearby Planning Activity', 'category' => 'planning', 'is_deal_breaker' => false, 'auto' => true],
            ['key' => 'price_history', 'label' => 'Price Trend', 'category' => 'sales', 'is_deal_breaker' => false, 'auto' => true],
            ['key' => 'structural', 'label' => 'Structural Condition', 'category' => 'survey', 'is_deal_breaker' => true, 'auto' => false],
            ['key' => 'damp', 'label' => 'Damp / Moisture', 'category' => 'survey', 'is_deal_breaker' => true, 'auto' => false],
            ['key' => 'roof_condition', 'label' => 'Roof Condition', 'category' => 'survey', 'is_deal_breaker' => false, 'auto' => false],
            ['key' => 'parking', 'label' => 'Parking', 'category' => 'location', 'is_deal_breaker' => false, 'auto' => false],
            ['key' => 'noise', 'label' => 'Noise Level', 'category' => 'location', 'is_deal_breaker' => false, 'auto' => false],
            ['key' => 'neighbours', 'label' => 'Neighbours', 'category' => 'location', 'is_deal_breaker' => false, 'auto' => false],
            ['key' => 'garden', 'label' => 'Garden / Outdoor Space', 'category' => 'location', 'is_deal_breaker' => false, 'auto' => false],
            ['key' => 'commute', 'label' => 'Commute Time', 'category' => 'location', 'is_deal_breaker' => false, 'auto' => false],
            ['key' => 'local_amenities', 'label' => 'Local Amenities', 'category' => 'location', 'is_deal_breaker' => false, 'auto' => false],
            ['key' => 'schools_nearby', 'label' => 'Schools Nearby', 'category' => 'schools', 'is_deal_breaker' => false, 'auto' => false],
            ['key' => 'broadband', 'label' => 'Broadband Speed', 'category' => 'broadband', 'is_deal_breaker' => false, 'auto' => false],
        ],
    ],
];
