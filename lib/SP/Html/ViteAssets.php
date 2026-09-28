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

namespace SP\Html;

/**
 * Class ViteAssets
 *
 * Helper class to integrate Vite-built assets with PHP templates.
 *
 * Features:
 * - Development mode: Uses the Vite dev server with HMR
 * - Production mode: Reads manifest.json for asset paths with content hashes
 * - Automatic generation of script/link tags
 *
 * @package SP\Html
 */
final class ViteAssets
{
    private const VITE_DEV_SERVER = 'http://localhost:5173';
    private const MANIFEST_PATH = PUBLIC_PATH . '/dist/manifest.json';

    private static ?array $manifest = null;
    private static ?bool $isDevelopment = null;

    /**
     * Check if the Vite dev server runs
     *
     * @return bool
     */
    public static function isDevelopment(): bool
    {
        if (self::$isDevelopment === null) {
            // Dev mode is opt-in through the VITE_DEV env var. Do not probe the dev
            // server over the network here. This code runs on each page render.
            // A blocking connect to localhost:5173 (refused/filtered in prod)
            // blocks the full request.
            self::$isDevelopment = getenv('VITE_DEV') === 'true';
        }

        return self::$isDevelopment;
    }

    /**
     * Load manifest.json (production only)
     *
     * @return array
     */
    private static function getManifest(): array
    {
        if (self::$manifest === null) {
            if (file_exists(self::MANIFEST_PATH)) {
                $manifestContent = file_get_contents(self::MANIFEST_PATH);
                self::$manifest = json_decode($manifestContent, true) ?? [];
            } else {
                self::$manifest = [];
            }
        }

        return self::$manifest;
    }

    /**
     * Get asset path from manifest
     *
     * @param string $entry Entry point name (for example, 'resources/js/app.js')
     * @return string|null
     */
    private static function getAssetPath(string $entry): ?string
    {
        $manifest = self::getManifest();

        if (isset($manifest[$entry]['file'])) {
            return '/dist/' . $manifest[$entry]['file'];
        }

        return null;
    }

    /**
     * Get CSS imports for an entry
     *
     * @param string $entry
     * @return array
     */
    private static function getCssImports(string $entry): array
    {
        $manifest = self::getManifest();

        if (isset($manifest[$entry]['css']) && is_array($manifest[$entry]['css'])) {
            return array_map(fn($css) => '/dist/' . $css, $manifest[$entry]['css']);
        }

        return [];
    }

    /**
     * Generate the script tag for an entry point
     *
     * @param string $entry Entry file path (for example, 'js/app.js')
     * @return string HTML script tag
     */
    public static function script(string $entry): string
    {
        if (self::isDevelopment()) {
            // Development: Load from Vite dev server
            $entryPath = 'resources/' . $entry;
            return sprintf(
                '<script type="module" src="%s/%s"></script>',
                self::VITE_DEV_SERVER,
                $entryPath
            );
        }

        // Production: Load from manifest
        $entryKey = 'resources/' . $entry;
        $assetPath = self::getAssetPath($entryKey);

        if ($assetPath) {
            return sprintf('<script type="module" src="%s"></script>', $assetPath);
        }

        return '';
    }

    /**
     * Generate the link tag for a stylesheet
     *
     * @param string $entry Entry file path (for example, 'scss/app.scss')
     * @return string HTML link tag(s)
     */
    public static function style(string $entry): string
    {
        if (self::isDevelopment()) {
            // In dev mode, Vite injects the CSS through JS
            return '';
        }

        // Production: Load CSS from manifest
        $entryKey = 'resources/' . $entry;
        $cssFiles = self::getCssImports($entryKey);

        $tags = [];
        foreach ($cssFiles as $cssFile) {
            $tags[] = sprintf('<link rel="stylesheet" href="%s">', $cssFile);
        }

        return implode("\n", $tags);
    }

    /**
     * Generate Vite client script (development only)
     *
     * HMR needs this script
     *
     * @return string
     */
    public static function viteClient(): string
    {
        if (self::isDevelopment()) {
            return sprintf(
                '<script type="module" src="%s/@vite/client"></script>',
                self::VITE_DEV_SERVER
            );
        }

        return '';
    }

    /**
     * Generate all the necessary tags for an entry point
     *
     * Includes the Vite client (dev), the scripts and the stylesheets
     *
     * @param string $entry Entry file path
     * @return string HTML tags
     */
    public static function tags(string $entry): string
    {
        $output = [];

        // Vite client for HMR
        if ($client = self::viteClient()) {
            $output[] = $client;
        }

        // Stylesheet
        if ($style = self::style($entry)) {
            $output[] = $style;
        }

        // Script
        if ($script = self::script($entry)) {
            $output[] = $script;
        }

        return implode("\n", $output);
    }
}
