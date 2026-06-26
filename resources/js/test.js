/**
 * Test script for Vite integration
 * This ensures vendor libraries are loaded before testing
 */

// Import vendor to ensure it loads first
import './vendor.js';

// Now test that libraries are available
const results = {
    jQuery: typeof $ !== 'undefined' && typeof jQuery !== 'undefined',
    moment: typeof moment !== 'undefined',
    toastr: typeof toastr !== 'undefined',
    Clipboard: typeof Clipboard !== 'undefined',
    JSEncrypt: typeof JSEncrypt !== 'undefined',
    zxcvbn: typeof zxcvbn !== 'undefined',
    SparkMD5: typeof SparkMD5 !== 'undefined'
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
console.log('jQuery version:', $?.fn?.jquery);
console.log('Moment version:', moment?.version);
