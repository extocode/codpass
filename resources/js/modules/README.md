# sysPass JavaScript Modules

This directory contains the modularized version of sysPass JavaScript code.

## Migration Plan

The original files from `/public/js/` will be gradually migrated here as ES modules:

- `app.js` → Namespace initialization (moved to main `app.js`)
- `app-config.js` → `config.js` (configuration object)
- `app-util.js` → `util.js` (utility functions)
- `app-triggers.js` → `triggers.js` (event triggers)
- `app-actions.js` → `actions.js` (user action handlers)
- `app-requests.js` → `requests.js` (AJAX requests)
- `app-main.js` → `main.js` (main application logic)

## Current Status

For Phase 7 initial setup, these are placeholder modules that will import the existing compiled code.
Full ES6 module conversion is a follow-up task.
