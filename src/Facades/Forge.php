<?php

declare(strict_types=1);

namespace Laravel\Forge\Facades;

use Illuminate\Support\Facades\Facade;
use Laravel\Forge\ForgeManager;

/**
 * @method static \Laravel\Forge\Forge setApiKey(string $apiKey, \GuzzleHttp\Client|null $guzzle = null)
 * @method static \Laravel\Forge\Forge setTimeout(int $timeout)
 * @method static int getTimeout()
 * @method static \Laravel\Forge\Resources\User user()
 * @method static \Laravel\Forge\Resources\User me()
 *
 * Organizations
 * @method static \Laravel\Forge\CursorPaginator organizations()
 * @method static \Laravel\Forge\Resources\Organization organization(string $organizationSlug)
 *
 * Servers
 * @method static \Laravel\Forge\CursorPaginator servers(string $organizationSlug)
 * @method static \Laravel\Forge\Resources\Server server(string $organizationSlug, int $serverId)
 * @method static \Laravel\Forge\Resources\Server createServer(string $organizationSlug, array $data, bool $wait = true)
 * @method static void deleteServer(string $organizationSlug, int $serverId)
 * @method static \Laravel\Forge\CursorPaginator archivedServers(string $organizationSlug)
 * @method static \Laravel\Forge\Resources\Server createArchivedServer(string $organizationSlug, array $data)
 * @method static void deleteArchivedServer(string $organizationSlug, int $serverId)
 * @method static array createServerAction(string $organizationSlug, int $serverId, array $data)
 * @method static array performNginxAction(string $organizationSlug, int $serverId, array $data)
 * @method static array performPostgresAction(string $organizationSlug, int $serverId, array $data)
 * @method static array performRedisAction(string $organizationSlug, int $serverId, array $data)
 * @method static array performMySQLAction(string $organizationSlug, int $serverId, array $data)
 * @method static array performPHPAction(string $organizationSlug, int $serverId, array $data)
 * @method static array performSupervisorAction(string $organizationSlug, int $serverId, array $data)
 * @method static \Laravel\Forge\CursorPaginator serverEvents(string $organizationSlug, int $serverId)
 * @method static \Laravel\Forge\Resources\Event serverEvent(string $organizationSlug, int $serverId, int $eventId)
 * @method static array serverEventOutput(string $organizationSlug, int $serverId, int $eventId)
 *
 * PHP
 * @method static \Laravel\Forge\CursorPaginator phpVersions(string $organizationSlug, int $serverId)
 * @method static \Laravel\Forge\Resources\PHPVersion phpVersion(string $organizationSlug, int $serverId, int $phpVersionId)
 * @method static \Laravel\Forge\Resources\PHPVersion installPhpVersion(string $organizationSlug, int $serverId, array $data)
 * @method static \Laravel\Forge\Resources\PHPVersion updatePhpVersion(string $organizationSlug, int $serverId, int $phpVersionId, array $data)
 * @method static void deletePhpVersion(string $organizationSlug, int $serverId, int $phpVersionId)
 * @method static array phpOpcache(string $organizationSlug, int $serverId)
 * @method static array createPhpOpcache(string $organizationSlug, int $serverId, array $data)
 * @method static void deletePhpOpcache(string $organizationSlug, int $serverId)
 *
 * Sites
 * @method static \Laravel\Forge\CursorPaginator sites()
 * @method static \Laravel\Forge\CursorPaginator organizationSites(string $organizationSlug)
 * @method static \Laravel\Forge\Resources\Site organizationSite(string $organizationSlug, int $siteId)
 * @method static \Laravel\Forge\CursorPaginator serverSites(string $organizationSlug, int $serverId)
 * @method static \Laravel\Forge\Resources\Site createSite(string $organizationSlug, int $serverId, array $data)
 * @method static \Laravel\Forge\Resources\Site updateSite(string $organizationSlug, int $serverId, int $siteId, array $data)
 * @method static void deleteSite(string $organizationSlug, int $serverId, int $siteId)
 *
 * Domains
 * @method static \Laravel\Forge\CursorPaginator domains(string $organizationSlug, int $serverId, int $siteId)
 * @method static \Laravel\Forge\Resources\Domain domain(string $organizationSlug, int $serverId, int $siteId, int $domainId)
 * @method static \Laravel\Forge\Resources\Domain createDomain(string $organizationSlug, int $serverId, int $siteId, array $data)
 * @method static \Laravel\Forge\Resources\Domain updateDomain(string $organizationSlug, int $serverId, int $siteId, int $domainId, array $data)
 * @method static void deleteDomain(string $organizationSlug, int $serverId, int $siteId, int $domainId)
 * @method static \Laravel\Forge\Resources\Certificate domainCertificate(string $organizationSlug, int $serverId, int $siteId, int $domainId)
 * @method static \Laravel\Forge\Resources\Certificate createDomainCertificate(string $organizationSlug, int $serverId, int $siteId, int $domainId, array $data)
 * @method static void deleteDomainCertificate(string $organizationSlug, int $serverId, int $siteId, int $domainId)
 *
 * Heartbeats
 * @method static \Laravel\Forge\CursorPaginator heartbeats(string $organizationSlug, int $serverId, int $siteId)
 * @method static \Laravel\Forge\Resources\Heartbeat heartbeat(string $organizationSlug, int $serverId, int $siteId, int $heartbeatId)
 * @method static \Laravel\Forge\Resources\Heartbeat createHeartbeat(string $organizationSlug, int $serverId, int $siteId, array $data)
 * @method static \Laravel\Forge\Resources\Heartbeat updateHeartbeat(string $organizationSlug, int $serverId, int $siteId, int $heartbeatId, array $data)
 * @method static void deleteHeartbeat(string $organizationSlug, int $serverId, int $siteId, int $heartbeatId)
 *
 * Databases
 * @method static \Laravel\Forge\CursorPaginator databases(string $organizationSlug, int $serverId)
 * @method static \Laravel\Forge\Resources\Database database(string $organizationSlug, int $serverId, int $databaseId)
 * @method static \Laravel\Forge\Resources\Database createDatabase(string $organizationSlug, int $serverId, array $data, bool $wait = true)
 * @method static void deleteDatabase(string $organizationSlug, int $serverId, int $databaseId)
 * @method static \Laravel\Forge\CursorPaginator databaseUsers(string $organizationSlug, int $serverId)
 * @method static \Laravel\Forge\Resources\DatabaseUser databaseUser(string $organizationSlug, int $serverId, int $userId)
 * @method static \Laravel\Forge\Resources\DatabaseUser createDatabaseUser(string $organizationSlug, int $serverId, array $data, bool $wait = true)
 * @method static \Laravel\Forge\Resources\DatabaseUser updateDatabaseUser(string $organizationSlug, int $serverId, int $userId, array $data)
 * @method static void deleteDatabaseUser(string $organizationSlug, int $serverId, int $userId)
 *
 * Deployments
 * @method static \Laravel\Forge\CursorPaginator deployments(string $organizationSlug, int $serverId, int $siteId)
 * @method static \Laravel\Forge\Resources\Deployment deployment(string $organizationSlug, int $serverId, int $siteId, int $deploymentId)
 * @method static \Laravel\Forge\Resources\Deployment createDeployment(string $organizationSlug, int $serverId, int $siteId, array $data = [])
 * @method static string deploymentScript(string $organizationSlug, int $serverId, int $siteId)
 * @method static void updateDeploymentScript(string $organizationSlug, int $serverId, int $siteId, array $data)
 * @method static void disableQuickDeploy(string $organizationSlug, int $serverId, int $siteId)
 * @method static string deploymentLog(string $organizationSlug, int $serverId, int $siteId, int $deploymentId)
 *
 * Webhooks
 * @method static \Laravel\Forge\CursorPaginator webhooks(string $organizationSlug, int $serverId, int $siteId)
 * @method static \Laravel\Forge\Resources\Webhook webhook(string $organizationSlug, int $serverId, int $siteId, int $webhookId)
 * @method static \Laravel\Forge\Resources\Webhook createWebhook(string $organizationSlug, int $serverId, int $siteId, array $data)
 * @method static void deleteWebhook(string $organizationSlug, int $serverId, int $siteId, int $webhookId)
 *
 * SSH Keys
 * @method static \Laravel\Forge\CursorPaginator sshKeys(string $organizationSlug, int $serverId)
 * @method static \Laravel\Forge\Resources\SSHKey sshKey(string $organizationSlug, int $serverId, int $keyId)
 * @method static void createSshKey(string $organizationSlug, int $serverId, array $data)
 * @method static void deleteSshKey(string $organizationSlug, int $serverId, int $keyId)
 *
 * Firewall Rules
 * @method static \Laravel\Forge\CursorPaginator firewallRules(string $organizationSlug, int $serverId)
 * @method static \Laravel\Forge\Resources\FirewallRule firewallRule(string $organizationSlug, int $serverId, int $ruleId)
 * @method static \Laravel\Forge\Resources\FirewallRule createFirewallRule(string $organizationSlug, int $serverId, array $data)
 * @method static void deleteFirewallRule(string $organizationSlug, int $serverId, int $ruleId)
 *
 * Redirect Rules
 * @method static \Laravel\Forge\CursorPaginator redirectRules(string $organizationSlug, int $serverId, int $siteId)
 * @method static \Laravel\Forge\Resources\RedirectRule redirectRule(string $organizationSlug, int $serverId, int $siteId, int $ruleId)
 * @method static \Laravel\Forge\Resources\RedirectRule createRedirectRule(string $organizationSlug, int $serverId, int $siteId, array $data)
 * @method static void deleteRedirectRule(string $organizationSlug, int $serverId, int $siteId, int $ruleId)
 *
 * Security Rules
 * @method static \Laravel\Forge\CursorPaginator securityRules(string $organizationSlug, int $serverId, int $siteId)
 * @method static \Laravel\Forge\Resources\SecurityRule securityRule(string $organizationSlug, int $serverId, int $siteId, int $ruleId)
 * @method static \Laravel\Forge\Resources\SecurityRule createSecurityRule(string $organizationSlug, int $serverId, int $siteId, array $data)
 * @method static void deleteSecurityRule(string $organizationSlug, int $serverId, int $siteId, int $ruleId)
 *
 * Monitors
 * @method static \Laravel\Forge\CursorPaginator monitors(string $organizationSlug, int $serverId)
 * @method static \Laravel\Forge\Resources\Monitor monitor(string $organizationSlug, int $serverId, int $monitorId)
 * @method static void createMonitor(string $organizationSlug, int $serverId, array $data)
 * @method static void deleteMonitor(string $organizationSlug, int $serverId, int $monitorId)
 *
 * Nginx Templates
 * @method static \Laravel\Forge\CursorPaginator nginxTemplates(string $organizationSlug, int $serverId)
 * @method static \Laravel\Forge\Resources\NginxTemplate nginxTemplate(string $organizationSlug, int $serverId, int $templateId)
 * @method static \Laravel\Forge\Resources\NginxTemplate createNginxTemplate(string $organizationSlug, int $serverId, array $data)
 * @method static \Laravel\Forge\Resources\NginxTemplate updateNginxTemplate(string $organizationSlug, int $serverId, int $templateId, array $data)
 * @method static void deleteNginxTemplate(string $organizationSlug, int $serverId, int $templateId)
 *
 * Recipes
 * @method static \Laravel\Forge\CursorPaginator recipes(string $organizationSlug)
 * @method static \Laravel\Forge\Resources\Recipe recipe(string $organizationSlug, int $recipeId)
 * @method static \Laravel\Forge\Resources\Recipe createRecipe(string $organizationSlug, array $data)
 * @method static \Laravel\Forge\Resources\Recipe updateRecipe(string $organizationSlug, int $recipeId, array $data)
 * @method static void deleteRecipe(string $organizationSlug, int $recipeId)
 * @method static \Laravel\Forge\Resources\RecipeRun createRecipeRun(string $organizationSlug, int $recipeId, array $data)
 *
 * Scheduled Jobs
 * @method static \Laravel\Forge\CursorPaginator scheduledJobs(string $organizationSlug, int $serverId)
 * @method static \Laravel\Forge\Resources\ScheduledJob scheduledJob(string $organizationSlug, int $serverId, int $jobId)
 * @method static \Laravel\Forge\Resources\ScheduledJob createScheduledJob(string $organizationSlug, int $serverId, array $data)
 * @method static void deleteScheduledJob(string $organizationSlug, int $serverId, int $jobId)
 *
 * Background Processes
 * @method static \Laravel\Forge\CursorPaginator backgroundProcesses(string $organizationSlug, int $serverId)
 * @method static \Laravel\Forge\Resources\BackgroundProcess backgroundProcess(string $organizationSlug, int $serverId, int $processId)
 * @method static \Laravel\Forge\Resources\BackgroundProcess createBackgroundProcess(string $organizationSlug, int $serverId, array $data)
 * @method static \Laravel\Forge\Resources\BackgroundProcess updateBackgroundProcess(string $organizationSlug, int $serverId, int $processId, array $data)
 * @method static void deleteBackgroundProcess(string $organizationSlug, int $serverId, int $processId)
 *
 * Commands
 * @method static \Laravel\Forge\CursorPaginator commands(string $organizationSlug, int $serverId, int $siteId)
 * @method static \Laravel\Forge\Resources\Command command(string $organizationSlug, int $serverId, int $siteId, int $commandId)
 * @method static void createCommand(string $organizationSlug, int $serverId, int $siteId, array $data)
 * @method static void deleteCommand(string $organizationSlug, int $serverId, int $siteId, int $commandId)
 *
 * Backups
 * @method static \Laravel\Forge\CursorPaginator backupConfigurations(string $organizationSlug, int $serverId)
 * @method static \Laravel\Forge\Resources\BackupConfiguration backupConfiguration(string $organizationSlug, int $serverId, int $backupConfigurationId)
 * @method static void createBackupConfiguration(string $organizationSlug, int $serverId, array $data)
 * @method static void updateBackupConfiguration(string $organizationSlug, int $serverId, int $backupConfigurationId, array $data)
 * @method static void deleteBackupConfiguration(string $organizationSlug, int $serverId, int $backupConfigurationId)
 * @method static \Laravel\Forge\CursorPaginator backups(string $organizationSlug, int $serverId, int $backupConfigurationId)
 * @method static \Laravel\Forge\Resources\Backup backup(string $organizationSlug, int $serverId, int $backupConfigurationId, int $backupId)
 * @method static void createBackup(string $organizationSlug, int $serverId, int $backupConfigurationId)
 * @method static void deleteBackup(string $organizationSlug, int $serverId, int $backupConfigurationId, int $backupId)
 * @method static void restoreBackup(string $organizationSlug, int $serverId, int $backupConfigurationId, int $backupId, array $data)
 *
 * HTTP Methods
 * @method static mixed get(string $uri, array $query = [])
 * @method static mixed post(string $uri, array $payload = [])
 * @method static mixed put(string $uri, array $payload = [])
 * @method static mixed patch(string $uri, array $payload = [])
 * @method static mixed delete(string $uri, array $payload = [])
 * @method static mixed retry(int $timeout, callable $callback, int $sleep = 5)
 *
 * @see \Laravel\Forge\Forge
 */
class Forge extends Facade
{
    /**
     * Get the registered name of the component.
     */
    public static function getFacadeAccessor(): string
    {
        return ForgeManager::class;
    }
}
