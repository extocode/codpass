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

namespace SP\Core;

use SP\Config\Config;
use SP\Config\ConfigData;
use SP\Core\Context\ContextInterface;
use SP\Core\Context\SessionContext;
use SP\Http\Request;

defined('APP_ROOT') || die();

/**
 * Class Language para el manejo del lenguaje utilizado por la aplicación
 *
 * @package SP
 */
final class Language
{
    /**
     * Lenguaje del usuario
     *
     * @var string
     */
    public static $userLang = '';

    /**
     * Lenguaje global de la Aplicación
     *
     * @var string
     */
    public static $globalLang = '';

    /**
     * Estado de la localización. false si no existe
     *
     * @var string|false
     */
    public static $localeStatus;

    /**
     * Si se ha establecido a las de la App
     *
     * @var bool
     */
    protected static $appSet = false;

    /**
     * @var array Available languages (English first, rest sorted alphabetically by key)
     */
    private static array $langs = [
        'en_US' => 'English',
        'ca_ES' => 'Catalá',
        'da' => 'Dansk',
        'de_DE' => 'Deutsch',
        'es_ES' => 'Español',
        'fo' => 'Føroyskt mál',
        'fr_FR' => 'Français',
        'hu_HU' => 'Magyar',
        'it_IT' => 'Italiano',
        'ja_JP' => '日本語',
        'nl_NL' => 'Nederlands',
        'pl_PL' => 'Polski',
        'pt_BR' => 'Português',
        'ru_RU' => 'русский',
    ];

    /**
     * @var ConfigData
     */
    protected $configData;

    /**
     * @var  SessionContext
     */
    protected $context;

    private readonly \SP\Http\Request $request;

    /**
     * Language constructor.
     */
    public function __construct(ContextInterface $session, Config $config, Request $request)
    {
        $this->context = $session;
        $this->configData = $config->getConfigData();
        $this->request = $request;
    }

    /**
     * Devolver los lenguajes disponibles
     *
     * @return array
     */
    public static function getAvailableLanguages()
    {
        return self::$langs;
    }

    /**
     * Establecer el lenguaje a utilizar
     *
     * @param bool $force Forzar la detección del lenguaje para los inicios de sesión
     */
    public function setLanguage($force = false): void
    {
        $lang = $this->context->getLocale();

        if (empty($lang) || $force === true) {
            self::$userLang = $this->getUserLang();
            self::$globalLang = $this->getGlobalLang();

            $lang = self::$userLang ?: self::$globalLang;

            $this->context->setLocale($lang);
        }

        self::setLocales($lang);
    }

    /**
     * Devuelve el lenguaje del usuario
     *
     * @return string
     */
    private function getUserLang()
    {
        $userData = $this->context->getUserData();

        return ($userData->getId() > 0) ? $userData->getPreferences()->getLang() : '';
    }

    /**
     * Establece el lenguaje de la aplicación.
     * Esta función establece el lenguaje según esté definido en la configuración o en el navegador.
     */
    private function getGlobalLang()
    {
        return $this->configData->getSiteLang() ?: $this->getBrowserLang();
    }

    /**
     * Devolver el lenguaje que acepta el navegador
     *
     * @return string
     */
    private function getBrowserLang(): string|array
    {
        $lang = $this->request->getHeader('Accept-Language');

        return $lang !== '' ? str_replace('-', '_', substr($lang, 0, 5)) : 'en_US';
    }

    /**
     * Establecer las locales de gettext
     *
     * @param string $lang El lenguaje a utilizar
     */
    public static function setLocales($lang): void
    {
        $lang .= '.utf8';

        self::$localeStatus = setlocale(LC_MESSAGES, $lang);

        putenv('LANG=' . $lang);
        putenv('LANGUAGE=' . $lang);

        $locale = setlocale(LC_ALL, $lang);

        if ($locale === false) {
            logger('Could not set locale to ' . $lang, 'ERROR');
            logger('Domain path: ' . LOCALES_PATH);
        } else {
            logger('Locale set to: ' . $locale);
        }

        bindtextdomain('messages', LOCALES_PATH);
        textdomain('messages');
        bind_textdomain_codeset('messages', 'UTF-8');
    }

    /**
     * Establecer el lenguaje global para las traducciones
     */
    public function setAppLocales(): void
    {
        if ($this->configData->getSiteLang() !== $this->context->getLocale()) {
            self::setLocales($this->configData->getSiteLang());

            self::$appSet = true;
        }
    }

    /**
     * Restablecer el lenguaje global para las traducciones
     */
    public function unsetAppLocales(): void
    {
        if (self::$appSet === true) {
            self::setLocales($this->context->getLocale());

            self::$appSet = false;
        }
    }
}
