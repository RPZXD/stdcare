<?php
/**
 * Theme Helpers
 * Small utilities to generate Tailwind class fragments based on $themeColor
 */

if (session_status() == PHP_SESSION_NONE) {
    // no session start here; layouts handle sessions
}

/**
 * Return a Tailwind class like "bg-rose-500" for given prefix and shade
 * @param string $prefix e.g. 'bg', 'text', 'ring', 'border', 'from', 'to', 'shadow'
 * @param string|int $shade e.g. '50', '100', '500'
 * @return string
 */
function themeClass($prefix, $shade = '500') {
    global $themeColor;
    $prefix = trim($prefix, "- ");
    $shade = (string)$shade;
    if (empty($themeColor)) $themeColor = 'indigo';
    return $prefix . '-' . $themeColor . '-' . $shade;
}

/**
 * Return gradient fragment like "from-rose-500 to-rose-600"
 * @param string $fromShade
 * @param string $toShade
 * @return string
 */
function themeGradient($fromShade = '500', $toShade = '600') {
    return 'from-' . $GLOBALS['themeColor'] . '-' . $fromShade . ' to-' . $GLOBALS['themeColor'] . '-' . $toShade;
}

?>
