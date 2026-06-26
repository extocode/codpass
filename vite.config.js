import { defineConfig } from 'vite';
import { resolve } from 'path';
import legacy from '@vitejs/plugin-legacy';

export default defineConfig({
  plugins: [
    // Support for older browsers (IE11, old Safari, etc.)
    legacy({
      targets: ['defaults', 'not IE 11'],
      additionalLegacyPolyfills: ['regenerator-runtime/runtime']
    })
  ],

  // Root directory for assets
  root: 'resources',

  // Base public path
  base: process.env.NODE_ENV === 'production' ? '/dist/' : '/',

  // Build configuration
  build: {
    // Output directory relative to project root
    outDir: '../public/dist',
    emptyOutDir: true,

    // Generate manifest for PHP integration
    manifest: true,

    // Asset inlining threshold (8kb)
    assetsInlineLimit: 8192,

    rollupOptions: {
      input: {
        // Main application entry points
        main: resolve(__dirname, 'resources/js/app.js'),
        theme: resolve(__dirname, 'resources/js/theme.js'),

        // Separate vendor bundle for better caching
        vendor: resolve(__dirname, 'resources/js/vendor.js'),

        // Main stylesheet
        styles: resolve(__dirname, 'resources/scss/app.scss')
      },
      output: {
        // Chunking strategy for better caching
        manualChunks: {
          'jquery-core': ['jquery'],
          'date-utils': ['moment', 'moment-timezone'],
          'ui-components': ['tom-select', 'toastr', 'glightbox'],
          'crypto': ['jsencrypt', 'spark-md5', 'zxcvbn']
        },

        // Asset naming with content hash for cache busting
        entryFileNames: 'js/[name].[hash].js',
        chunkFileNames: 'js/[name].[hash].js',
        assetFileNames: (assetInfo) => {
          const info = assetInfo.name.split('.');
          const ext = info[info.length - 1];

          if (/\.(woff2?|eot|ttf|otf)(\?.*)?$/i.test(assetInfo.name)) {
            return 'fonts/[name].[hash][extname]';
          }

          if (/\.(png|jpe?g|gif|svg|webp|ico)(\?.*)?$/i.test(assetInfo.name)) {
            return 'images/[name].[hash][extname]';
          }

          if (/\.css$/i.test(assetInfo.name)) {
            return 'css/[name].[hash][extname]';
          }

          return 'assets/[name].[hash][extname]';
        }
      }
    },

    // Sourcemaps for production debugging
    sourcemap: process.env.NODE_ENV === 'development',

    // Minification
    minify: 'terser',
    terserOptions: {
      compress: {
        drop_console: process.env.NODE_ENV === 'production',
        drop_debugger: process.env.NODE_ENV === 'production'
      }
    },

    // Chunk size warnings
    chunkSizeWarningLimit: 1000
  },

  // Development server configuration
  server: {
    host: 'localhost',
    port: 5173,
    strictPort: false,

    // CORS for PHP dev server
    cors: {
      origin: '*',
      methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
      allowedHeaders: ['Content-Type', 'Authorization'],
      credentials: true
    },

    // HMR (Hot Module Replacement)
    hmr: {
      host: 'localhost',
      port: 5173,
      protocol: 'ws'
    },

    // Proxy API requests to PHP server
    proxy: {
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true
      },
      '/index.php': {
        target: 'http://localhost:8000',
        changeOrigin: true
      }
    },

    watch: {
      usePolling: true,
      interval: 100
    }
  },

  // CSS preprocessing
  css: {
    preprocessorOptions: {
      scss: {
        additionalData: `@import "./resources/scss/_variables.scss";`
      }
    },
    devSourcemap: true
  },

  // Resolve aliases
  resolve: {
    alias: {
      '@': resolve(__dirname, 'resources'),
      '@js': resolve(__dirname, 'resources/js'),
      '@css': resolve(__dirname, 'resources/scss'),
      '@images': resolve(__dirname, 'resources/images'),
      '@fonts': resolve(__dirname, 'resources/fonts')
    }
  },

  // Optimize dependencies
  optimizeDeps: {
    include: [
      'jquery',
      'moment',
      'moment-timezone',
      'tom-select',
      'toastr',
      'clipboard',
      'jsencrypt',
      'zxcvbn',
      'spark-md5'
    ]
  }
});
