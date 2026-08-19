<?php

return [
    'token_header' => env('RUP_API_APPLICATION_TOKEN_HEADER', 'X-Rup-Application-Token'),
    'token_ttl_minutes' => (int) env('RUP_APPLICATION_TOKEN_TTL_MINUTES', 60),
    'require_https' => (bool) env('RUP_API_REQUIRE_HTTPS', false),
    'require_ip_allowlist' => (bool) env('RUP_API_REQUIRE_CLIENT_IP_ALLOWLIST', false),
];