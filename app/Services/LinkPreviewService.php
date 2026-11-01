<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class LinkPreviewService
{
    public function getPreview(string $url): array
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; Keepsake/1.0)',
                ])
                ->get($url);

            if (!$response->successful()) {
                return $this->fallback($url);
            }

            $html = $response->body();

            return [
                'title' => $this->extractMeta($html, 'og:title')
                    ?? $this->extractTitle($html)
                    ?? parse_url($url, PHP_URL_HOST),
                'description' => $this->extractMeta($html, 'og:description')
                    ?? $this->extractMeta($html, 'description'),
                'image' => $this->extractMeta($html, 'og:image'),
                'favicon' => $this->extractFavicon($html, $url),
                'domain' => parse_url($url, PHP_URL_HOST),
                'site_name' => $this->extractMeta($html, 'og:site_name'),
            ];
        } catch (\Exception $e) {
            return $this->fallback($url);
        }
    }

    private function extractMeta(string $html, string $property): ?string
    {
        // Try og: property
        if (preg_match('/<meta[^>]*property=["\']' . preg_quote($property, '/') . '["\'][^>]*content=["\']([^"\']*)["\'][^>]*\/?>/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
        }

        // Try name attribute (for description, etc.)
        if (preg_match('/<meta[^>]*name=["\']' . preg_quote($property, '/') . '["\'][^>]*content=["\']([^"\']*)["\'][^>]*\/?>/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
        }

        // Try content-first order
        if (preg_match('/<meta[^>]*content=["\']([^"\']*)["\'][^>]*(?:property|name)=["\']' . preg_quote($property, '/') . '["\'][^>]*\/?>/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
        }

        return null;
    }

    private function extractTitle(string $html): ?string
    {
        if (preg_match('/<title[^>]*>([^<]+)<\/title>/i', $html, $matches)) {
            return html_entity_decode(trim($matches[1]), ENT_QUOTES, 'UTF-8');
        }
        return null;
    }

    private function extractFavicon(string $html, string $url): ?string
    {
        if (preg_match('/<link[^>]*rel=["\'](?:shortcut )?icon["\'][^>]*href=["\']([^"\']*)["\'][^>]*\/?>/i', $html, $matches)) {
            $favicon = $matches[1];
            if (str_starts_with($favicon, '//')) {
                return 'https:' . $favicon;
            }
            if (str_starts_with($favicon, '/')) {
                $parsed = parse_url($url);
                return ($parsed['scheme'] ?? 'https') . '://' . $parsed['host'] . $favicon;
            }
            if (str_starts_with($favicon, 'http')) {
                return $favicon;
            }
        }

        // Fallback to /favicon.ico
        $parsed = parse_url($url);
        return ($parsed['scheme'] ?? 'https') . '://' . ($parsed['host'] ?? '') . '/favicon.ico';
    }

    private function fallback(string $url): array
    {
        return [
            'title' => parse_url($url, PHP_URL_HOST) ?? $url,
            'description' => null,
            'image' => null,
            'favicon' => null,
            'domain' => parse_url($url, PHP_URL_HOST),
            'site_name' => null,
        ];
    }
}
