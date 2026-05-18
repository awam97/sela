<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class PwaFilter implements FilterInterface
{
    /**
     * No action required before request.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        //
    }

    /**
     * Automatically injects PWA links, meta tags, and registration script into HTML response.
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Only run on standard GET requests
        if ($request->getMethod() !== 'get') {
            return;
        }

        // Avoid running in CLI
        if (is_cli()) {
            return;
        }

        // Only inject into HTML pages
        $contentType = $response->getHeaderLine('Content-Type');
        if (strpos($contentType, 'text/html') === false) {
            return;
        }

        $body = $response->getBody();
        if (empty($body)) {
            return;
        }

        // 1. Prepare PWA Meta and Manifest tags for <head>
        $manifestUrl = base_url('app.webmanifest');
        $iconUrl = base_url('assets/images/icon-192.png');
        $themeColor = '#192A56'; // Premium deep navy primary color

        $pwaMeta = "\n    <!-- PWA Settings -->\n";
        $pwaMeta .= "    <meta name=\"theme-color\" content=\"{$themeColor}\">\n";
        $pwaMeta .= "    <link rel=\"manifest\" href=\"{$manifestUrl}\">\n";
        $pwaMeta .= "    <meta name=\"apple-mobile-web-app-capable\" content=\"yes\">\n";
        $pwaMeta .= "    <meta name=\"apple-mobile-web-app-status-bar-style\" content=\"black-translucent\">\n";
        $pwaMeta .= "    <meta name=\"apple-mobile-web-app-title\" content=\"Sela\">\n";
        $pwaMeta .= "    <link rel=\"apple-touch-icon\" href=\"{$iconUrl}\">\n";

        // 2. Prepare PWA Service Worker script for </body>
        $swUrl = base_url('sw.js');
        $pwaScript = "\n    <!-- PWA Service Worker Registration -->\n";
        $pwaScript .= "    <script>\n";
        $pwaScript .= "        if ('serviceWorker' in navigator) {\n";
        $pwaScript .= "            window.addEventListener('load', () => {\n";
        $pwaScript .= "                navigator.serviceWorker.register('{$swUrl}')\n";
        $pwaScript .= "                    .then(reg => console.log('Service Worker registered successfully. Scope: ', reg.scope))\n";
        $pwaScript .= "                    .catch(err => console.log('Service Worker registration failed: ', err));\n";
        $pwaScript .= "            });\n";
        $pwaScript .= "        }\n";
        $pwaScript .= "    </script>\n";

        // 3. Inject PWA assets safely
        if (stripos($body, '</head>') !== false) {
            $body = str_ireplace('</head>', $pwaMeta . '</head>', $body);
        }
        if (stripos($body, '</body>') !== false) {
            $body = str_ireplace('</body>', $pwaScript . '</body>', $body);
        }

        $response->setBody($body);
    }
}
