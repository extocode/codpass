<?php

declare(strict_types=1);
/**
 * sysPass
 *
 * @author    nuxsmin
 * @link      https://syspass.org
 * @copyright 2012-2019, Rubén Domínguez nuxsmin@$syspass.org
 *
 * This file is part of sysPass.
 *
 * sysPass is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * sysPass is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 *  along with sysPass.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace SP\Config;

use JsonSerializable;

/**
 * Class configData
 *
 * @package SP\Config
 */
final class ConfigData implements JsonSerializable
{
    /**
     * @var string
     */
    private $upgradeKey;

    private bool $dokuwikiEnabled = false;

    /**
     * @var string
     */
    private $dokuwikiUrl;

    /**
     * @var string
     */
    private $dokuwikiUrlBase;

    /**
     * @var string
     */
    private $dokuwikiUser;

    /**
     * @var string
     */
    private $dokuwikiPass;

    /**
     * @var string
     */
    private $dokuwikiNamespace;

    private ?int $ldapDefaultGroup = null;

    private ?int $ldapDefaultProfile = null;

    private bool $proxyEnabled = false;

    /**
     * @var string
     */
    private $proxyServer;

    private int $proxyPort = 8080;

    /**
     * @var string
     */
    private $proxyUser;

    /**
     * @var string
     */
    private $proxyPass;

    private int $publinksMaxViews = 3;

    private int $publinksMaxTime = 600;

    private bool $publinksEnabled = false;

    private int $accountCount = 12;

    private bool $accountLink = true;

    private bool $checkUpdates = false;

    /**
     * @var bool
     */
    private $checknotices = false;

    private ?string $configHash = null;

    /**
     * @var string
     */
    private $dbHost;

    /**
     * @var string
     */
    private $dbSocket;

    /**
     * @var string
     */
    private $dbName;

    /**
     * @var string
     */
    private $dbPass;

    /**
     * @var string
     */
    private $dbUser;

    private int $dbPort = 3306;

    private bool $debug = false;

    private bool $demoEnabled = false;

    private array $filesAllowedExts = [];

    private array $filesAllowedMime = [];

    private int $filesAllowedSize = 1024;

    private bool $filesEnabled = true;

    private bool $globalSearch = true;

    private bool $installed = false;

    /**
     * @var string
     */
    private $ldapBase;

    /**
     * @var string
     */
    private $ldapBindUser;

    /**
     * @var string
     */
    private $ldapBindPass;

    /**
     * @var string
     */
    private $ldapProxyUser;

    private bool $ldapEnabled = false;

    private bool $ldapAds = false;

    private ?int $ldapType = null;

    /**
     * @var string
     */
    private $ldapGroup;

    /**
     * @var string
     */
    private $ldapServer;

    private bool $logEnabled = true;

    private array $logEvents = [];

    private bool $mailAuthenabled = false;

    private bool $mailEnabled = false;

    /**
     * @var string
     */
    private $mailFrom;

    /**
     * @var string
     */
    private $mailPass;

    private int $mailPort = 25;

    private bool $mailRequestsEnabled = false;

    /**
     * @var string
     */
    private $mailSecurity;

    /**
     * @var string
     */
    private $mailServer;

    /**
     * @var string
     */
    private $mailUser;

    private array $mailRecipients = [];

    /**
     * @var string
     */
    private $mailFeedback = '';

    private bool $feedbackEnabled = false;
    private bool $feedbackAuthenabled = false;
    private string $feedbackServer = '';
    private int $feedbackPort = 25;
    private string $feedbackUser = '';
    private string $feedbackPass = '';
    private string $feedbackSecurity = '';
    private string $feedbackFrom = '';

    private array $mailEvents = [];

    private bool $maintenance = false;

    /**
     * @var string
     */
    private $passwordSalt;

    private bool $resultsAsCards = false;

    private int $sessionTimeout = 300;

    /**
     * @var string
     */
    private $siteLang;

    /**
     * @var string
     */
    private $siteTheme = 'material-blue';

    /**
     * @var string
     */
    private $configVersion;

    private ?string $appVersion = null;

    /**
     * @var string
     */
    private $databaseVersion;

    private bool $wikiEnabled = false;

    /**
     * @var array
     */
    private $wikiFilter = [];

    /**
     * @var string
     */
    private $wikiPageurl;

    /**
     * @var string
     */
    private $wikiSearchurl;

    private int $configDate = 0;

    private bool $publinksImageEnabled = false;

    /**
     * @var string
     */
    private $backup_hash;

