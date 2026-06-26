#!/bin/bash
# Disable Vite and restore legacy system

echo "Disabling Vite build system..."

if [ -f "app/modules/web/themes/material-blue/views/_layouts/main.inc.bak" ]; then
    cp app/modules/web/themes/material-blue/views/_layouts/main.inc.bak \
       app/modules/web/themes/material-blue/views/_layouts/main.inc
    echo "✓ Restored original layout"
    echo ""
    echo "Vite is now disabled. Legacy asset system restored."
else
    echo "✗ No backup found. Cannot restore."
    exit 1
fi
