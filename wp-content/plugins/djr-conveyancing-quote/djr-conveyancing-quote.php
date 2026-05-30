<?php
/**
 * Plugin Name: DJR Conveyancing Quote
 * Description: Conveyancing quote calculator with central pricing management for DJR Conveyancing
 * Version: 1.0.0-djr1
 * Author: DJR Conveyancing
 */

if (!defined('ABSPATH')) exit;

define('OSL_CQ_VERSION', '1.2.3-djr-style3');
define('OSL_CQ_PATH', plugin_dir_path(__FILE__));
define('OSL_CQ_URL', plugin_dir_url(__FILE__));

// Include files
require_once OSL_CQ_PATH . 'includes/admin-pricing.php';
require_once OSL_CQ_PATH . 'includes/quote-engine.php';
require_once OSL_CQ_PATH . 'includes/ajax-handlers.php';
require_once OSL_CQ_PATH . 'includes/suburbs.php';
require_once OSL_CQ_PATH . 'includes/sitemap.php';

// Enqueue front-end assets
add_action('wp_enqueue_scripts', function() {
    if (is_page() || has_shortcode(get_post()->post_content ?? '', 'osl_quote_form')) {
        wp_enqueue_style('osl-cq-style', OSL_CQ_URL . 'assets/quote-form.css', array(), OSL_CQ_VERSION);
        wp_enqueue_script('osl-cq-script', OSL_CQ_URL . 'assets/quote-form.js', array('jquery'), OSL_CQ_VERSION, true);
        wp_localize_script('osl-cq-script', 'OslCQ', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('osl_cq_nonce'),
        ));
    }
});

// Register shortcode
add_shortcode('osl_quote_form', 'osl_cq_render_form');

// Activation/load: ensure default state/council pricing exists and migrate legacy options.
register_activation_hook(__FILE__, 'osl_cq_activate');
function osl_cq_activate() {
    osl_cq_migrate_pricing_options();
    if (!get_option('osl_cq_councils')) {
        update_option('osl_cq_councils', osl_cq_get_default_councils());
    } else {
        osl_cq_migrate_councils_option();
    }
}
add_action('plugins_loaded', 'osl_cq_migrate_pricing_options');
add_action('plugins_loaded', 'osl_cq_migrate_councils_option');

function osl_cq_get_default_pricing() {
    return array(
        'purchasing' => array(
            'house' => array(
                'professional_fee' => 1210,
                'title_search' => 43.47,
                'registered_plan' => 45.78,
                'land_tax' => 41.90,
                'identity_check' => 19.90,
            ),
            'unit_townhouse_duplex' => array(
                'professional_fee' => 1210,
                'title_search' => 43.47,
                'registered_plan' => 45.78,
                'land_tax' => 41.90,
                'identity_check' => 19.90,
            ),
            'land' => array(
                'professional_fee' => 1210,
                'title_search' => 43.47,
                'registered_plan' => 45.78,
                'land_tax' => 41.90,
                'identity_check' => 19.90,
            ),
        ),
        'selling' => array(
            'house' => array(
                'professional_fee' => 1089,
                'title_search' => 43.47,
                'identity_check' => 19.90,
            ),
            'unit_townhouse_duplex' => array(
                'professional_fee' => 1089,
                'title_search' => 43.47,
                'identity_check' => 19.90,
            ),
            'land' => array(
                'professional_fee' => 1089,
                'title_search' => 43.47,
                'identity_check' => 19.90,
            ),
        ),
    );
}

function osl_cq_get_default_council_state() {
    return 'QLD';
}

function osl_cq_get_transaction_types() {
    return array(
        'purchase' => 'Purchasing a Property',
        'sale' => 'Selling a Property',
    );
}

function osl_cq_normalize_transaction_type($type) {
    if ($type === 'purchasing') {
        return 'purchase';
    }
    if ($type === 'selling') {
        return 'sale';
    }
    return $type;
}

function osl_cq_legacy_transaction_type($type) {
    if ($type === 'purchase') {
        return 'purchasing';
    }
    if ($type === 'sale') {
        return 'selling';
    }
    return $type;
}