    /**
     * @var string
     */
    private $export_hash;

    private bool $httpsEnabled = false;

    private bool $syslogEnabled = false;

    private bool $syslogRemoteEnabled = false;

    /**
     * @var string
     */
    private $syslogServer;

    private int $syslogPort = 514;

    private bool $accountPassToImage = false;

    /**
     * @var string
     */
    private $configSaver;

    private bool $encryptSession = false;

    private bool $accountFullGroupAccess = false;

    /**
     * @var bool
     */
    private $authBasicEnabled = true;

    /**
     * @var bool
     */
    private $authBasicAutoLoginEnabled = true;

    /**
     * @var string
     */
    private $authBasicDomain;

    /**
     * @var int
     */
    private $ssoDefaultGroup;

    /**
     * @var int
     */
    private $ssoDefaultProfile;

    /**
     * @var bool
     */
    private $accountExpireEnabled = false;

    private int $accountExpireTime = 10368000;

    /**
     * @var bool
     */
    private $ldapTlsEnabled = false;

    private ?string $applicationUrl = null;

    public function getLogEvents(): array
    {
        return is_array($this->logEvents) ? $this->logEvents : [];
    }

    public function setLogEvents(array $logEvents): void
    {
        $this->logEvents = $logEvents;
    }

    /**
     * @return bool
     */
    public function isDokuwikiEnabled()
    {
        return $this->dokuwikiEnabled;
    }

    /**
     * @param bool $dokuwikiEnabled
     *
     * @return $this
     */
    public function setDokuwikiEnabled($dokuwikiEnabled): self
    {
        $this->dokuwikiEnabled = (bool) $dokuwikiEnabled;

        return $this;
    }

    /**
     * @return string
     */
    public function getDokuwikiUrl()
    {
        return $this->dokuwikiUrl;
    }

