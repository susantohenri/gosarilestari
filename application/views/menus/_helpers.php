<?php defined('BASEPATH') or exit('No direct script access allowed');

function sidebar_link($controller, $url, $icon, $label, $current)
{
    $active = $current['controller'] === $controller;
    $classes = $active
        ? 'flex items-center gap-3 px-3 py-2.5 bg-brand-50 text-brand-700 rounded-lg font-medium'
        : 'flex items-center gap-3 px-3 py-2.5 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-lg transition-colors';
    echo '<a href="' . site_url($url) . '" class="' . $classes . '">';
    echo '<i class="fa-solid fa-' . $icon . ' w-5 text-center"></i> ' . htmlspecialchars($label);
    echo '</a>';
}

function sidebar_section($title)
{
    echo '<div class="px-4 mb-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">' . htmlspecialchars($title) . '</div>';
    echo '<nav class="space-y-1 px-3 mb-4">';
}

function sidebar_section_end()
{
    echo '</nav>';
}