function osl_cq_normalize_property_pricing($fields) {
    $normalized = array(
        'professional_fee' => isset($fields['professional_fee']) ? floatval($fields['professional_fee']) : 0,
        'disbursements' => array(),
    );

    foreach ((array) $fields as $key => $value) {
        if (in_array($key, array('professional_fee', 'professional_fee_discount', 'discount_amount', 'disbursements'), true)) {
            continue;
        }
        $normalized['disbursements'][$key] = floatval($value);
    }

    if (isset($fields['disbursements']) && is_array($fields['disbursements'])) {
        foreach ($fields['disbursements'] as $key => $value) {
            $normalized['disbursements'][$key] = floatval($value);
        }
    }

    return $normalized;
}

function osl_cq_normalize_council_property_pricing($fields) {
    $normalized = array(
        'disbursements' => array(),
    );

    foreach (array('rates_search', 'water_meter_reading') as $field_key) {
        if (isset($fields['disbursements']) && is_array($fields['disbursements']) && array_key_exists($field_key, $fields['disbursements'])) {
            $normalized['disbursements'][$field_key] = floatval($fields['disbursements'][$field_key]);
        } elseif (array_key_exists($field_key, (array) $fields)) {
            $normalized['disbursements'][$field_key] = floatval($fields[$field_key]);
        }
    }

    return $normalized;
}

function osl_cq_flatten_property_pricing($fields) {
    $flat = array(
        'professional_fee' => $fields['professional_fee'] ?? 0,
    );

    if (!empty($fields['disbursements']) && is_array($fields['disbursements'])) {
        foreach ($fields['disbursements'] as $key => $value) {
            $flat[$key] = $value;
        }
    }

    return $flat;
}

function osl_cq_convert_flat_pricing_to_nested($flat_pricing) {
    $nested = array();
    foreach (array('purchasing' => 'purchase', 'selling' => 'sale', 'purchase' => 'purchase', 'sale' => 'sale') as $source_type => $target_type) {
        if (empty($flat_pricing[$source_type]) || !is_array($flat_pricing[$source_type])) {
            continue;
        }
        foreach ($flat_pricing[$source_type] as $property_type => $fields) {
            $nested[$target_type][$property_type] = array(
                'professional_fee' => 0,
                'disbursements' => array(),
            );
            foreach (osl_cq_get_default_fee_fields($target_type, $property_type) as $field_key => $field_label) {
                if ($field_key === 'professional_fee') {
                    $nested[$target_type][$property_type]['professional_fee'] = isset($fields['professional_fee']) ? floatval($fields['professional_fee']) : 0;
                    continue;
                }
                if (isset($fields['disbursements']) && is_array($fields['disbursements']) && array_key_exists($field_key, $fields['disbursements'])) {
                    $nested[$target_type][$property_type]['disbursements'][$field_key] = floatval($fields['disbursements'][$field_key]);
                } elseif (array_key_exists($field_key, (array) $fields)) {
                    $nested[$target_type][$property_type]['disbursements'][$field_key] = floatval($fields[$field_key]);
                } else {
                    $nested[$target_type][$property_type]['disbursements'][$field_key] = 0;
                }
            }
        }
    }
    return $nested;
}

function osl_cq_convert_flat_council_pricing_to_nested($flat_pricing) {
    $nested = array();
    foreach (array('purchasing' => 'purchase', 'purchase' => 'purchase') as $source_type => $target_type) {
        if (empty($flat_pricing[$source_type]) || !is_array($flat_pricing[$source_type])) {
            continue;
        }
        foreach ($flat_pricing[$source_type] as $property_type => $fields) {
            $nested[$target_type][$property_type] = osl_cq_normalize_council_property_pricing($fields);
        }
    }
    return $nested;
}

function osl_cq_merge_legacy_override_into_default($default_pricing, $override_pricing) {
    return osl_cq_convert_flat_council_pricing_to_nested($override_pricing);
}

function osl_cq_get_default_pricing_structure() {
    return array(
        'states' => array(
            'QLD' => array(
                'default' => osl_cq_convert_flat_pricing_to_nested(osl_cq_get_default_pricing()),
                'councils' => array(),
            ),
        ),
    );
}

