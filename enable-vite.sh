#!/bin/bash
# Enable Vite Frontend Build System

echo "Enabling Vite build system..."

# Backup original layout
if [ ! -f "app/modules/web/themes/material-blue/views/_layouts/main.inc.bak" ]; then
    cp app/modules/web/themes/material-blue/views/_layouts/main.inc \
       app/modules/web/themes/material-blue/views/_layouts/main.inc.bak
    echo "✓ Backed up original layout to main.inc.bak"
fi

# Replace with Vite layout
cp app/modules/web/themes/material-blue/views/_layouts/main-vite.inc \
   app/modules/web/themes/material-blue/views/_layouts/main.inc

echo "✓ Activated Vite layout"
echo ""
echo "Vite is now enabled!"
echo ""
echo "Next steps:"
echo "1. Make sure Vite dev server is running: npm run dev"
echo "2. Start PHP server: php -S localhost:8000 -t ."
echo "3. Visit: http://localhost:8000"
echo ""
echo "To disable Vite, run: ./disable-vite.sh"
