<?php

namespace App\Enum;

enum RedisEnum: string
{
    case ALL_AVAILABLE_CURRENCIES = 'all-available-currencies';
    case GRAPH = 'graph';
    case DIJKSTRA_ROUTE = 'dijkstra-route';
    case TAG_INVALIDATE_BY_IMPORT = 'invalidate-by-import';
}