function osl_cq_migrate_pricing_options() {
    $pricing = get_option('osl_cq_pricing', null);
    $legacy_defaults = get_option('osl_cq_default_pricing', null);
    $legacy_overrides = get_option('osl_cq_council_overrides', null);

    if ($pricing && $legacy_defaults === null && $legacy_overrides === null) {
        return;
    }

    if (!$pricing) {
        $pricing = osl_cq_get_default_pricing_structure();
    }

    if ($legacy_defaults !== null && is_array($legacy_defaults)) {
        $pricing['states']['QLD']['default'] = osl_cq_convert_flat_pricing_to_nested($legacy_defaults);
    } elseif (empty($pricing['states']['QLD']['default'])) {
        $pricing['states']['QLD']['default'] = osl_cq_convert_flat_pricing_to_nested(osl_cq_get_default_pricing());
    }

    if (!isset($pricing['states']['QLD']['councils']) || !is_array($pricing['states']['QLD']['councils'])) {
        $pricing['states']['QLD']['councils'] = array();
    }

    if ($legacy_overrides !== null && is_array($legacy_overrides)) {
        foreach ($legacy_overrides as $council_key => $override) {
            $pricing['states']['QLD']['councils'][$council_key] = osl_cq_merge_legacy_override_into_default(
                $pricing['states']['QLD']['default'],
                $override
            );
        }
    }

    update_option('osl_cq_pricing', $pricing);
    delete_option('osl_cq_default_pricing');
    delete_option('osl_cq_council_overrides');
}

function osl_cq_migrate_councils_option() {
    $councils = get_option('osl_cq_councils', null);
    if ($councils === null) {
        return;
    }

    $normalized = osl_cq_normalize_councils($councils);
    if ($normalized !== $councils) {
        update_option('osl_cq_councils', $normalized);
    }
}

function osl_cq_normalize_councils($councils) {
    $normalized = array();
    foreach ((array) $councils as $key => $council) {
        if (is_array($council)) {
            $name = sanitize_text_field($council['name'] ?? $key);
            $state = sanitize_text_field($council['state'] ?? osl_cq_get_default_council_state());
            $council_key = sanitize_title($council['key'] ?? $key);
        } else {
            $name = sanitize_text_field($council);
            $state = osl_cq_get_default_council_state();
            $council_key = sanitize_title($key);
        }
        if ($name === '') {
            continue;
        }
        if ($council_key === '' || is_numeric($council_key)) {
            $council_key = sanitize_title($name);
        }
        $normalized[] = array(
            'key' => $council_key,
            'name' => $name,
            'state' => $state ?: osl_cq_get_default_council_state(),
        );
    }

    usort($normalized, function($a, $b) {
        return strcasecmp($a['name'], $b['name']);
    });

    return $normalized;
}

function osl_cq_get_council_key($council) {
    if (is_array($council)) {
        $key = sanitize_title($council['key'] ?? '');
        return $key !== '' ? $key : sanitize_title($council['name'] ?? '');
    }
    return sanitize_title($council);
}

function osl_cq_get_council_choices() {
    $choices = array();
    foreach (osl_cq_normalize_councils(get_option('osl_cq_councils', array())) as $council) {
        $choices[osl_cq_get_council_key($council)] = $council;
    }
    return $choices;
}

function osl_cq_get_council_name($council_key) {
    $choices = osl_cq_get_council_choices();
    return $choices[$council_key]['name'] ?? $council_key;
}

function osl_cq_get_pricing() {
    $pricing = get_option('osl_cq_pricing', array());
    if (empty($pricing['states'])) {
        $pricing = osl_cq_get_default_pricing_structure();
        update_option('osl_cq_pricing', $pricing);
    }
    return $pricing;
}

function osl_cq_update_pricing($pricing) {
    update_option('osl_cq_pricing', $pricing);
}

function osl_cq_get_pricing_data($council_key, $state = 'QLD') {
    $pricing = osl_cq_get_pricing();
    if (empty($pricing['states'][$state])) {
        return false;
    }

    if (isset($pricing['states'][$state]['councils'][$council_key])) {
        return $pricing['states'][$state]['councils'][$council_key];
    }

    return $pricing['states'][$state]['default'] ?? false;
}

