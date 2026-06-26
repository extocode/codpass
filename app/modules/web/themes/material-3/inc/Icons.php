<?php

declare(strict_types=1);
/**
 * Material Design 3 Theme - Icons
 *
 * Uses Material Symbols Rounded icons with periwinkle accent colors.
 */

defined('APP_ROOT') || die();

use SP\Core\UI\ThemeIcons;
use SP\Html\Assets\FontIcon;

$themeIcons = new ThemeIcons();

// Primary actions - Periwinkle
$themeIcons->addIcon('add', new FontIcon('add', 'md3-color-primary', __u('Add')));
$themeIcons->addIcon('view', new FontIcon('visibility', 'md3-color-primary', __u('View Details')));
$themeIcons->addIcon('viewPass', new FontIcon('lock_open', 'md3-color-primary', __u('View password')));
$themeIcons->addIcon('copy', new FontIcon('content_copy', 'md3-color-primary', __u('Copy')));
$themeIcons->addIcon('clipboard', new FontIcon('content_paste', 'md3-color-primary'));
$themeIcons->addIcon('email', new FontIcon('email', 'md3-color-primary', __u('Email')));
$themeIcons->addIcon('back', new FontIcon('arrow_back', 'md3-color-primary', __u('Back')));
$themeIcons->addIcon('download', new FontIcon('file_download', 'md3-color-primary', __u('Download')));
$themeIcons->addIcon('check', new FontIcon('cached', 'md3-color-primary', __u('Check')));
$themeIcons->addIcon('search', new FontIcon('search', 'md3-color-primary', __u('Search')));
$themeIcons->addIcon('account', new FontIcon('account_box', 'md3-color-primary'));
$themeIcons->addIcon('group', new FontIcon('group_work', 'md3-color-primary'));
$themeIcons->addIcon('settings', new FontIcon('settings', 'md3-color-primary', __u('Configuration')));
$themeIcons->addIcon('headline', new FontIcon('view_headline', 'md3-color-primary'));
$themeIcons->addIcon('info', new FontIcon('info', 'md3-color-primary', __u('Information')));
$themeIcons->addIcon('notices', new FontIcon('notifications', 'md3-color-primary', __u('Notifications')));
$themeIcons->addIcon('remove', new FontIcon('remove', 'md3-color-primary', __u('Delete')));
$themeIcons->addIcon('clear', new FontIcon('close', 'md3-color-primary', __u('Clear')));

// Edit actions - Amber/Warning
$themeIcons->addIcon('edit', new FontIcon('edit', 'md3-color-warning', __u('Edit')));
$themeIcons->addIcon('editPass', new FontIcon('lock', 'md3-color-warning', __u('Change Password')));
$themeIcons->addIcon('warning', new FontIcon('warning', 'md3-color-warning', __u('Warning')));

// Delete/Error actions - Error red
$themeIcons->addIcon('delete', new FontIcon('delete', 'md3-color-error', __u('Delete')));
$themeIcons->addIcon('disabled', new FontIcon('error', 'md3-color-error', __u('Disabled')));
$themeIcons->addIcon('critical', new FontIcon('error', 'md3-color-error', __u('Critical')));

// Success actions - Teal/Success
$themeIcons->addIcon('enabled', new FontIcon('check_circle', 'md3-color-success', __u('Enabled')));
$themeIcons->addIcon('refresh', new FontIcon('refresh', 'md3-color-success', __u('Update')));
$themeIcons->addIcon('restore', new FontIcon('restore', 'md3-color-success', __u('Restore')));
$themeIcons->addIcon('save', new FontIcon('save', 'md3-color-success', __u('Save')));
$themeIcons->addIcon('play', new FontIcon('play_circle', 'md3-color-success', __u('Perform')));
$themeIcons->addIcon('publicLink', new FontIcon('link', 'md3-color-success'));

// Admin icons
$themeIcons->addIcon('appAdmin', new FontIcon('star', 'md3-color-warning', __u('Application Admin')));
$themeIcons->addIcon('accAdmin', new FontIcon('star_half', 'md3-color-warning', __u('Accounts Admin')));
$themeIcons->addIcon('ldapUser', new FontIcon('business', 'md3-color-tertiary', __u('LDAP User')));

// Utility icons - No color
$themeIcons->addIcon('optional', new FontIcon('settings'));
$themeIcons->addIcon('help', new FontIcon('help', 'md3-color-on-surface-variant', __u('Help')));
$themeIcons->addIcon('previous', new FontIcon('chevron_left', null, __u('Previous page')));
$themeIcons->addIcon('next', new FontIcon('chevron_right', null, __u('Next page')));
$themeIcons->addIcon('first', new FontIcon('first_page', null, __u('First page')));
$themeIcons->addIcon('last', new FontIcon('last_page', null, __u('Last page')));
$themeIcons->addIcon('up', new FontIcon('arrow_drop_up'));
$themeIcons->addIcon('down', new FontIcon('arrow_drop_down'));

return $themeIcons;
