<?php

namespace Laravel\Forge\Resources;

class ServerTypes
{
    const APP = 'app';
    const WEB = 'web';
    const LOADBALANCER = 'loadbalancer';
    const CACHE = 'cache';
    const DATABASE = 'database';
    const WORKER = 'worker';
    const MEILISEARCH = 'meilisearch';
}