function osl_cq_get_price($council_key, $type, $property_type, $fee_key) {
    $pricing = osl_cq_get_pricing();
    $state = osl_cq_get_default_council_state();
    if (empty($pricing['states'][$state])) {
        return 0;
    }

    $type = osl_cq_normalize_transaction_type($type);
    $is_council_field = ($type === 'purchase' && in_array($fee_key, array('rates_search', 'water_meter_reading'), true));
    $source = $is_council_field
        ? ($pricing['states'][$state]['councils'][$council_key] ?? array())
        : ($pricing['states'][$state]['default'] ?? array());

    $property_pricing = $source[$type][$property_type] ?? array();

    if ($fee_key === 'professional_fee') {
        return $property_pricing['professional_fee'] ?? 0;
    }

    return $property_pricing['disbursements'][$fee_key] ?? 0;
}

function osl_cq_get_default_councils() {
    return osl_cq_normalize_councils(array(
        'balonne-shire' => 'Balonne Shire Council',
        'banana-shire' => 'Banana Shire Council',
        'barcaldine-regional' => 'Barcaldine Regional',
        'barcoo-shire' => 'Barcoo Shire',
        'blackhall-tambo-regional' => 'Blackhall-Tambo Regional',
        'boulia-shire' => 'Boulia Shire',
        'brisbane-city' => 'Brisbane City',
        'bulloo-shire' => 'Bulloo Shire Council',
        'bundaberg' => 'Bundaberg',
        'burdekin' => 'Burdekin',
        'burke-shire' => 'Burke Shire',
        'cairns' => 'Cairns',
        'carpentaria' => 'Carpentaria',
        'cassowary-coast-regional' => 'Cassowary Coast Regional',
        'central-highlands-regional' => 'Central Highlands Regional',
        'charters-towers-regional' => 'Charters Towers Regional',
        'cloncurry-shire' => 'Cloncurry Shire',
        'cook-shire' => 'Cook Shire',
        'croydon-shire' => 'Croydon Shire',
        'diamantina-shire' => 'Diamantina Shire',
        'douglas-shire' => 'Douglas Shire',
        'etheridge-shire' => 'Etheridge Shire',
        'flinders-shire' => 'Flinders Shire',
        'fraser-coast-regional' => 'Fraser Coast Regional',
        'gladstone-regional' => 'Gladstone Regional',
        'gold-coast' => 'Gold Coast',
        'goondiwindi-regional' => 'Goondiwindi Regional',
        'gympie-regional' => 'Gympie Regional',
        'hinchinbrook-shire' => 'Hinchinbrook Shire',
        'ipswich-city' => 'Ipswich City',
        'isaac-regional' => 'Isaac Regional',
        'livingstone-shire' => 'Livingstone Shire',
        'lockyer-valley' => 'Lockyer Valley',
        'logan-city' => 'Logan City',
        'longreach-regional' => 'Longreach Regional',
        'mackay-regional' => 'Mackay Regional',
        'maranoa-regional' => 'Maranoa Regional',
        'mareeba-shire' => 'Mareeba Shire',
        'mckinlay-shire' => 'McKinlay Shire',
        'moreton-bay-regional' => 'Moreton Bay Regional',
        'mount-isa-city' => 'Mount Isa City',
        'murweh-shire' => 'Murweh Shire',
        'noosa-shire' => 'Noosa Shire',
        'north-burnett-regional' => 'North Burnett Regional',
        'paroo-shire' => 'Paroo Shire',
        'quilpie-shire' => 'Quilpie Shire',
        'redland-city' => 'Redland City Council',
        'richmond-shire' => 'Richmond Shire',
        'rockhampton-regional' => 'Rockhampton Regional',
        'scenic-rim-regional' => 'Scenic Rim Regional',
        'somerset-regional' => 'Somerset Regional',
        'south-burnett-regional' => 'South Burnett Regional',
        'southern-downs-regional' => 'Southern Downs Regional',
        'sunshine-coast-regional' => 'Sunshine Coast Regional',
        'tablelands-regional' => 'Tablelands Regional',
        'toowoomba-regional' => 'Toowoomba Regional',
        'torres-shire' => 'Torres Shire',
        'townsville-city' => 'Townsville City',
        'weipa-town' => 'Weipa Town',
        'western-downs-regional' => 'Western Downs Regional',
        'whitsunday-regional' => 'Whitsunday Regional Council',
        'winton-shire' => 'Winton Shire Council',
    ));
}
