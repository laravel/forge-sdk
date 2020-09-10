<?php

namespace Laravel\Forge\Resources;

class LoadBalancingStrategies
{
    const ROUND_ROBIN = 'round_robin';
    const LEAST_CONNECTIONS = 'least_conn';
    const IP_HASH = 'ip_hash';
}