    /**
     * @param string $dokuwikiUrl
     *
     * @return $this
     */
    public function setDokuwikiUrl($dokuwikiUrl): self
    {
        $this->dokuwikiUrl = $dokuwikiUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getDokuwikiUrlBase()
    {
        return $this->dokuwikiUrlBase;
    }

    /**
     * @param string $dokuwikiUrlBase
     *
     * @return $this
     */
    public function setDokuwikiUrlBase($dokuwikiUrlBase): self
    {
        $this->dokuwikiUrlBase = $dokuwikiUrlBase;

        return $this;
    }

    /**
     * @return string
     */
    public function getDokuwikiUser()
    {
        return $this->dokuwikiUser;
    }

    /**
     * @param string $dokuwikiUser
     *
     * @return $this
     */
    public function setDokuwikiUser($dokuwikiUser): self
    {
        $this->dokuwikiUser = $dokuwikiUser;

        return $this;
    }

    /**
     * @return string
     */
    public function getDokuwikiPass()
    {
        return $this->dokuwikiPass;
    }

    /**
     * @param string $dokuwikiPass
     *
     * @return $this
     */
    public function setDokuwikiPass($dokuwikiPass): self
    {
        $this->dokuwikiPass = $dokuwikiPass;

        return $this;
    }

    /**
     * @return string
     */
    public function getDokuwikiNamespace()
    {
        return $this->dokuwikiNamespace;
    }

    /**
     * @param string $dokuwikiNamespace
     *
     * @return $this
     */
    public function setDokuwikiNamespace($dokuwikiNamespace): self
    {
        $this->dokuwikiNamespace = $dokuwikiNamespace;

        return $this;
    }

    public function getLdapDefaultGroup(): int
    {
        return (int) $this->ldapDefaultGroup;
    }

    /**
     * @param int $ldapDefaultGroup
     *
     * @return $this
     */
    public function setLdapDefaultGroup($ldapDefaultGroup): self
    {
        $this->ldapDefaultGroup = (int) $ldapDefaultGroup;

        return $this;
    }

    public function getLdapDefaultProfile(): int
    {
        return (int) $this->ldapDefaultProfile;
    }

    /**
     * @param int $ldapDefaultProfile
     *
     * @return $this
     */
    public function setLdapDefaultProfile($ldapDefaultProfile): self
    {
        $this->ldapDefaultProfile = (int) $ldapDefaultProfile;

        return $this;
    }

    /**
     * @return bool
     */
    public function isProxyEnabled()
    {
        return $this->proxyEnabled;
    }

    /**
     * @param bool $proxyEnabled
     *
     * @return $this
     */
    public function setProxyEnabled($proxyEnabled): self
    {
        $this->proxyEnabled = (bool) $proxyEnabled;

        return $this;
    }

    /**
     * @return string
     */
    public function getProxyServer()
    {
        return $this->proxyServer;
    }

    /**
     * @param string $proxyServer
     *
     * @return $this
     */
    public function setProxyServer($proxyServer): self
    {
        $this->proxyServer = $proxyServer;

        return $this;
    }

    /**
     * @return int
     */
    public function getProxyPort()
    {
        return $this->proxyPort;
    }

    /**
     * @param int $proxyPort
     *
     * @return $this
     */
    public function setProxyPort($proxyPort): self
    {
        $this->proxyPort = (int) $proxyPort;

        return $this;
    }

    /**
     * @return string
     */
    public function getProxyUser()
    {
        return $this->proxyUser;
    }

    /**
     * @param string $proxyUser
     *
     * @return $this
     */
    public function setProxyUser($proxyUser): self
    {
        $this->proxyUser = $proxyUser;

        return $this;
    }

    /**
     * @return string
     */
    public function getProxyPass()
    {
        return $this->proxyPass;
    }

    /**
     * @param string $proxyPass
     *
     * @return $this
     */
    public function setProxyPass($proxyPass): self
    {
        $this->proxyPass = $proxyPass;

        return $this;
    }

    /**
     * @return int
     */
    public function getPublinksMaxViews()
    {
        return $this->publinksMaxViews;
    }

    /**
     * @param int $publinksMaxViews
     *
     * @return $this
     */
    public function setPublinksMaxViews($publinksMaxViews): self
    {
        $this->publinksMaxViews = (int) $publinksMaxViews;

        return $this;
    }

    /**
     * @return int
     */
    public function getPublinksMaxTime()
    {
        return $this->publinksMaxTime;
    }

    /**
     * @param int $publinksMaxTime
     *
     * @return $this
     */
    public function setPublinksMaxTime($publinksMaxTime): self
    {
        $this->publinksMaxTime = (int) $publinksMaxTime;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSyslogEnabled()
    {
        return $this->syslogEnabled;
    }

    /**
     * @param bool $syslogEnabled
     *
     * @return $this
     */
    public function setSyslogEnabled($syslogEnabled): self
    {
        $this->syslogEnabled = (bool) $syslogEnabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSyslogRemoteEnabled()
    {
        return $this->syslogRemoteEnabled;
    }

    /**
     * @param bool $syslogRemoteEnabled
     *
     * @return $this
     */
    public function setSyslogRemoteEnabled($syslogRemoteEnabled): self
    {
        $this->syslogRemoteEnabled = (bool) $syslogRemoteEnabled;

        return $this;
    }

    /**
     * @return string
     */
    public function getSyslogServer()
    {
        return $this->syslogServer;
    }

    /**
     * @param string $syslogServer
     *
     * @return $this
     */
    public function setSyslogServer($syslogServer): self
    {
        $this->syslogServer = $syslogServer;

        return $this;
    }

    /**
     * @return int
     */
    public function getSyslogPort()
    {
        return $this->syslogPort;
    }

    /**
     * @param int $syslogPort
     *
     * @return $this
     */
    public function setSyslogPort($syslogPort): self
    {
        $this->syslogPort = (int) $syslogPort;

        return $this;
    }

    /**
     * @return string
     */
    public function getBackupHash()
    {
        return $this->backup_hash;
    }

    /**
     * @param string $backup_hash
     *
     * @return $this
     */
    public function setBackupHash($backup_hash): self
    {
        $this->backup_hash = $backup_hash;

        return $this;
    }

    /**
     * @return string
     */
    public function getExportHash()
    {
        return $this->export_hash;
    }

    /**
     * @param string $export_hash
     *
     * @return $this
     */
    public function setExportHash($export_hash): self
    {
        $this->export_hash = $export_hash;

        return $this;
    }

    /**
     * @return string
     */
    public function getLdapBindUser()
    {
        return $this->ldapBindUser;
    }

    /**
     * @param string $ldapBindUser
     *
     * @return $this
     */
    public function setLdapBindUser($ldapBindUser): self
    {
        $this->ldapBindUser = $ldapBindUser;

        return $this;
    }

    /**
     * @return string
     */
    public function getLdapProxyUser()
    {
        return $this->ldapProxyUser;
    }

    /**
     * @param string $ldapProxyUser
     *
     * @return $this
     */
    public function setLdapProxyUser($ldapProxyUser): self
    {
        $this->ldapProxyUser = $ldapProxyUser;

        return $this;
    }

    /**
     * @return int
     */
    public function getAccountCount()
    {
        return $this->accountCount;
    }

    /**
     * @param int $accountCount
     *
     * @return $this
     */
    public function setAccountCount($accountCount): self
    {
        $this->accountCount = (int) $accountCount;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAccountLink()
    {
        return $this->accountLink;
    }

    /**
     * @param bool $accountLink
     *
     * @return $this
     */
    public function setAccountLink($accountLink): self
    {
        $this->accountLink = (bool) $accountLink;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCheckUpdates()
    {
        return $this->checkUpdates;
    }

    /**
     * @param bool $checkUpdates
     *
     * @return $this
     */
    public function setCheckUpdates($checkUpdates): self
    {
        $this->checkUpdates = (bool) $checkUpdates;

        return $this;
    }

    /**
     * @return string
     */
    public function getConfigHash()
    {
        return $this->configHash;
    }

    /**
     * Generates a hash from current config options
     */
    public function setConfigHash(): self
    {
        $this->configHash = sha1(serialize($this));

        return $this;
    }

    /**
     * @return string
     */
    public function getDbHost()
    {
        return $this->dbHost;
    }

    /**
     * @param string $dbHost
     *
     * @return $this
     */
    public function setDbHost($dbHost): self
    {
        $this->dbHost = $dbHost;

        return $this;
    }

    /**
     * @return string
     */
    public function getDbName()
    {
        return $this->dbName;
    }

    /**
     * @param string $dbName
     *
     * @return $this
     */
    public function setDbName($dbName): self
    {
        $this->dbName = $dbName;

        return $this;
    }

    /**
     * @return string
     */
    public function getDbPass()
    {
        return $this->dbPass;
    }

    /**
     * @param string $dbPass
     *
     * @return $this
     */
    public function setDbPass($dbPass): self
    {
        $this->dbPass = $dbPass;

        return $this;
    }

    /**
     * @return string
     */
    public function getDbUser()
    {
        return $this->dbUser;
    }

    /**
     * @param string $dbUser
     *
     * @return $this
     */
    public function setDbUser($dbUser): self
    {
        $this->dbUser = $dbUser;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * @param bool $debug
     *
     * @return $this
     */
    public function setDebug($debug): self
    {
        $this->debug = (bool) $debug;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDemoEnabled()
    {
        return $this->demoEnabled;
    }

    /**
     * @param bool $demoEnabled
     *
     * @return $this
     */
    public function setDemoEnabled($demoEnabled): self
    {
        $this->demoEnabled = (bool) $demoEnabled;

        return $this;
    }

    public function getFilesAllowedExts(): array
    {
        return (array) $this->filesAllowedExts;
    }

    /**
     * @return int
     */
    public function getFilesAllowedSize()
    {
        return $this->filesAllowedSize;
    }

    /**
     * @param int $filesAllowedSize
     *
     * @return $this
     */
    public function setFilesAllowedSize($filesAllowedSize): self
    {
        $this->filesAllowedSize = (int) $filesAllowedSize;

        return $this;
    }

    /**
     * @return bool
     */
    public function isFilesEnabled()
    {
        return $this->filesEnabled;
    }

    /**
     * @param bool $filesEnabled
     *
     * @return $this
     */
    public function setFilesEnabled($filesEnabled): self
    {
        $this->filesEnabled = (bool) $filesEnabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function isGlobalSearch()
    {
        return $this->globalSearch;
    }

    /**
     * @param bool $globalSearch
     *
     * @return $this
     */
    public function setGlobalSearch($globalSearch): self
    {
        $this->globalSearch = (bool) $globalSearch;

        return $this;
    }

    /**
     * @return bool
     */
    public function isInstalled()
    {
        return $this->installed;
    }

    /**
     * @param bool $installed
     *
     * @return $this
     */
    public function setInstalled($installed): self
    {
        $this->installed = (bool) $installed;

        return $this;
    }

    /**
     * @return string
     */
    public function getLdapBase()
    {
        return $this->ldapBase;
    }

    /**
     * @param string $ldapBase
     *
     * @return $this
     */
    public function setLdapBase($ldapBase): self
    {
        $this->ldapBase = $ldapBase;

        return $this;
    }

    /**
     * @return bool
     */
    public function isLdapEnabled()
    {
        return $this->ldapEnabled;
    }

    /**
     * @param bool $ldapEnabled
     *
     * @return $this
     */
    public function setLdapEnabled($ldapEnabled): self
    {
        $this->ldapEnabled = (bool) $ldapEnabled;

        return $this;
    }

    /**
     * @return string
     */
    public function getLdapGroup()
    {
        return $this->ldapGroup;
    }

    /**
     * @param string $ldapGroup
     *
     * @return $this
     */
    public function setLdapGroup($ldapGroup): self
    {
        $this->ldapGroup = $ldapGroup;

        return $this;
    }

    /**
     * @return string
     */
    public function getLdapServer()
    {
        return $this->ldapServer;
    }

    /**
     * @param string $ldapServer
     *
     * @return $this
     */
    public function setLdapServer($ldapServer): self
    {
        $this->ldapServer = $ldapServer;

        return $this;
    }

    /**
     * @return bool
     */
    public function isLogEnabled()
    {
        return $this->logEnabled;
    }

    /**
     * @param bool $logEnabled
     *
     * @return $this
     */
    public function setLogEnabled($logEnabled): self
    {
        $this->logEnabled = (bool) $logEnabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMailAuthenabled()
    {
        return $this->mailAuthenabled;
    }

    /**
     * @param bool $mailAuthenabled
     *
     * @return $this
     */
    public function setMailAuthenabled($mailAuthenabled): self
    {
        $this->mailAuthenabled = (bool) $mailAuthenabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMailEnabled()
    {
        return $this->mailEnabled;
    }

    /**
     * @param bool $mailEnabled
     *
     * @return $this
     */
    public function setMailEnabled($mailEnabled): self
    {
        $this->mailEnabled = (bool) $mailEnabled;

        return $this;
    }

    /**
     * @return string
     */
    public function getMailFrom()
    {
        return $this->mailFrom;
    }

    /**
     * @param string $mailFrom
     *
     * @return $this
     */
    public function setMailFrom($mailFrom): self
    {
        $this->mailFrom = $mailFrom;

        return $this;
    }

    /**
     * @return string
     */
    public function getMailPass()
    {
        return $this->mailPass;
    }

    /**
     * @param string $mailPass
     *
     * @return $this
     */
    public function setMailPass($mailPass): self
    {
        $this->mailPass = $mailPass;

        return $this;
    }

    /**
     * @return int
     */
    public function getMailPort()
    {
        return $this->mailPort;
    }

    /**
     * @param int $mailPort
     *
     * @return $this
     */
    public function setMailPort($mailPort): self
    {
        $this->mailPort = (int) $mailPort;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMailRequestsEnabled()
    {
        return $this->mailRequestsEnabled;
    }

    /**
     * @param bool $mailRequestsEnabled
     *
     * @return $this
     */
    public function setMailRequestsEnabled($mailRequestsEnabled): self
    {
        $this->mailRequestsEnabled = (bool) $mailRequestsEnabled;

        return $this;
    }

    /**
     * @return string
     */
    public function getMailSecurity()
    {
        return $this->mailSecurity;
    }

    /**
     * @param string $mailSecurity
     *
     * @return $this
     */
    public function setMailSecurity($mailSecurity): self
    {
        $this->mailSecurity = $mailSecurity;

        return $this;
    }

    /**
     * @return string
     */
    public function getMailServer()
    {
        return $this->mailServer;
    }

    /**
     * @param string $mailServer
     *
     * @return $this
     */
    public function setMailServer($mailServer): self
    {
        $this->mailServer = $mailServer;

        return $this;
    }

    /**
     * @return string
     */
    public function getMailUser()
    {
        return $this->mailUser;
    }

    /**
     * @param string $mailUser
     *
     * @return $this
     */
    public function setMailUser($mailUser): self
    {
        $this->mailUser = $mailUser;

        return $this;
    }

    public function isMaintenance(): bool
    {
        return (bool) $this->maintenance;
    }

    /**
     * @param bool $maintenance
     *
     * @return $this
     */
    public function setMaintenance($maintenance): self
    {
        $this->maintenance = (bool) $maintenance;

        return $this;
    }

    /**
     * @return string
     */
    public function getPasswordSalt()
    {
        return $this->passwordSalt;
    }

    /**
     * @param string $passwordSalt
     *
     * @return $this
     */
    public function setPasswordSalt($passwordSalt): self
    {
        $this->passwordSalt = $passwordSalt;

        return $this;
    }

    /**
     * @return bool
     */
    public function isResultsAsCards()
    {
        return $this->resultsAsCards;
    }

    /**
     * @param bool $resultsAsCards
     *
     * @return $this
     */
    public function setResultsAsCards($resultsAsCards): self
    {
        $this->resultsAsCards = (bool) $resultsAsCards;

        return $this;
    }

    /**
     * @return int
     */
    public function getSessionTimeout()
    {
        return $this->sessionTimeout;
    }

    /**
     * @param int $sessionTimeout
     *
     * @return $this
     */
    public function setSessionTimeout($sessionTimeout): self
    {
        $this->sessionTimeout = (int) $sessionTimeout;

        return $this;
    }

    /**
     * @return string
     */
    public function getSiteLang()
    {
        return $this->siteLang;
    }

    /**
     * @param string $siteLang
     *
     * @return $this
     */
    public function setSiteLang($siteLang): self
    {
        $this->siteLang = $siteLang;

        return $this;
    }

    /**
     * @return string
     */
    public function getSiteTheme()
    {
        return $this->siteTheme;
    }

    /**
     * @param string $siteTheme
     *
     * @return $this
     */
    public function setSiteTheme($siteTheme): self
    {
        $this->siteTheme = $siteTheme;

        return $this;
    }

    public function getConfigVersion(): string
    {
        return (string) $this->configVersion;
    }

    /**
     * @param string $configVersion
     *
     * @return $this
     */
    public function setConfigVersion($configVersion): self
    {
        $this->configVersion = $configVersion;

        return $this;
    }

    /**
     * @return bool
     */
    public function isWikiEnabled()
    {
        return $this->wikiEnabled;
    }

    /**
     * @param bool $wikiEnabled
     *
     * @return $this
     */
    public function setWikiEnabled($wikiEnabled): self
    {
        $this->wikiEnabled = (bool) $wikiEnabled;

        return $this;
    }

    public function getWikiFilter(): array
    {
        return is_array($this->wikiFilter) ? $this->wikiFilter : [];
    }

    /**
     * @param array $wikiFilter
     *
     * @return $this
     */
    public function setWikiFilter($wikiFilter): self
    {
        $this->wikiFilter = $wikiFilter;

        return $this;
    }

    /**
     * @return string
     */
    public function getWikiPageurl()
    {
        return $this->wikiPageurl;
    }

    /**
     * @param string $wikiPageurl
     *
     * @return $this
     */
    public function setWikiPageurl($wikiPageurl): self
    {
        $this->wikiPageurl = $wikiPageurl;

        return $this;
    }

    /**
     * @return string
     */
    public function getWikiSearchurl()
    {
        return $this->wikiSearchurl;
    }

    /**
     * @param string $wikiSearchurl
     *
     * @return $this
     */
    public function setWikiSearchurl($wikiSearchurl): self
    {
        $this->wikiSearchurl = $wikiSearchurl;

        return $this;
    }

    /**
     * @return bool
     */
    public function isLdapAds()
    {
        return $this->ldapAds;
    }

    /**
     * @param bool $ldapAds
     *
     * @return $this
     */
    public function setLdapAds($ldapAds): self
    {
        $this->ldapAds = (bool) $ldapAds;

        return $this;
    }

    /**
     * @return string
     */
    public function getLdapBindPass()
    {
        return $this->ldapBindPass;
    }

    /**
     * @param string $ldapBindPass
     *
     * @return $this
     */
    public function setLdapBindPass($ldapBindPass): self
    {
        $this->ldapBindPass = $ldapBindPass;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPublinksImageEnabled()
    {
        return $this->publinksImageEnabled;
    }

    /**
     * @param bool $publinksImageEnabled
     *
     * @return $this
     */
    public function setPublinksImageEnabled($publinksImageEnabled): self
    {
        $this->publinksImageEnabled = (bool) $publinksImageEnabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHttpsEnabled()
    {
        return $this->httpsEnabled;
    }

    /**
     * @param bool $httpsEnabled
     *
     * @return $this
     */
    public function setHttpsEnabled($httpsEnabled): self
    {
        $this->httpsEnabled = (bool) $httpsEnabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function isChecknotices()
    {
        return $this->checknotices;
    }

    /**
     * @param bool $checknotices
     *
     * @return $this
     */
    public function setChecknotices($checknotices): self
    {
        $this->checknotices = $checknotices;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAccountPassToImage()
    {
        return $this->accountPassToImage;
    }

    /**
     * @param bool $accountPassToImage
     *
     * @return $this
     */
    public function setAccountPassToImage($accountPassToImage): self
    {
        $this->accountPassToImage = (bool) $accountPassToImage;

        return $this;
    }

    /**
     * @return string
     */
    public function getUpgradeKey()
    {
        return $this->upgradeKey;
    }

    /**
     * @param string $upgradeKey
     *
     * @return $this
     */
    public function setUpgradeKey($upgradeKey): self
    {
        $this->upgradeKey = $upgradeKey;

        return $this;
    }

    /**
     * @return int
     */
    public function getDbPort()
    {
        return $this->dbPort;
    }

    /**
     * @param int $dbPort
     *
     * @return $this
     */
    public function setDbPort($dbPort): self
    {
        $this->dbPort = (int) $dbPort;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPublinksEnabled()
    {
        return $this->publinksEnabled;
    }

    /**
     * @param bool $publinksEnabled
     *
     * @return $this
     */
    public function setPublinksEnabled($publinksEnabled): self
    {
        $this->publinksEnabled = (bool) $publinksEnabled;

        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *        which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }

    /**
     * @return string
     */
    public function getConfigSaver()
    {
        return $this->configSaver;
    }

    /**
     * @param string $configSaver
     *
     * @return $this
     */
    public function setConfigSaver($configSaver): self
    {
        $this->configSaver = $configSaver;

        return $this;
    }

    /**
     * @return string
     */
    public function getDbSocket()
    {
        return $this->dbSocket;
    }

    /**
     * @param string $dbSocket
     */
    public function setDbSocket($dbSocket): void
    {
        $this->dbSocket = $dbSocket;
    }

    public function isEncryptSession(): bool
    {
        return (bool) $this->encryptSession;
    }

    /**
     * @param bool $encryptSession
     *
     * @return $this
     */
    public function setEncryptSession($encryptSession): self
    {
        $this->encryptSession = (bool) $encryptSession;

        return $this;
    }

    public function isAccountFullGroupAccess(): bool
    {
        return (bool) $this->accountFullGroupAccess;
    }

    /**
     * @param bool $accountFullGroupAccess
     *
     * @return $this
     */
    public function setAccountFullGroupAccess($accountFullGroupAccess): self
    {
        $this->accountFullGroupAccess = (bool) $accountFullGroupAccess;

        return $this;
    }

    public function isAuthBasicEnabled(): bool
    {
        return (bool) $this->authBasicEnabled;
    }

    /**
     * @param bool $authBasicEnabled
     */
    public function setAuthBasicEnabled($authBasicEnabled): void
    {
        $this->authBasicEnabled = $authBasicEnabled;
    }

    /**
     * @return string
     */
    public function getAuthBasicDomain()
    {
        return $this->authBasicDomain;
    }

    /**
     * @param string $authBasicDomain
     */
    public function setAuthBasicDomain($authBasicDomain): void
    {
        $this->authBasicDomain = $authBasicDomain;
    }

    public function isAuthBasicAutoLoginEnabled(): bool
    {
        return (bool) $this->authBasicAutoLoginEnabled;
    }

    /**
     * @param bool $authBasicAutoLoginEnabled
     */
    public function setAuthBasicAutoLoginEnabled($authBasicAutoLoginEnabled): void
    {
        $this->authBasicAutoLoginEnabled = $authBasicAutoLoginEnabled;
    }

    /**
     * @return int
     */
    public function getSsoDefaultGroup()
    {
        return $this->ssoDefaultGroup;
    }

    /**
     * @param int $ssoDefaultGroup
     */
    public function setSsoDefaultGroup($ssoDefaultGroup): void
    {
        $this->ssoDefaultGroup = $ssoDefaultGroup;
    }

    /**
     * @return int
     */
    public function getSsoDefaultProfile()
    {
        return $this->ssoDefaultProfile;
    }

    /**
     * @param int $ssoDefaultProfile
     */
    public function setSsoDefaultProfile($ssoDefaultProfile): void
    {
        $this->ssoDefaultProfile = $ssoDefaultProfile;
    }

    public function getMailRecipients(): array
    {
        return (array) $this->mailRecipients;
    }

    public function setMailRecipients(array $mailRecipients): void
    {
        $this->mailRecipients = $mailRecipients;
    }

    public function getMailFeedback(): string
    {
        return (string) $this->mailFeedback;
    }

    public function setMailFeedback(string $mailFeedback): void
    {
        $this->mailFeedback = $mailFeedback;
    }

    public function isFeedbackEnabled(): bool
    {
        return $this->feedbackEnabled;
    }

    public function setFeedbackEnabled(bool $feedbackEnabled): self
    {
        $this->feedbackEnabled = $feedbackEnabled;
        return $this;
    }

    public function isFeedbackAuthenabled(): bool
    {
        return $this->feedbackAuthenabled;
    }

    public function setFeedbackAuthenabled(bool $feedbackAuthenabled): self
    {
        $this->feedbackAuthenabled = $feedbackAuthenabled;
        return $this;
    }

    public function getFeedbackServer(): string
    {
        return (string) $this->feedbackServer;
    }

    public function setFeedbackServer(string $feedbackServer): self
    {
        $this->feedbackServer = $feedbackServer;
        return $this;
    }

    public function getFeedbackPort(): int
    {
        return (int) $this->feedbackPort;
    }

    public function setFeedbackPort(int $feedbackPort): self
    {
        $this->feedbackPort = $feedbackPort;
        return $this;
    }

    public function getFeedbackUser(): string
    {
        return (string) $this->feedbackUser;
    }

    public function setFeedbackUser(string $feedbackUser): self
    {
        $this->feedbackUser = $feedbackUser;
        return $this;
    }

    public function getFeedbackPass(): string
    {
        return (string) $this->feedbackPass;
    }

    public function setFeedbackPass(string $feedbackPass): self
    {
        $this->feedbackPass = $feedbackPass;
        return $this;
    }

    public function getFeedbackSecurity(): string
    {
        return (string) $this->feedbackSecurity;
    }

    public function setFeedbackSecurity(string $feedbackSecurity): self
    {
        $this->feedbackSecurity = $feedbackSecurity;
        return $this;
    }

    public function getFeedbackFrom(): string
    {
        return (string) $this->feedbackFrom;
    }

    public function setFeedbackFrom(string $feedbackFrom): self
    {
        $this->feedbackFrom = $feedbackFrom;
        return $this;
    }

    public function getMailEvents(): array
    {
        return is_array($this->mailEvents) ? $this->mailEvents : [];
    }

    public function setMailEvents(array $mailEvents): void
    {
        $this->mailEvents = $mailEvents;
    }

    public function getDatabaseVersion(): string
    {
        return (string) $this->databaseVersion;
    }

    /**
     * @param string $databaseVersion
     */
    public function setDatabaseVersion($databaseVersion): self
    {
        $this->databaseVersion = $databaseVersion;

        return $this;
    }

    /**
     * @return int
     */
    public function getConfigDate()
    {
        return $this->configDate;
    }

    /**
     * @param int $configDate
     *
     * @return $this
     */
    public function setConfigDate($configDate): self
    {
        $this->configDate = (int) $configDate;

        return $this;
    }

    public function isAccountExpireEnabled(): int
    {
        return (int) $this->accountExpireEnabled;
    }

    /**
     * @param bool $accountExpireEnabled
     */
    public function setAccountExpireEnabled($accountExpireEnabled): self
    {
        $this->accountExpireEnabled = $accountExpireEnabled;

        return $this;
    }

    /**
     * @return int
     */
    public function getAccountExpireTime()
    {
        return $this->accountExpireTime;
    }

    /**
     * @param int $accountExpireTime
     */
    public function setAccountExpireTime($accountExpireTime): self
    {
        $this->accountExpireTime = (int) $accountExpireTime;

        return $this;
    }

    public function isLdapTlsEnabled(): bool
    {
        return (bool) $this->ldapTlsEnabled;
    }

    public function setLdapTlsEnabled(bool $ldapTlsEnabled): void
    {
        $this->ldapTlsEnabled = (int) $ldapTlsEnabled;
    }

    public function getFilesAllowedMime(): array
    {
        return (array) $this->filesAllowedMime;
    }

    public function setFilesAllowedMime(array $filesAllowedMime): void
    {
        $this->filesAllowedMime = $filesAllowedMime;
    }

    public function getLdapType(): int
    {
        return (int) $this->ldapType;
    }

    public function setLdapType(int $ldapType): void
    {
        $this->ldapType = $ldapType;
    }

    /**
     * @return string
     */
    public function getAppVersion()
    {
        return $this->appVersion;
    }

    public function setAppVersion(string $appVersion): void
    {
        $this->appVersion = $appVersion;
    }

    /**
     * @return string
     */
    public function getApplicationUrl()
    {
        return $this->applicationUrl;
    }

    /**
     * @param string $applicationUrl
     */
    public function setApplicationUrl(?string $applicationUrl = null): void
    {
        $this->applicationUrl = $applicationUrl ? rtrim($applicationUrl, '/') : null;
    }
}
