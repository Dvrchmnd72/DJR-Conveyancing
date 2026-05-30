<?php
if (!defined('ABSPATH')) exit;

// Register rewrite rule + query var
// Disable WordPress core sitemaps - we generate our own
add_filter('wp_sitemaps_enabled', '__return_false');
add_filter('aioseo_sitemap_output', '__return_false');
add_filter('aioseo_sitemap_disable', '__return_true');

// Prevent WordPress trailing-slash redirect on sitemap.xml
add_filter('redirect_canonical', 'osl_sitemap_no_redirect', 10, 2);
function osl_sitemap_no_redirect($redirect_url, $requested_url) {
    if (strpos($requested_url, 'sitemap.xml') !== false) return false;
    return $redirect_url;
}

add_action('init', 'osl_sitemap_register_rewrite');
function osl_sitemap_register_rewrite() {
    add_rewrite_rule('^sitemap\.xml$', 'index.php?osl_sitemap=1', 'top');
    add_filter('query_vars', function($vars) {
        $vars[] = 'osl_sitemap';
        return $vars;
    });
    add_action('template_redirect', 'osl_sitemap_template_redirect');
}

function osl_sitemap_template_redirect() {
    if (!get_query_var('osl_sitemap')) return;
    header('Content-Type: application/xml; charset=UTF-8');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('X-Accel-Expires: 0');

    // Build exclusion list - start with hardcoded IDs then add slug-based ones
    $exclude_ids = array(3341); // Conveyancing Gold Coast hub - listed explicitly in core pages
    $exclude_slugs = array('cancel', 'suburb-quote', 'gold-coast-quote', 'settlex-login', 'wills-quote');
    foreach ($exclude_slugs as $slug) {
        $p = get_page_by_path($slug);
        if ($p) $exclude_ids[] = $p->ID;
    }

    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    // Homepage
    echo osl_sitemap_url(home_url('/'), date('Y-m-d'), 'daily', '1.0');

    // Core pages - explicit list with priorities
    $core_pages = array(
        array('path' => 'services',                               'pri' => '0.9', 'freq' => 'weekly'),
        array('path' => 'about-us',                               'pri' => '0.9', 'freq' => 'monthly'),
        array('path' => 'contact-us',                             'pri' => '0.9', 'freq' => 'monthly'),
        array('path' => 'conveyancing',                           'pri' => '0.9', 'freq' => 'weekly'),
        array('path' => 'conveyancing/gold-coast',                'pri' => '0.9', 'freq' => 'weekly'),
        array('path' => 'conveyancing-quote',                     'pri' => '0.9', 'freq' => 'weekly'),
        array('path' => 'wills-and-estate-planning',              'pri' => '0.8', 'freq' => 'weekly'),
        array('path' => 'wills-and-estate-planning/gold-coast',   'pri' => '0.8', 'freq' => 'weekly'),
        array('path' => 'book',                                   'pri' => '0.8', 'freq' => 'monthly'),
        array('path' => 'compensation',                           'pri' => '0.7', 'freq' => 'monthly'),
        array('path' => 'binding-financial-agreements',           'pri' => '0.7', 'freq' => 'monthly'),
        array('path' => 'commercial-and-corporate-law',           'pri' => '0.7', 'freq' => 'monthly'),
        array('path' => 'contract-reviews',                       'pri' => '0.7', 'freq' => 'monthly'),
        array('path' => 'litigation',                             'pri' => '0.7', 'freq' => 'monthly'),
        array('path' => 'immigration',                            'pri' => '0.7', 'freq' => 'monthly'),
        array('path' => 'personal-injury-claims',                 'pri' => '0.7', 'freq' => 'monthly'),
        array('path' => 'medical-negligence',                     'pri' => '0.7', 'freq' => 'monthly'),
        array('path' => 'public-liability',                       'pri' => '0.7', 'freq' => 'monthly'),
        array('path' => 'product-liability',                      'pri' => '0.7', 'freq' => 'monthly'),
        array('path' => 'workers-compensation-claims',            'pri' => '0.7', 'freq' => 'monthly'),
        array('path' => 'superannuation-and-tpd-claims',          'pri' => '0.7', 'freq' => 'monthly'),
        array('path' => 'asbestos-and-dust-disease-compensation', 'pri' => '0.7', 'freq' => 'monthly'),
        array('path' => 'property-settlement-agent',              'pri' => '0.7', 'freq' => 'monthly'),
        array('path' => 'newsfacts',                              'pri' => '0.6', 'freq' => 'weekly'),
        array('path' => 'privacy-policy-2',                       'pri' => '0.3', 'freq' => 'yearly'),
        array('path' => 'terms-of-service',                       'pri' => '0.3', 'freq' => 'yearly'),
    );
    foreach ($core_pages as $cp) {
        $page    = get_page_by_path($cp['path']);
        $lastmod = $page ? get_the_modified_date('Y-m-d', $page->ID) : date('Y-m-d');
        echo osl_sitemap_url(home_url('/' . $cp['path'] . '/'), $lastmod, $cp['freq'], $cp['pri']);
    }

    // Regional hub pages - get IDs first so we can exclude from Gold Coast query
    $regional_hub_ids = array();
    $regional_hubs = get_posts(array(
        'post_type'      => 'page',
        'post_parent'    => 2394,
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'meta_key'       => '_wp_page_template',
        'meta_value'     => 'page-conveyancing-region.php',
        'orderby'        => 'post_name',
        'order'          => 'ASC',
    ));
    foreach ($regional_hubs as $hub) {
        $regional_hub_ids[] = $hub->ID;
        echo osl_sitemap_url(get_permalink($hub->ID), get_the_modified_date('Y-m-d', $hub->ID), 'weekly', '0.9');
    }

    // Gold Coast suburb pages - direct children of 2394, excluding hub pages + utility pages
    $gc_exclude = array_merge($exclude_ids, $regional_hub_ids);
    $suburb_pages = get_posts(array(
        'post_type'      => 'page',
        'post_parent'    => 2394,
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'post_name',
        'order'          => 'ASC',
        'exclude'        => $gc_exclude,
    ));
    foreach ($suburb_pages as $page) {
        echo osl_sitemap_url(get_permalink($page->ID), get_the_modified_date('Y-m-d', $page->ID), 'weekly', '0.8');
    }

    // Regional suburb pages - children of each regional hub
    if (!empty($regional_hub_ids)) {
        foreach ($regional_hub_ids as $hub_id) {
            $regional_suburbs = get_posts(array(
                'post_type'      => 'page',
                'post_parent'    => $hub_id,
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                'orderby'        => 'post_name',
                'order'          => 'ASC',
            ));
            foreach ($regional_suburbs as $page) {
                echo osl_sitemap_url(get_permalink($page->ID), get_the_modified_date('Y-m-d', $page->ID), 'weekly', '0.8');
            }
        }
    }

    // Blog posts
    $posts = get_posts(array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'modified',
        'order'          => 'DESC',
    ));
    foreach ($posts as $post) {
        echo osl_sitemap_url(get_permalink($post->ID), get_the_modified_date('Y-m-d', $post->ID), 'monthly', '0.6');
    }

    echo '</urlset>';
    exit;
}

