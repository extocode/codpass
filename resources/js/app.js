/**
 * sysPass - Main Application Entry Point
 *
 * This file serves as the entry point for the sysPass application.
 * It initializes the global sysPass namespace and loads all modules.
 *
 * Vite will bundle this along with all dependencies.
 */

// Import vendor dependencies
import './vendor';

// Import application modules
import './modules/config';
import './modules/util';
import './modules/triggers';
import './modules/actions';
import './modules/requests';
import './modules/main';

// Initialize global sysPass namespace
window.sysPass = window.sysPass || {};

console.log('sysPass application initialized (Vite build)');
