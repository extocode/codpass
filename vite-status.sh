#!/bin/bash
# Show Vite Build System Status

echo "╔════════════════════════════════════════════════════════════╗"
echo "║           Vite Build System Status                         ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""

# Check if Vite is enabled
if [ -f "app/modules/web/themes/material-blue/views/_layouts/main.inc.bak" ]; then
    echo "✅ Vite: ENABLED"
else
    echo "❌ Vite: DISABLED (run ./enable-vite.sh)"
fi

echo ""
echo "─────────────────────────────────────────────────────────────"
echo "Services:"
echo "─────────────────────────────────────────────────────────────"

# Check Vite dev server
if curl -s http://localhost:5173/@vite/client > /dev/null 2>&1; then
    echo "✅ Vite Dev Server: RUNNING (http://localhost:5173)"
    echo "   → HMR: Enabled"
    echo "   → Mode: Development"
else
    echo "❌ Vite Dev Server: NOT RUNNING"
    echo "   → Start with: npm run dev"
fi

echo ""

# Check PHP server
if curl -s http://localhost:8001/test-vite.php > /dev/null 2>&1; then
    echo "✅ PHP Server: RUNNING (http://localhost:8001)"
elif curl -s http://localhost:8000/test-vite.php > /dev/null 2>&1; then
    echo "✅ PHP Server: RUNNING (http://localhost:8000)"
else
    echo "❌ PHP Server: NOT RUNNING"
    echo "   → Start with: php -S localhost:8001 -t ."
fi

echo ""
echo "─────────────────────────────────────────────────────────────"
echo "Build Output:"
echo "─────────────────────────────────────────────────────────────"

if [ -f "public/dist/.vite/manifest.json" ]; then
    echo "✅ Production Build: COMPLETE"
    echo "   → Location: public/dist/"
    echo "   → Assets: $(ls public/dist/js/*.js 2>/dev/null | wc -l) JS files"
    echo "   → Styles: $(ls public/dist/css/*.css 2>/dev/null | wc -l) CSS files"
else
    echo "❌ Production Build: NOT FOUND"
    echo "   → Build with: npm run build"
fi

echo ""
echo "─────────────────────────────────────────────────────────────"
echo "Quick Links:"
echo "─────────────────────────────────────────────────────────────"
echo "📄 Test Page:      http://localhost:8001/test-vite.php"
echo "🏠 sysPass:        http://localhost:8001"
echo "⚡ Vite Server:    http://localhost:5173"
echo ""
echo "─────────────────────────────────────────────────────────────"
echo "Documentation:"
echo "─────────────────────────────────────────────────────────────"
echo "📖 Quick Start:    VITE-QUICKSTART.md"
echo "📖 Full Guide:     .claude/vite-setup-guide.md"
echo "📖 Status:         .claude/phase7-complete.md"
echo ""