function osl_sitemap_url($loc, $lastmod, $changefreq, $priority) {
    return "  <url>\n" .
           "    <loc>" . esc_url($loc) . "</loc>\n" .
           "    <lastmod>" . esc_html($lastmod) . "</lastmod>\n" .
           "    <changefreq>" . esc_html($changefreq) . "</changefreq>\n" .
           "    <priority>" . esc_html($priority) . "</priority>\n" .
           "  </url>\n";
}

// Keep conveyancing-specific sitemap for backwards compat
add_action('init', 'osl_cq_register_sitemap');
function osl_cq_register_sitemap() {
    add_rewrite_rule('conveyancing-sitemap\.xml$', 'index.php?osl_cq_sitemap=1', 'top');
    add_filter('query_vars', function($vars) {
        $vars[] = 'osl_cq_sitemap';
        return $vars;
    });
    add_action('template_redirect', 'osl_cq_render_suburb_sitemap');
}
function osl_cq_render_suburb_sitemap() {
    if (!get_query_var('osl_cq_sitemap')) return;
    header('Content-Type: application/xml; charset=UTF-8');
    $pages = get_posts(array(
        'post_type'      => 'page',
        'post_parent'    => 2394,
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'post_name',
        'order'          => 'ASC',
    ));
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    echo osl_sitemap_url(get_permalink(2394), get_the_modified_date('Y-m-d', 2394), 'weekly', '0.9');
    foreach ($pages as $page) {
        echo osl_sitemap_url(get_permalink($page->ID), get_the_modified_date('Y-m-d', $page->ID), 'weekly', '0.8');
    }
    echo '</urlset>';
    exit;
}

// robots.txt
add_filter('robots_txt', 'osl_cq_robots_txt', 10, 2);
function osl_cq_robots_txt($output, $public) {
    $output .= "\nSitemap: " . home_url('/sitemap.xml') . "\n";
    return $output;
}
