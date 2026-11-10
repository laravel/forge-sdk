<?php

declare(strict_types=1);

namespace Laravel\Forge\Enums;

enum LogKey: string
{
    case NginxAccess = 'nginx-access';
    case NginxError = 'nginx-error';
    case Redis = 'redis-server';
    case Meilisearch = 'meilisearch';
    case UnattendedUpgrades = 'unattended-upgrades';
    case Ssh = 'ssh';

    case Php5 = 'php-5';
    case Php56 = 'php-5.6';
    case Php70 = 'php-7.0';
    case Php71 = 'php-7.1';
    case Php72 = 'php-7.2';
    case Php73 = 'php-7.3';
    case Php74 = 'php-7.4';
    case Php80 = 'php-8.0';
    case Php81 = 'php-8.1';
    case Php82 = 'php-8.2';
    case Php83 = 'php-8.3';
    case Php84 = 'php-8.4';
    case Php85 = 'php-8.5';

    case Mysql = 'database-mysql';
    case MariaDb = 'database-mariadb';
    case Postgresql = 'database-postgresql';
}
