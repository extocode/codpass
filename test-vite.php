<!DOCTYPE html>
<html>
<head>
    <title>Vite Test Page</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    require_once __DIR__ . '/lib/SP/Html/ViteAssets.php';
    define('PUBLIC_PATH', __DIR__ . '/public');

    use SP\Html\ViteAssets;

    // Vite assets
    echo ViteAssets::viteClient();
    echo ViteAssets::style('scss/app.scss');
    ?>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
        }
        .status.success { background: #4CAF50; color: white; }
        .status.dev { background: #2196F3; color: white; }
        pre {
            background: #f8f8f8;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
        }
        h1 { color: #333; margin-top: 0; }
        h2 { color: #666; font-size: 18px; margin-top: 30px; }
        .info { color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>🚀 Vite Build System Test</h1>

        <h2>Status</h2>
        <p>
            Mode: <span class="status <?php echo ViteAssets::isDevelopment() ? 'dev' : 'success'; ?>">
                <?php echo ViteAssets::isDevelopment() ? 'DEVELOPMENT (HMR)' : 'PRODUCTION'; ?>
            </span>
        </p>

        <h2>Asset Loading</h2>
        <div id="test-results">
            <p class="info">Checking JavaScript libraries...</p>
        </div>

        <h2>Generated HTML</h2>
        <pre><?php
echo htmlspecialchars(ViteAssets::viteClient() . "\n");
echo htmlspecialchars(ViteAssets::style('scss/app.scss') . "\n");
echo htmlspecialchars(ViteAssets::script('js/vendor.js') . "\n");
echo htmlspecialchars(ViteAssets::script('js/app.js'));
        ?></pre>

        <h2>Build Info</h2>
        <pre><?php
if (ViteAssets::isDevelopment()) {
    echo "Using Vite Dev Server:\n";
    echo "- URL: http://localhost:5173\n";
    echo "- HMR: Enabled\n";
    echo "- Source maps: Yes\n";
} else {
    echo "Using Production Build:\n";
    echo "- Location: public/dist/\n";
    echo "- Minified: Yes\n";
    echo "- Code splitting: Yes\n";
    echo "- Content hash: Yes\n";
}
        ?></pre>
    </div>

    <?php
    echo ViteAssets::script('js/vendor.js');
    echo ViteAssets::script('js/app.js');
    ?>

    <script>
        // Wait for modules to load and expose globals
        // Check every 50ms for up to 5 seconds
        let checkCount = 0;
        const maxChecks = 100;

        const checkInterval = setInterval(() => {
            checkCount++;

            // Check if at least one global is set (means modules are loading)
            if (typeof window.$ !== 'undefined' || checkCount >= maxChecks) {
                clearInterval(checkInterval);

                // Wait one more cycle to ensure all globals are set
                setTimeout(() => {
                    const results = {
                        jQuery: typeof window.$ !== 'undefined' && typeof window.jQuery !== 'undefined',
                        moment: typeof window.moment !== 'undefined',
                        toastr: typeof window.toastr !== 'undefined',
                        Clipboard: typeof window.Clipboard !== 'undefined',
                        JSEncrypt: typeof window.JSEncrypt !== 'undefined',
                        zxcvbn: typeof window.zxcvbn !== 'undefined',
                        SparkMD5: typeof window.SparkMD5 !== 'undefined'
                    };

                    const resultsDiv = document.getElementById('test-results');
                    let html = '<ul style="margin:0; padding-left: 20px;">';

                    for (const [lib, loaded] of Object.entries(results)) {
                        const status = loaded ? '✅' : '❌';
                        const color = loaded ? '#4CAF50' : '#F44336';
                        html += `<li style="color: ${color}">${status} ${lib}</li>`;
                    }

                    html += '</ul>';
                    resultsDiv.innerHTML = html;

                    console.log('Vite Test Results:', results);
                    console.log('jQuery version:', window.$?.fn?.jquery);
                    console.log('Moment version:', window.moment?.version);
                }, 100);
            }
        }, 50);
    </script>
</body>
</html>
