<?php

namespace Laravel\Forge;

abstract class LoadBalancingStrategies
{
    const ROUND_ROBIN = 'round_robin';
    const LEAST_CONNECTIONS = 'least_conn';
    const IP_HASH = 'ip_hash';
}
