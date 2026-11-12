<?php
declare(strict_types=1);

/**
 * Averroes International School CMS
 * Formatting & Media Parsing Helpers
 */

if (!function_exists('parse_video_embed_url')) {
    /**
     * Convert standard YouTube or Vimeo URL to responsive embed format
     */
    function parse_video_embed_url(string $url): string {
        $url = trim($url);
        if ($url === '') {
            return 'https://www.youtube.com/embed/lWlHTIiGMvk?autoplay=1&mute=1&loop=1&playlist=lWlHTIiGMvk&playsinline=1&controls=0&modestbranding=1&rel=0&iv_load_policy=3&disablekb=1&fs=0&cc_load_policy=0&cc_lang_pref=off&hl=en&enablejsapi=1';
        }

        // Check YouTube watch or short URLs
        if (preg_match('#(?:youtube\.com\/(?:watch\?.*v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})#i', $url, $m)) {
            $videoId = $m[1];
            return "https://www.youtube.com/embed/{$videoId}?autoplay=1&mute=1&loop=1&playlist={$videoId}&playsinline=1&controls=0&modestbranding=1&rel=0&iv_load_policy=3&disablekb=1&fs=0&cc_load_policy=0&cc_lang_pref=off&hl=en&enablejsapi=1";
        }

        // Check Vimeo URLs
        if (preg_match('#vimeo\.com\/(?:video\/)?([0-9]+)#i', $url, $m)) {
            $vimeoId = $m[1];
            return "https://player.vimeo.com/video/{$vimeoId}?autoplay=1&loop=1&background=1&autopause=0&muted=1";
        }

        return $url;
    }
}

if (!function_exists('render_hero_video')) {
    /**
     * Renders background video engine matching aisl.edu.bd 1:1 Elementor implementation
     */
    function render_hero_video(string $url): string {
        $url = trim($url);
        if ($url === '') {
            $url = 'https://youtu.be/lWlHTIiGMvk';
        }

        // Check if an explicit MP4 path is provided or local video exists
        if (str_ends_with(strtolower($url), '.mp4')) {
            $src = str_starts_with($url, 'http') ? $url : asset_url(ltrim($url, '/'));
            return '<div class="elementor-background-video-container"><video class="elementor-background-video-hosted" autoplay muted loop playsinline preload="auto"><source src="' . e($src) . '" type="video/mp4"></video></div>';
        }

        $localMp4 = defined('APP_ROOT') ? (APP_ROOT . '/assets/videos/hero.mp4') : (dirname(__DIR__, 2) . '/assets/videos/hero.mp4');
        if (file_exists($localMp4)) {
            return '<div class="elementor-background-video-container"><video class="elementor-background-video-hosted" autoplay muted loop playsinline preload="auto"><source src="' . asset_url('videos/hero.mp4') . '" type="video/mp4"></video></div>';
        }

        $videoId = 'lWlHTIiGMvk';
        if (preg_match('#(?:youtube\.com\/(?:watch\?.*v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})#i', $url, $m)) {
            $videoId = $m[1];
        }

        return '<div class="elementor-background-video-container loading" data-video-id="' . e($videoId) . '">' .
               '<div id="heroVideoEmbed" class="elementor-background-video-embed" role="presentation"></div>' .
               '</div>';
    }
}
