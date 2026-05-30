<?php
if (!defined('ABSPATH')) exit;

function osl_cq_get_gold_coast_suburbs() {
    return array(
        'southport'          => array('name' => 'Southport',          'postcode' => '4215', 'desc' => 'the heart of the Gold Coast CBD'),
        'robina'             => array('name' => 'Robina',             'postcode' => '4226', 'desc' => 'one of the Gold Coast\'s premier suburbs'),
        'surfers-paradise'   => array('name' => 'Surfers Paradise',   'postcode' => '4217', 'desc' => 'the iconic beachside hub of the Gold Coast'),
        'broadbeach'         => array('name' => 'Broadbeach',         'postcode' => '4218', 'desc' => 'one of the Gold Coast\'s most popular coastal suburbs'),
        'burleigh-heads'     => array('name' => 'Burleigh Heads',     'postcode' => '4220', 'desc' => 'a highly sought-after beachside suburb'),
        'palm-beach'         => array('name' => 'Palm Beach',         'postcode' => '4221', 'desc' => 'a vibrant southern Gold Coast suburb'),
        'currumbin'          => array('name' => 'Currumbin',          'postcode' => '4223', 'desc' => 'a beautiful coastal suburb near the NSW border'),
        'coolangatta'        => array('name' => 'Coolangatta',        'postcode' => '4225', 'desc' => 'the southernmost suburb on the Gold Coast'),
        'nerang'             => array('name' => 'Nerang',             'postcode' => '4211', 'desc' => 'a well-established suburb in the Gold Coast hinterland'),
        'mudgeeraba'         => array('name' => 'Mudgeeraba',         'postcode' => '4213', 'desc' => 'a family-friendly hinterland suburb'),
        'upper-coomera'      => array('name' => 'Upper Coomera',      'postcode' => '4209', 'desc' => 'one of the fastest-growing suburbs on the northern Gold Coast'),
        'coomera'            => array('name' => 'Coomera',            'postcode' => '4209', 'desc' => 'a thriving suburb in the northern Gold Coast corridor'),
        'ormeau'             => array('name' => 'Ormeau',             'postcode' => '4208', 'desc' => 'a growing residential suburb between Brisbane and the Gold Coast'),
        'pimpama'            => array('name' => 'Pimpama',            'postcode' => '4209', 'desc' => 'one of Australia\'s fastest-growing suburbs'),
        'helensvale'         => array('name' => 'Helensvale',         'postcode' => '4212', 'desc' => 'a major transport hub on the northern Gold Coast'),
        'oxenford'           => array('name' => 'Oxenford',           'postcode' => '4210', 'desc' => 'home to the Gold Coast\'s famous theme parks'),
        'mermaid-beach'      => array('name' => 'Mermaid Beach',      'postcode' => '4218', 'desc' => 'one of the Gold Coast\'s most prestigious beachside suburbs'),
        'miami'              => array('name' => 'Miami',              'postcode' => '4220', 'desc' => 'a popular beachside suburb between Burleigh and Broadbeach'),
        'varsity-lakes'      => array('name' => 'Varsity Lakes',      'postcode' => '4227', 'desc' => 'a modern master-planned community'),
        'bundall'            => array('name' => 'Bundall',            'postcode' => '4217', 'desc' => 'a central business and residential suburb'),
        'ashmore'            => array('name' => 'Ashmore',            'postcode' => '4214', 'desc' => 'a centrally located residential suburb'),
        'molendinar'         => array('name' => 'Molendinar',         'postcode' => '4214', 'desc' => 'a mixed residential and commercial suburb'),
        'parkwood'           => array('name' => 'Parkwood',           'postcode' => '4214', 'desc' => 'home to Griffith University and Gold Coast University Hospital'),
        'labrador'           => array('name' => 'Labrador',           'postcode' => '4215', 'desc' => 'a waterfront suburb on the Gold Coast Broadwater'),
        'biggera-waters'     => array('name' => 'Biggera Waters',     'postcode' => '4216', 'desc' => 'a Broadwater suburb with stunning water views'),
        'runaway-bay'        => array('name' => 'Runaway Bay',        'postcode' => '4216', 'desc' => 'a sought-after waterfront suburb'),
        'hope-island'        => array('name' => 'Hope Island',        'postcode' => '4212', 'desc' => 'an exclusive resort-style community'),
        'sanctuary-cove'     => array('name' => 'Sanctuary Cove',     'postcode' => '4212', 'desc' => 'a prestigious gated waterfront community'),
        'tamborine-mountain' => array('name' => 'Tamborine Mountain', 'postcode' => '4272', 'desc' => 'a scenic hinterland retreat above the Gold Coast'),
        'elanora'            => array('name' => 'Elanora',            'postcode' => '4221', 'desc' => 'a family-friendly suburb near Palm Beach'),
        'tugun'              => array('name' => 'Tugun',              'postcode' => '4224', 'desc' => 'a relaxed beachside suburb near the airport'),
        'merrimac'           => array('name' => 'Merrimac',           'postcode' => '4226', 'desc' => 'a centrally located and affordable suburb'),
        'carrara'            => array('name' => 'Carrara',            'postcode' => '4211', 'desc' => 'home to Metricon Stadium and central Gold Coast living'),
        'benowa'             => array('name' => 'Benowa',             'postcode' => '4217', 'desc' => 'a prestigious suburb known for its golf courses'),
        'pacific-pines'      => array('name' => 'Pacific Pines',      'postcode' => '4211', 'desc' => 'a modern family suburb in the Gold Coast hinterland'),
        'worongary'          => array('name' => 'Worongary',          'postcode' => '4213', 'desc' => 'a peaceful semi-rural suburb with acreage properties'),
    );
}

add_action('wp_head', 'osl_cq_suburb_seo_meta', 1);

function osl_cq_extract_faq_answers($post_content, $name) {
    $text   = preg_replace('/<!--.*?-->/s', '', $post_content);
    // Split on closing p, h2, h3, h4 tags so question headings and answer paragraphs
    // are treated as separate segments — prevents answer bleed-through
    $parts  = preg_split('/<\/(p|h2|h3|h4)>/i', $text);
    $answers = array(
        'cost'      => "Conveyancing fees in {$name} vary depending on whether you are buying or selling. Use our free online quote calculator above for an instant fixed-fee quote with no hidden costs.",
        'solicitor' => "Yes, in Queensland you are legally required to have a solicitor or licensed conveyancer handle the transfer of property. DJR Conveyancing provides professional conveyancing services for {$name} and all Gold Coast suburbs.",
        'timeline'  => "A standard property settlement in {$name} typically takes 30 to 90 days from contract signing, depending on your contract terms, finance approval, and any conditions that need to be satisfied.",
        'remote'    => "DJR Conveyancing handles all conveyancing electronically. Whether you are in {$name} or interstate, we manage your entire property transaction remotely with no need to visit our office.",
    );
    $found = array();
    foreach ($parts as $i => $part) {
        $clean = trim(strip_tags($part));
        if (empty($clean)) continue;
        if (!isset($found['cost']) && stripos($clean, 'how much') !== false && stripos($clean, 'cost') !== false) {
            for ($j = $i + 1; $j < count($parts); $j++) {
                $a = trim(strip_tags($parts[$j]));
                if (!empty($a)) { $answers['cost'] = $a; $found['cost'] = true; break; }
            }
        }
        if (!isset($found['solicitor']) && stripos($clean, 'need a conveyancer') !== false) {
            for ($j = $i + 1; $j < count($parts); $j++) {
                $a = trim(strip_tags($parts[$j]));
                if (!empty($a)) { $answers['solicitor'] = $a; $found['solicitor'] = true; break; }
            }
        }
        if (!isset($found['timeline']) && stripos($clean, 'how long') !== false) {
            for ($j = $i + 1; $j < count($parts); $j++) {
                $a = trim(strip_tags($parts[$j]));
                if (!empty($a)) { $answers['timeline'] = $a; $found['timeline'] = true; break; }
            }
        }
        if (!isset($found['remote']) && stripos($clean, 'remotely') !== false) {
            for ($j = $i + 1; $j < count($parts); $j++) {
                $a = trim(strip_tags($parts[$j]));
                if (!empty($a)) { $answers['remote'] = $a; $found['remote'] = true; break; }
            }
        }
    }
    return $answers;
}

function osl_cq_suburb_seo_meta() {
    if (!is_singular('page')) return;

    global $post;
    $suburbs = osl_cq_get_gold_coast_suburbs();

    if (!preg_match('/\[osl_quote_form suburb="([^"]+)"/', $post->post_content, $m)) return;
    $slug = $m[1];
    if (!isset($suburbs[$slug])) return;

    $name     = $suburbs[$slug]['name'];
    $postcode = $suburbs[$slug]['postcode'];
    $desc     = $suburbs[$slug]['desc'];
    $url      = get_permalink();
    $title    = "Conveyancer {$name} {$postcode} | Fixed-Fee Gold Coast Conveyancing | DJR Conveyancing";
    $metadesc = "Need a conveyancer in {$name} {$postcode}? DJR Conveyancing offers fixed-fee conveyancing on the Gold Coast — buying or selling. Instant online quote, no hidden costs, electronic settlement via PEXA.";
    $sitename = "DJR Conveyancing";
    $logo     = "https://djrconveyancing.com.au/wp-content/uploads/2024/03/cropped-onestop-legal-512-x-512-270x270.png";

    // ── Title & Core Meta ──────────────────────────────────────────────────
    echo '<meta name="description" content="' . esc_attr($metadesc) . '" />' . "\n";
    echo '<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />' . "\n";
    echo '<link rel="canonical" href="' . esc_url($url) . '" />' . "\n";

    // ── Open Graph ─────────────────────────────────────────────────────────
    echo '<meta property="og:type" content="website" />' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '" />' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($metadesc) . '" />' . "\n";
    echo '<meta property="og:url" content="' . esc_url($url) . '" />' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr($sitename) . '" />' . "\n";
    echo '<meta property="og:image" content="' . esc_url($logo) . '" />' . "\n";
    echo '<meta property="og:locale" content="en_AU" />' . "\n";

    // ── Twitter Card ───────────────────────────────────────────────────────
    echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '" />' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($metadesc) . '" />' . "\n";
    echo '<meta name="twitter:image" content="' . esc_url($logo) . '" />' . "\n";

    // ── LocalBusiness + LegalService Schema ───────────────────────────────
    $schema = array(
        '@context' => 'https://schema.org',
        '@type'    => array('LegalService', 'LocalBusiness'),
        'name'     => "DJR Conveyancing - Conveyancer {$name}",
        'description' => $metadesc,
        'url'      => $url,
        'telephone' => '+61755129827',
        'email'    => 'info@djrconveyancing.com.au',
        'logo'     => $logo,
        'image'    => $logo,
        'address'  => array(
            '@type'           => 'PostalAddress',
            'streetAddress'   => 'Level 4, 91 Scarborough Street',
            'addressLocality' => 'Southport',
            'addressRegion'   => 'QLD',
            'postalCode'      => '4215',
            'addressCountry'  => 'AU',
        ),
        'geo' => array(
            '@type'     => 'GeoCoordinates',
            'latitude'  => '-27.9957',
            'longitude' => '153.4073',
        ),
        'areaServed' => array(
            '@type'          => 'Place',
            'name'           => $name,
            'additionalType' => 'http://schema.org/Neighborhood',
            'postalCode'     => $postcode,
            'containedInPlace' => array(
                '@type' => 'City',
                'name'  => 'Gold Coast',
                'containedInPlace' => array(
                    '@type' => 'State',
                    'name'  => 'Queensland',
                ),
            ),
        ),
        'priceRange' => '$$',
        'currenciesAccepted' => 'AUD',
        'paymentAccepted'    => 'Credit Card, Bank Transfer, PEXA',
        'openingHoursSpecification' => array(
            '@type'       => 'OpeningHoursSpecification',
            'dayOfWeek'   => array('Monday','Tuesday','Wednesday','Thursday','Friday'),
            'opens'       => '08:30',
            'closes'      => '17:00',
        ),
        'hasOfferCatalog' => array(
            '@type' => 'OfferCatalog',
            'name'  => 'Conveyancing Services',
            'itemListElement' => array(
                array('@type' => 'Offer', 'itemOffered' => array('@type' => 'Service', 'name' => 'Property Purchase Conveyancing')),
                array('@type' => 'Offer', 'itemOffered' => array('@type' => 'Service', 'name' => 'Property Sale Conveyancing')),
                array('@type' => 'Offer', 'itemOffered' => array('@type' => 'Service', 'name' => 'Contract Review')),
                array('@type' => 'Offer', 'itemOffered' => array('@type' => 'Service', 'name' => 'PEXA Electronic Settlement')),
            ),
        ),
        'sameAs' => array(
            'https://www.facebook.com/onestoplegal',
            'https://www.linkedin.com/company/onestop-legal',
        ),
    );
    echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";

    // ── FAQPage Schema ─────────────────────────────────────────────────────
    $faq_answers = osl_cq_extract_faq_answers($post->post_content, $name);
    $faqs = array(
        array('q' => "How much does a conveyancer cost in {$name}?",                     'a' => $faq_answers['cost']),
        array('q' => "Do I need a conveyancer or solicitor to buy property in {$name}?", 'a' => $faq_answers['solicitor']),
        array('q' => "How long does conveyancing take in {$name}?",                      'a' => $faq_answers['timeline']),
        array('q' => "Can you handle conveyancing remotely for {$name} properties?",     'a' => $faq_answers['remote']),
    );
    $faq_schema = array(
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => array(),
    );
    foreach ($faqs as $faq) {
        $faq_schema['mainEntity'][] = array(
            '@type' => 'Question',
            'name'  => $faq['q'],
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text'  => $faq['a'],
            ),
        );
    }
    echo '<script type="application/ld+json">' . json_encode($faq_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";

    // ── BreadcrumbList Schema ──────────────────────────────────────────────
    $breadcrumb_schema = array(
        '@context' => 'https://schema.org',
        '@type'    => 'BreadcrumbList',
        'itemListElement' => array(
            array(
                '@type'    => 'ListItem',
                'position' => 1,
                'name'     => 'Home',
                'item'     => 'https://djrconveyancing.com.au/',
            ),
            array(
                '@type'    => 'ListItem',
                'position' => 2,
                'name'     => 'Conveyancing',
                'item'     => 'https://djrconveyancing.com.au/conveyancing/',
            ),
            array(
                '@type'    => 'ListItem',
                'position' => 3,
                'name'     => "Conveyancer {$name}",
                'item'     => $url,
            ),
        ),
    );
    echo '<script type="application/ld+json">' . json_encode($breadcrumb_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}

// ── Remove theme/WordPress default title tag so ours takes over ────────────
add_filter('pre_get_document_title', 'osl_cq_suburb_page_title');
function osl_cq_suburb_page_title($title) {
    if (!is_singular('page')) return $title;
    global $post;
    $suburbs = osl_cq_get_gold_coast_suburbs();
    if (!preg_match('/\[osl_quote_form suburb="([^"]+)"/', $post->post_content, $m)) return $title;
    $slug = $m[1];
    if (!isset($suburbs[$slug])) return $title;
    $name     = $suburbs[$slug]['name'];
    $postcode = $suburbs[$slug]['postcode'];
    return "Conveyancer {$name} {$postcode} | Fixed-Fee Gold Coast Conveyancing | DJR Conveyancing";
}

// ── Remove theme SEO output on suburb pages so our plugin owns all meta ────
add_action('wp', 'osl_cq_remove_theme_seo');
function osl_cq_remove_theme_seo() {
    if (!is_singular('page')) return;
    global $post;
    if (!preg_match('/\[osl_quote_form suburb="([^"]+)"/', $post->post_content, $m)) return;
    $suburbs = osl_cq_get_gold_coast_suburbs();
    if (!isset($suburbs[$m[1]])) return;
    remove_action('wp_head', 'osl_custom_seo_meta', 1);
    remove_filter('pre_get_document_title', 'osl_custom_document_title', 10);
    remove_action('wp_head', 'rel_canonical');
    remove_action('wp_head', 'wp_shortlink_wp_head');
}


// ═══════════════════════════════════════════════════════════════════════════
// REGIONAL COUNCILS — appended below. Gold Coast code above is untouched.
// ═══════════════════════════════════════════════════════════════════════════

function osl_cq_get_brisbane_suburbs() {
    return array(
        'brisbane-city'       => array('name'=>'Brisbane City',       'postcode'=>'4000','desc'=>'the heart of Queensland\'s capital city',                             'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'spring-hill'         => array('name'=>'Spring Hill',         'postcode'=>'4000','desc'=>'a historic inner-city suburb adjacent to the Brisbane CBD',           'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'fortitude-valley'    => array('name'=>'Fortitude Valley',    'postcode'=>'4006','desc'=>'Brisbane\'s vibrant entertainment and residential precinct',           'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'new-farm'            => array('name'=>'New Farm',            'postcode'=>'4005','desc'=>'a prestigious inner-Brisbane riverside suburb',                        'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'teneriffe'           => array('name'=>'Teneriffe',           'postcode'=>'4005','desc'=>'an exclusive riverside suburb with converted woolstore apartments',    'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'newstead'            => array('name'=>'Newstead',            'postcode'=>'4006','desc'=>'a rapidly growing inner-Brisbane lifestyle precinct',                  'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'kelvin-grove'        => array('name'=>'Kelvin Grove',        'postcode'=>'4059','desc'=>'a vibrant suburb home to QUT and the Kelvin Grove Urban Village',      'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'red-hill'            => array('name'=>'Red Hill',            'postcode'=>'4059','desc'=>'a sought-after inner-Brisbane hilltop suburb',                         'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'paddington'          => array('name'=>'Paddington',          'postcode'=>'4064','desc'=>'a charming inner-Brisbane suburb known for its Queenslander homes',    'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'toowong'             => array('name'=>'Toowong',             'postcode'=>'4066','desc'=>'a major inner-west Brisbane hub with strong transport links',           'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'st-lucia'            => array('name'=>'St Lucia',            'postcode'=>'4067','desc'=>'home to the University of Queensland and prestigious river homes',     'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'indooroopilly'       => array('name'=>'Indooroopilly',       'postcode'=>'4068','desc'=>'a major western Brisbane suburb with a large shopping centre',         'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'chapel-hill'         => array('name'=>'Chapel Hill',         'postcode'=>'4069','desc'=>'a leafy western Brisbane suburb popular with families',                'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'kenmore'             => array('name'=>'Kenmore',             'postcode'=>'4069','desc'=>'a well-established family suburb in western Brisbane',                 'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'west-end'            => array('name'=>'West End',            'postcode'=>'4101','desc'=>'a vibrant and diverse inner-south Brisbane suburb',                    'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'south-brisbane'      => array('name'=>'South Brisbane',      'postcode'=>'4101','desc'=>'home to the Queensland Cultural Centre and South Bank Parklands',      'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'woolloongabba'       => array('name'=>'Woolloongabba',       'postcode'=>'4102','desc'=>'a rapidly transforming inner-south Brisbane suburb',                   'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'coorparoo'           => array('name'=>'Coorparoo',           'postcode'=>'4151','desc'=>'a popular inner-south Brisbane suburb with strong local amenities',    'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'camp-hill'           => array('name'=>'Camp Hill',           'postcode'=>'4152','desc'=>'a prestigious elevated suburb in inner-south Brisbane',                'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'greenslopes'         => array('name'=>'Greenslopes',         'postcode'=>'4120','desc'=>'a well-connected inner-south Brisbane suburb',                         'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'annerley'            => array('name'=>'Annerley',            'postcode'=>'4103','desc'=>'an affordable and well-located inner-south Brisbane suburb',           'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'morningside'         => array('name'=>'Morningside',         'postcode'=>'4170','desc'=>'a thriving inner-east Brisbane suburb close to the CBD',               'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'cannon-hill'         => array('name'=>'Cannon Hill',         'postcode'=>'4170','desc'=>'a well-established eastern Brisbane suburb',                           'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'carindale'           => array('name'=>'Carindale',           'postcode'=>'4152','desc'=>'a major eastern Brisbane suburb with a large shopping centre',         'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'wynnum'              => array('name'=>'Wynnum',              'postcode'=>'4178','desc'=>'a popular bayside suburb in eastern Brisbane',                         'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'manly'               => array('name'=>'Manly',               'postcode'=>'4179','desc'=>'a popular Brisbane bayside suburb with a marina',                      'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'chermside'           => array('name'=>'Chermside',           'postcode'=>'4032','desc'=>'Brisbane\'s major northern suburban hub and shopping destination',     'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'aspley'              => array('name'=>'Aspley',              'postcode'=>'4034','desc'=>'a well-established family suburb in northern Brisbane',                'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'nundah'              => array('name'=>'Nundah',              'postcode'=>'4012','desc'=>'a rapidly growing northern Brisbane suburb with train access',         'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'albany-creek'        => array('name'=>'Albany Creek',        'postcode'=>'4035','desc'=>'a large family suburb in Brisbane\'s northern corridor',               'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'everton-park'        => array('name'=>'Everton Park',        'postcode'=>'4053','desc'=>'a popular northern Brisbane suburb known for its cafe strip',          'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'sunnybank'           => array('name'=>'Sunnybank',           'postcode'=>'4109','desc'=>'a major southern Brisbane suburb and popular dining destination',      'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'sunnybank-hills'     => array('name'=>'Sunnybank Hills',     'postcode'=>'4109','desc'=>'a large family suburb in southern Brisbane',                           'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'runcorn'             => array('name'=>'Runcorn',             'postcode'=>'4113','desc'=>'an affordable southern Brisbane suburb with good transport links',      'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'eight-mile-plains'   => array('name'=>'Eight Mile Plains',   'postcode'=>'4113','desc'=>'a southern Brisbane technology and residential hub',                   'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'mount-gravatt'       => array('name'=>'Mount Gravatt',       'postcode'=>'4122','desc'=>'a major south-eastern Brisbane hub with Westfield shopping',          'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
        'upper-mount-gravatt' => array('name'=>'Upper Mount Gravatt', 'postcode'=>'4122','desc'=>'a major south-eastern Brisbane commercial and residential centre',    'council'=>'brisbane-city','region'=>'Brisbane','region_slug'=>'brisbane'),
    );
}

function osl_cq_get_moreton_bay_suburbs() {
    return array(
        'caboolture'     => array('name'=>'Caboolture',     'postcode'=>'4510','desc'=>'the major service centre for the northern Moreton Bay region',        'council'=>'moreton-bay-regional','region'=>'Moreton Bay','region_slug'=>'moreton-bay'),
        'morayfield'     => array('name'=>'Morayfield',     'postcode'=>'4506','desc'=>'one of Moreton Bay\'s fastest-growing residential suburbs',           'council'=>'moreton-bay-regional','region'=>'Moreton Bay','region_slug'=>'moreton-bay'),
        'narangba'       => array('name'=>'Narangba',       'postcode'=>'4504','desc'=>'a rapidly expanding family suburb in the Moreton Bay region',         'council'=>'moreton-bay-regional','region'=>'Moreton Bay','region_slug'=>'moreton-bay'),
        'burpengary'     => array('name'=>'Burpengary',     'postcode'=>'4505','desc'=>'a growing residential suburb in the Moreton Bay corridor',            'council'=>'moreton-bay-regional','region'=>'Moreton Bay','region_slug'=>'moreton-bay'),
        'deception-bay'  => array('name'=>'Deception Bay',  'postcode'=>'4508','desc'=>'an affordable bayside suburb in the Moreton Bay region',              'council'=>'moreton-bay-regional','region'=>'Moreton Bay','region_slug'=>'moreton-bay'),
        'kallangur'      => array('name'=>'Kallangur',      'postcode'=>'4503','desc'=>'a well-established suburb in southern Moreton Bay',                   'council'=>'moreton-bay-regional','region'=>'Moreton Bay','region_slug'=>'moreton-bay'),
        'mango-hill'     => array('name'=>'Mango Hill',     'postcode'=>'4509','desc'=>'a fast-growing master-planned suburb in Moreton Bay',                  'council'=>'moreton-bay-regional','region'=>'Moreton Bay','region_slug'=>'moreton-bay'),
        'north-lakes'    => array('name'=>'North Lakes',    'postcode'=>'4509','desc'=>'a major master-planned community in the Moreton Bay region',           'council'=>'moreton-bay-regional','region'=>'Moreton Bay','region_slug'=>'moreton-bay'),
        'redcliffe'      => array('name'=>'Redcliffe',      'postcode'=>'4020','desc'=>'a historic coastal city on the Moreton Bay peninsula',                 'council'=>'moreton-bay-regional','region'=>'Moreton Bay','region_slug'=>'moreton-bay'),
        'kippa-ring'     => array('name'=>'Kippa-Ring',     'postcode'=>'4021','desc'=>'a bayside suburb on the Redcliffe Peninsula',                          'council'=>'moreton-bay-regional','region'=>'Moreton Bay','region_slug'=>'moreton-bay'),
        'clontarf'       => array('name'=>'Clontarf',       'postcode'=>'4019','desc'=>'a waterfront suburb on the Redcliffe Peninsula',                       'council'=>'moreton-bay-regional','region'=>'Moreton Bay','region_slug'=>'moreton-bay'),
        'rothwell'       => array('name'=>'Rothwell',       'postcode'=>'4022','desc'=>'a growing suburb between Redcliffe and North Lakes',                   'council'=>'moreton-bay-regional','region'=>'Moreton Bay','region_slug'=>'moreton-bay'),
        'griffin'        => array('name'=>'Griffin',        'postcode'=>'4503','desc'=>'a newer residential suburb in the Moreton Bay growth corridor',        'council'=>'moreton-bay-regional','region'=>'Moreton Bay','region_slug'=>'moreton-bay'),
        'petrie'         => array('name'=>'Petrie',         'postcode'=>'4502','desc'=>'an established suburb in the southern Moreton Bay region',              'council'=>'moreton-bay-regional','region'=>'Moreton Bay','region_slug'=>'moreton-bay'),
        'strathpine'     => array('name'=>'Strathpine',     'postcode'=>'4500','desc'=>'a key commercial centre in the Pine Rivers area of Moreton Bay',       'council'=>'moreton-bay-regional','region'=>'Moreton Bay','region_slug'=>'moreton-bay'),
        'brendale'       => array('name'=>'Brendale',       'postcode'=>'4500','desc'=>'an industrial and residential suburb in the Pine Rivers corridor',     'council'=>'moreton-bay-regional','region'=>'Moreton Bay','region_slug'=>'moreton-bay'),
        'lawnton'        => array('name'=>'Lawnton',        'postcode'=>'4501','desc'=>'a quiet family suburb in the Pine Rivers area of Moreton Bay',         'council'=>'moreton-bay-regional','region'=>'Moreton Bay','region_slug'=>'moreton-bay'),
        'woody-point'    => array('name'=>'Woody Point',    'postcode'=>'4019','desc'=>'a charming bayside suburb on the Redcliffe Peninsula',                 'council'=>'moreton-bay-regional','region'=>'Moreton Bay','region_slug'=>'moreton-bay'),
        'ningi'          => array('name'=>'Ningi',          'postcode'=>'4511','desc'=>'a semi-rural suburb north of Caboolture in the Moreton Bay region',    'council'=>'moreton-bay-regional','region'=>'Moreton Bay','region_slug'=>'moreton-bay'),
        'beachmere'      => array('name'=>'Beachmere',      'postcode'=>'4510','desc'=>'a quiet coastal suburb north of Caboolture on Moreton Bay',            'council'=>'moreton-bay-regional','region'=>'Moreton Bay','region_slug'=>'moreton-bay'),
    );
}

function osl_cq_get_sunshine_coast_suburbs() {
    return array(
        'maroochydore'   => array('name'=>'Maroochydore',   'postcode'=>'4558','desc'=>'the commercial heart of the Sunshine Coast',                           'council'=>'sunshine-coast-regional','region'=>'Sunshine Coast','region_slug'=>'sunshine-coast'),
        'mooloolaba'     => array('name'=>'Mooloolaba',     'postcode'=>'4557','desc'=>'a popular beachside destination on the Sunshine Coast',                'council'=>'sunshine-coast-regional','region'=>'Sunshine Coast','region_slug'=>'sunshine-coast'),
        'caloundra'      => array('name'=>'Caloundra',      'postcode'=>'4551','desc'=>'the southern gateway to the Sunshine Coast',                           'council'=>'sunshine-coast-regional','region'=>'Sunshine Coast','region_slug'=>'sunshine-coast'),
        'kawana-waters'  => array('name'=>'Kawana Waters',  'postcode'=>'4575','desc'=>'a major master-planned coastal community on the Sunshine Coast',       'council'=>'sunshine-coast-regional','region'=>'Sunshine Coast','region_slug'=>'sunshine-coast'),
        'buddina'        => array('name'=>'Buddina',        'postcode'=>'4575','desc'=>'a beachside suburb in the Kawana Waters precinct',                     'council'=>'sunshine-coast-regional','region'=>'Sunshine Coast','region_slug'=>'sunshine-coast'),
        'birtinya'       => array('name'=>'Birtinya',       'postcode'=>'4575','desc'=>'a fast-growing suburb home to Sunshine Coast University Hospital',     'council'=>'sunshine-coast-regional','region'=>'Sunshine Coast','region_slug'=>'sunshine-coast'),
        'sippy-downs'    => array('name'=>'Sippy Downs',    'postcode'=>'4556','desc'=>'home to the University of the Sunshine Coast',                         'council'=>'sunshine-coast-regional','region'=>'Sunshine Coast','region_slug'=>'sunshine-coast'),
        'buderim'        => array('name'=>'Buderim',        'postcode'=>'4556','desc'=>'a prestigious hinterland suburb overlooking the Sunshine Coast',       'council'=>'sunshine-coast-regional','region'=>'Sunshine Coast','region_slug'=>'sunshine-coast'),
        'mountain-creek' => array('name'=>'Mountain Creek', 'postcode'=>'4557','desc'=>'a popular family suburb in the heart of the Sunshine Coast',           'council'=>'sunshine-coast-regional','region'=>'Sunshine Coast','region_slug'=>'sunshine-coast'),
        'nambour'        => array('name'=>'Nambour',        'postcode'=>'4560','desc'=>'the hinterland service centre of the Sunshine Coast',                  'council'=>'sunshine-coast-regional','region'=>'Sunshine Coast','region_slug'=>'sunshine-coast'),
        'palmview'       => array('name'=>'Palmview',       'postcode'=>'4553','desc'=>'a major new growth corridor suburb on the Sunshine Coast',             'council'=>'sunshine-coast-regional','region'=>'Sunshine Coast','region_slug'=>'sunshine-coast'),
        'coolum-beach'   => array('name'=>'Coolum Beach',   'postcode'=>'4573','desc'=>'a laid-back surf beach suburb on the northern Sunshine Coast',         'council'=>'sunshine-coast-regional','region'=>'Sunshine Coast','region_slug'=>'sunshine-coast'),
        'peregian-beach' => array('name'=>'Peregian Beach', 'postcode'=>'4573','desc'=>'a sought-after coastal village on the northern Sunshine Coast',        'council'=>'sunshine-coast-regional','region'=>'Sunshine Coast','region_slug'=>'sunshine-coast'),
        'noosa-heads'    => array('name'=>'Noosa Heads',    'postcode'=>'4567','desc'=>'Queensland\'s most prestigious coastal destination',                   'council'=>'noosa-shire','region'=>'Sunshine Coast','region_slug'=>'sunshine-coast'),
        'noosaville'     => array('name'=>'Noosaville',     'postcode'=>'4566','desc'=>'a popular riverside suburb adjacent to Noosa Heads',                   'council'=>'noosa-shire','region'=>'Sunshine Coast','region_slug'=>'sunshine-coast'),
        'tewantin'       => array('name'=>'Tewantin',       'postcode'=>'4565','desc'=>'the gateway suburb to Noosa and the Noosa River',                      'council'=>'noosa-shire','region'=>'Sunshine Coast','region_slug'=>'sunshine-coast'),
        'sunshine-beach' => array('name'=>'Sunshine Beach', 'postcode'=>'4567','desc'=>'an exclusive beachside enclave adjoining Noosa National Park',         'council'=>'noosa-shire','region'=>'Sunshine Coast','region_slug'=>'sunshine-coast'),
    );
}

function osl_cq_get_ipswich_suburbs() {
    return array(
        'ipswich'            => array('name'=>'Ipswich',            'postcode'=>'4305','desc'=>'Queensland\'s oldest provincial city with a strong property market',  'council'=>'ipswich-city','region'=>'Ipswich','region_slug'=>'ipswich'),
        'springfield'        => array('name'=>'Springfield',        'postcode'=>'4300','desc'=>'Australia\'s largest master-planned city outside a capital',          'council'=>'ipswich-city','region'=>'Ipswich','region_slug'=>'ipswich'),
        'springfield-lakes'  => array('name'=>'Springfield Lakes',  'postcode'=>'4300','desc'=>'a premier master-planned lakeside community in Ipswich',              'council'=>'ipswich-city','region'=>'Ipswich','region_slug'=>'ipswich'),
        'ripley'             => array('name'=>'Ripley',             'postcode'=>'4306','desc'=>'one of Queensland\'s fastest-growing new suburbs in Ipswich',         'council'=>'ipswich-city','region'=>'Ipswich','region_slug'=>'ipswich'),
        'collingwood-park'   => array('name'=>'Collingwood Park',   'postcode'=>'4301','desc'=>'a growing family suburb in the Ipswich corridor',                     'council'=>'ipswich-city','region'=>'Ipswich','region_slug'=>'ipswich'),
        'redbank-plains'     => array('name'=>'Redbank Plains',     'postcode'=>'4301','desc'=>'a fast-growing affordable suburb in western Ipswich',                 'council'=>'ipswich-city','region'=>'Ipswich','region_slug'=>'ipswich'),
        'goodna'             => array('name'=>'Goodna',             'postcode'=>'4300','desc'=>'an affordable suburb at the Brisbane-Ipswich boundary',               'council'=>'ipswich-city','region'=>'Ipswich','region_slug'=>'ipswich'),
        'brassall'           => array('name'=>'Brassall',           'postcode'=>'4305','desc'=>'a growing northern Ipswich suburb popular with families',             'council'=>'ipswich-city','region'=>'Ipswich','region_slug'=>'ipswich'),
        'leichhardt'         => array('name'=>'Leichhardt',         'postcode'=>'4305','desc'=>'a riverside suburb adjacent to Ipswich CBD',                         'council'=>'ipswich-city','region'=>'Ipswich','region_slug'=>'ipswich'),
        'bellbird-park'      => array('name'=>'Bellbird Park',      'postcode'=>'4300','desc'=>'a family suburb in the Springfield corridor of Ipswich',              'council'=>'ipswich-city','region'=>'Ipswich','region_slug'=>'ipswich'),
        'camira'             => array('name'=>'Camira',             'postcode'=>'4300','desc'=>'a well-established family suburb near Springfield in Ipswich',         'council'=>'ipswich-city','region'=>'Ipswich','region_slug'=>'ipswich'),
        'karalee'            => array('name'=>'Karalee',            'postcode'=>'4306','desc'=>'a semi-rural riverside suburb in western Ipswich',                    'council'=>'ipswich-city','region'=>'Ipswich','region_slug'=>'ipswich'),
        'north-ipswich'      => array('name'=>'North Ipswich',      'postcode'=>'4305','desc'=>'a historic suburb adjacent to Ipswich CBD',                          'council'=>'ipswich-city','region'=>'Ipswich','region_slug'=>'ipswich'),
        'booval'             => array('name'=>'Booval',             'postcode'=>'4304','desc'=>'an established inner suburb of Ipswich city',                         'council'=>'ipswich-city','region'=>'Ipswich','region_slug'=>'ipswich'),
        'augustine-heights'  => array('name'=>'Augustine Heights',  'postcode'=>'4300','desc'=>'a new residential estate in the Springfield growth corridor',         'council'=>'ipswich-city','region'=>'Ipswich','region_slug'=>'ipswich'),
    );
}

function osl_cq_get_logan_suburbs() {
    return array(
        'logan-central'  => array('name'=>'Logan Central',  'postcode'=>'4114','desc'=>'the civic heart of Logan City',                                        'council'=>'logan-city','region'=>'Logan','region_slug'=>'logan'),
        'browns-plains'  => array('name'=>'Browns Plains',  'postcode'=>'4118','desc'=>'a major commercial and residential hub in Logan City',                 'council'=>'logan-city','region'=>'Logan','region_slug'=>'logan'),
        'springwood'     => array('name'=>'Springwood',     'postcode'=>'4127','desc'=>'a key northern Logan suburb bordering Brisbane',                       'council'=>'logan-city','region'=>'Logan','region_slug'=>'logan'),
        'underwood'      => array('name'=>'Underwood',      'postcode'=>'4119','desc'=>'an industrial and residential suburb on the Logan-Brisbane border',    'council'=>'logan-city','region'=>'Logan','region_slug'=>'logan'),
        'meadowbrook'    => array('name'=>'Meadowbrook',    'postcode'=>'4131','desc'=>'home to Logan Hospital and a growing residential community',           'council'=>'logan-city','region'=>'Logan','region_slug'=>'logan'),
        'loganholme'     => array('name'=>'Loganholme',     'postcode'=>'4129','desc'=>'a well-connected suburb in the northern Logan corridor',               'council'=>'logan-city','region'=>'Logan','region_slug'=>'logan'),
        'beenleigh'      => array('name'=>'Beenleigh',      'postcode'=>'4207','desc'=>'a major southern Logan hub at the gateway to the Gold Coast',          'council'=>'logan-city','region'=>'Logan','region_slug'=>'logan'),
        'eagleby'        => array('name'=>'Eagleby',        'postcode'=>'4207','desc'=>'an affordable suburb adjacent to Beenleigh in southern Logan',         'council'=>'logan-city','region'=>'Logan','region_slug'=>'logan'),
        'waterford-west' => array('name'=>'Waterford West', 'postcode'=>'4133','desc'=>'a family suburb in central Logan City',                                'council'=>'logan-city','region'=>'Logan','region_slug'=>'logan'),
        'jimboomba'      => array('name'=>'Jimboomba',      'postcode'=>'4280','desc'=>'a semi-rural growth suburb in southern Logan',                         'council'=>'logan-city','region'=>'Logan','region_slug'=>'logan'),
        'marsden'        => array('name'=>'Marsden',        'postcode'=>'4132','desc'=>'an established residential suburb in central Logan',                   'council'=>'logan-city','region'=>'Logan','region_slug'=>'logan'),
        'park-ridge'     => array('name'=>'Park Ridge',     'postcode'=>'4125','desc'=>'a growing semi-rural suburb in western Logan',                         'council'=>'logan-city','region'=>'Logan','region_slug'=>'logan'),
        'daisy-hill'     => array('name'=>'Daisy Hill',     'postcode'=>'4127','desc'=>'a family suburb in northern Logan adjacent to conservation parkland',  'council'=>'logan-city','region'=>'Logan','region_slug'=>'logan'),
        'slacks-creek'   => array('name'=>'Slacks Creek',   'postcode'=>'4127','desc'=>'an established residential suburb in northern Logan',                  'council'=>'logan-city','region'=>'Logan','region_slug'=>'logan'),
        'regents-park'   => array('name'=>'Regents Park',   'postcode'=>'4118','desc'=>'a residential suburb in western Logan City',                           'council'=>'logan-city','region'=>'Logan','region_slug'=>'logan'),
    );
}

function osl_cq_get_redland_suburbs() {
    return array(
        'cleveland'        => array('name'=>'Cleveland',        'postcode'=>'4163','desc'=>'the civic and commercial centre of Redland City',                      'council'=>'redland-city','region'=>'Redland City','region_slug'=>'redland'),
        'capalaba'         => array('name'=>'Capalaba',         'postcode'=>'4157','desc'=>'the major commercial hub of Redland City',                             'council'=>'redland-city','region'=>'Redland City','region_slug'=>'redland'),
        'victoria-point'   => array('name'=>'Victoria Point',   'postcode'=>'4165','desc'=>'a popular bayside suburb in Redland City',                             'council'=>'redland-city','region'=>'Redland City','region_slug'=>'redland'),
        'thornlands'       => array('name'=>'Thornlands',       'postcode'=>'4164','desc'=>'a fast-growing family suburb in Redland City',                         'council'=>'redland-city','region'=>'Redland City','region_slug'=>'redland'),
        'redland-bay'      => array('name'=>'Redland Bay',      'postcode'=>'4165','desc'=>'a bayside suburb at the southern tip of Redland City',                 'council'=>'redland-city','region'=>'Redland City','region_slug'=>'redland'),
        'alexandra-hills'  => array('name'=>'Alexandra Hills',  'postcode'=>'4161','desc'=>'a well-established family suburb in western Redland City',              'council'=>'redland-city','region'=>'Redland City','region_slug'=>'redland'),
        'ormiston'         => array('name'=>'Ormiston',         'postcode'=>'4160','desc'=>'a prestige bayside suburb in Redland City',                            'council'=>'redland-city','region'=>'Redland City','region_slug'=>'redland'),
        'wellington-point' => array('name'=>'Wellington Point', 'postcode'=>'4160','desc'=>'a sought-after bayside suburb with a historic jetty',                  'council'=>'redland-city','region'=>'Redland City','region_slug'=>'redland'),
        'birkdale'         => array('name'=>'Birkdale',         'postcode'=>'4159','desc'=>'a leafy bayside suburb in northern Redland City',                      'council'=>'redland-city','region'=>'Redland City','region_slug'=>'redland'),
        'mount-cotton'     => array('name'=>'Mount Cotton',     'postcode'=>'4165','desc'=>'a semi-rural suburb in southern Redland City',                         'council'=>'redland-city','region'=>'Redland City','region_slug'=>'redland'),
    );
}

function osl_cq_get_toowoomba_suburbs() {
    return array(
        'toowoomba-city'  => array('name'=>'Toowoomba City',  'postcode'=>'4350','desc'=>'the Garden City and major regional centre of southern Queensland',     'council'=>'toowoomba-regional','region'=>'Toowoomba','region_slug'=>'toowoomba'),
        'rangeville'      => array('name'=>'Rangeville',      'postcode'=>'4350','desc'=>'a prestigious eastern suburb of Toowoomba',                            'council'=>'toowoomba-regional','region'=>'Toowoomba','region_slug'=>'toowoomba'),
        'east-toowoomba'  => array('name'=>'East Toowoomba',  'postcode'=>'4350','desc'=>'an elevated and desirable suburb on Toowoomba\'s eastern ridge',       'council'=>'toowoomba-regional','region'=>'Toowoomba','region_slug'=>'toowoomba'),
        'south-toowoomba' => array('name'=>'South Toowoomba', 'postcode'=>'4350','desc'=>'a well-established residential suburb south of Toowoomba CBD',         'council'=>'toowoomba-regional','region'=>'Toowoomba','region_slug'=>'toowoomba'),
        'harristown'      => array('name'=>'Harristown',      'postcode'=>'4350','desc'=>'a family suburb in southern Toowoomba',                                'council'=>'toowoomba-regional','region'=>'Toowoomba','region_slug'=>'toowoomba'),
        'kearneys-spring' => array('name'=>'Kearneys Spring', 'postcode'=>'4350','desc'=>'one of Toowoomba\'s most popular southern residential suburbs',        'council'=>'toowoomba-regional','region'=>'Toowoomba','region_slug'=>'toowoomba'),
        'darling-heights' => array('name'=>'Darling Heights', 'postcode'=>'4350','desc'=>'a family-friendly suburb in southern Toowoomba',                       'council'=>'toowoomba-regional','region'=>'Toowoomba','region_slug'=>'toowoomba'),
        'wilsonton'       => array('name'=>'Wilsonton',       'postcode'=>'4350','desc'=>'a northern Toowoomba suburb popular with families',                    'council'=>'toowoomba-regional','region'=>'Toowoomba','region_slug'=>'toowoomba'),
        'rockville'       => array('name'=>'Rockville',       'postcode'=>'4350','desc'=>'a quiet residential suburb in northern Toowoomba',                     'council'=>'toowoomba-regional','region'=>'Toowoomba','region_slug'=>'toowoomba'),
        'highfields'      => array('name'=>'Highfields',      'postcode'=>'4352','desc'=>'the fastest-growing suburb north of Toowoomba',                        'council'=>'toowoomba-regional','region'=>'Toowoomba','region_slug'=>'toowoomba'),
        'newtown'         => array('name'=>'Newtown',         'postcode'=>'4350','desc'=>'a historic inner suburb close to Toowoomba CBD',                       'council'=>'toowoomba-regional','region'=>'Toowoomba','region_slug'=>'toowoomba'),
        'glenvale'        => array('name'=>'Glenvale',        'postcode'=>'4350','desc'=>'a growing western Toowoomba suburb popular with families',             'council'=>'toowoomba-regional','region'=>'Toowoomba','region_slug'=>'toowoomba'),
    );
}

function osl_cq_get_cairns_suburbs() {
    return array(
        'cairns-city'    => array('name'=>'Cairns City',    'postcode'=>'4870','desc'=>'the tropical capital of Far North Queensland',                        'council'=>'cairns','region'=>'Cairns','region_slug'=>'cairns'),
        'cairns-north'   => array('name'=>'Cairns North',   'postcode'=>'4870','desc'=>'a residential suburb immediately north of Cairns CBD',                'council'=>'cairns','region'=>'Cairns','region_slug'=>'cairns'),
        'manoora'        => array('name'=>'Manoora',        'postcode'=>'4870','desc'=>'an affordable residential suburb in inner Cairns',                    'council'=>'cairns','region'=>'Cairns','region_slug'=>'cairns'),
        'manunda'        => array('name'=>'Manunda',        'postcode'=>'4870','desc'=>'a central residential suburb west of Cairns CBD',                     'council'=>'cairns','region'=>'Cairns','region_slug'=>'cairns'),
        'earlville'      => array('name'=>'Earlville',      'postcode'=>'4870','desc'=>'a major southern Cairns commercial and residential hub',               'council'=>'cairns','region'=>'Cairns','region_slug'=>'cairns'),
        'edmonton'       => array('name'=>'Edmonton',       'postcode'=>'4869','desc'=>'a fast-growing suburb south of Cairns',                               'council'=>'cairns','region'=>'Cairns','region_slug'=>'cairns'),
        'gordonvale'     => array('name'=>'Gordonvale',     'postcode'=>'4865','desc'=>'a cane-farming town south of Cairns with affordable properties',      'council'=>'cairns','region'=>'Cairns','region_slug'=>'cairns'),
        'woree'          => array('name'=>'Woree',          'postcode'=>'4868','desc'=>'a southern Cairns suburb with strong community amenities',             'council'=>'cairns','region'=>'Cairns','region_slug'=>'cairns'),
        'smithfield'     => array('name'=>'Smithfield',     'postcode'=>'4878','desc'=>'a northern Cairns suburb close to James Cook University',              'council'=>'cairns','region'=>'Cairns','region_slug'=>'cairns'),
        'trinity-beach'  => array('name'=>'Trinity Beach',  'postcode'=>'4879','desc'=>'a popular beachside suburb north of Cairns',                          'council'=>'cairns','region'=>'Cairns','region_slug'=>'cairns'),
        'clifton-beach'  => array('name'=>'Clifton Beach',  'postcode'=>'4879','desc'=>'a sought-after coastal suburb on the northern Cairns beaches',        'council'=>'cairns','region'=>'Cairns','region_slug'=>'cairns'),
        'palm-cove'      => array('name'=>'Palm Cove',      'postcode'=>'4879','desc'=>'a prestigious resort and residential suburb north of Cairns',          'council'=>'cairns','region'=>'Cairns','region_slug'=>'cairns'),
        'kewarra-beach'  => array('name'=>'Kewarra Beach',  'postcode'=>'4879','desc'=>'a relaxed beachside suburb on the northern Cairns coast',             'council'=>'cairns','region'=>'Cairns','region_slug'=>'cairns'),
        'caravonica'     => array('name'=>'Caravonica',     'postcode'=>'4878','desc'=>'a quiet suburb at the base of the Cairns hinterland',                 'council'=>'cairns','region'=>'Cairns','region_slug'=>'cairns'),
        'white-rock'     => array('name'=>'White Rock',     'postcode'=>'4868','desc'=>'a southern Cairns suburb with a mix of residential properties',       'council'=>'cairns','region'=>'Cairns','region_slug'=>'cairns'),
    );
}

function osl_cq_get_townsville_suburbs() {
    return array(
        'townsville-city'    => array('name'=>'Townsville City',    'postcode'=>'4810','desc'=>'the major city of North Queensland',                                  'council'=>'townsville-city','region'=>'Townsville','region_slug'=>'townsville'),
        'north-ward'         => array('name'=>'North Ward',         'postcode'=>'4810','desc'=>'a prestigious beachside suburb in inner Townsville',                  'council'=>'townsville-city','region'=>'Townsville','region_slug'=>'townsville'),
        'south-townsville'   => array('name'=>'South Townsville',   'postcode'=>'4810','desc'=>'an inner Townsville suburb on the Ross Creek waterfront',             'council'=>'townsville-city','region'=>'Townsville','region_slug'=>'townsville'),
        'hyde-park'          => array('name'=>'Hyde Park',          'postcode'=>'4812','desc'=>'a popular inner suburb of Townsville',                                'council'=>'townsville-city','region'=>'Townsville','region_slug'=>'townsville'),
        'hermit-park'        => array('name'=>'Hermit Park',        'postcode'=>'4812','desc'=>'a well-established inner residential suburb of Townsville',           'council'=>'townsville-city','region'=>'Townsville','region_slug'=>'townsville'),
        'kirwan'             => array('name'=>'Kirwan',             'postcode'=>'4817','desc'=>'a major residential suburb in the Thuringowa area of Townsville',     'council'=>'townsville-city','region'=>'Townsville','region_slug'=>'townsville'),
        'thuringowa-central' => array('name'=>'Thuringowa Central', 'postcode'=>'4817','desc'=>'a growing commercial and residential hub in western Townsville',      'council'=>'townsville-city','region'=>'Townsville','region_slug'=>'townsville'),
        'annandale'          => array('name'=>'Annandale',          'postcode'=>'4814','desc'=>'a family suburb in the western Townsville corridor',                  'council'=>'townsville-city','region'=>'Townsville','region_slug'=>'townsville'),
        'aitkenvale'         => array('name'=>'Aitkenvale',         'postcode'=>'4814','desc'=>'a well-located residential suburb in central Townsville',             'council'=>'townsville-city','region'=>'Townsville','region_slug'=>'townsville'),
        'mundingburra'       => array('name'=>'Mundingburra',       'postcode'=>'4812','desc'=>'a central Townsville suburb close to CBD and university',             'council'=>'townsville-city','region'=>'Townsville','region_slug'=>'townsville'),
        'idalia'             => array('name'=>'Idalia',             'postcode'=>'4811','desc'=>'a prestige lakeside suburb in eastern Townsville',                    'council'=>'townsville-city','region'=>'Townsville','region_slug'=>'townsville'),
        'mount-low'          => array('name'=>'Mount Low',          'postcode'=>'4818','desc'=>'a fast-growing northern Townsville family suburb',                    'council'=>'townsville-city','region'=>'Townsville','region_slug'=>'townsville'),
        'bushland-beach'     => array('name'=>'Bushland Beach',     'postcode'=>'4818','desc'=>'a coastal suburb in the northern Townsville growth corridor',         'council'=>'townsville-city','region'=>'Townsville','region_slug'=>'townsville'),
        'bohle-plains'       => array('name'=>'Bohle Plains',       'postcode'=>'4817','desc'=>'a newer residential estate in western Townsville',                    'council'=>'townsville-city','region'=>'Townsville','region_slug'=>'townsville'),
        'magnetic-island'    => array('name'=>'Magnetic Island',    'postcode'=>'4819','desc'=>'a unique island suburb of Townsville in the Coral Sea',               'council'=>'townsville-city','region'=>'Townsville','region_slug'=>'townsville'),
    );
}

// ── MASTER LOOKUP (all councils) ───────────────────────────────────────────
function osl_cq_get_all_regional_suburbs() {
    return array_merge(
        osl_cq_get_brisbane_suburbs(),
        osl_cq_get_moreton_bay_suburbs(),
        osl_cq_get_sunshine_coast_suburbs(),
        osl_cq_get_ipswich_suburbs(),
        osl_cq_get_logan_suburbs(),
        osl_cq_get_redland_suburbs(),
        osl_cq_get_toowoomba_suburbs(),
        osl_cq_get_cairns_suburbs(),
        osl_cq_get_townsville_suburbs()
    );
}

// ── SEO META FOR REGIONAL SUBURB PAGES ────────────────────────────────────
add_action('wp_head', 'osl_cq_regional_suburb_seo_meta', 1);

function osl_cq_regional_suburb_seo_meta() {
    if (!is_singular('page')) return;
    global $post;

    // Only fire on pages using the regional suburb template
    $template = get_post_meta($post->ID, '_wp_page_template', true);
    if ($template !== 'page-suburb-landing-regional.php') return;

    $suburbs = osl_cq_get_all_regional_suburbs();

    if (!preg_match('/\[osl_quote_form suburb="([^"]+)"/', $post->post_content, $m)) return;
    $slug = $m[1];
    if (!isset($suburbs[$slug])) return;

    $name       = $suburbs[$slug]['name'];
    $postcode   = $suburbs[$slug]['postcode'];
    $region     = $suburbs[$slug]['region'];
    $url        = get_permalink();
    $title      = "Conveyancer {$name} {$postcode} | Fixed-Fee {$region} Conveyancing | DJR Conveyancing";
    $metadesc   = "Need a conveyancer in {$name} {$postcode}? DJR Conveyancing offers fixed-fee conveyancing in {$region} — buying or selling. Instant online quote, no hidden costs, electronic settlement via PEXA.";
    $sitename   = "DJR Conveyancing";
    $logo       = "https://djrconveyancing.com.au/wp-content/uploads/2024/03/cropped-onestop-legal-512-x-512-270x270.png";

    // Hub page URL for breadcrumb
    $hub_id  = wp_get_post_parent_id($post->ID);
    $hub_url = $hub_id ? get_permalink($hub_id) : 'https://djrconveyancing.com.au/conveyancing/';

    echo '<meta name="description" content="' . esc_attr($metadesc) . '" />' . "\n";
    echo '<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />' . "\n";
    echo '<link rel="canonical" href="' . esc_url($url) . '" />' . "\n";

    echo '<meta property="og:type" content="website" />' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '" />' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($metadesc) . '" />' . "\n";
    echo '<meta property="og:url" content="' . esc_url($url) . '" />' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr($sitename) . '" />' . "\n";
    echo '<meta property="og:image" content="' . esc_url($logo) . '" />' . "\n";
    echo '<meta property="og:locale" content="en_AU" />' . "\n";

    echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '" />' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($metadesc) . '" />' . "\n";
    echo '<meta name="twitter:image" content="' . esc_url($logo) . '" />' . "\n";

    $schema = array(
        '@context'   => 'https://schema.org',
        '@type'      => array('LegalService','LocalBusiness'),
        'name'       => "DJR Conveyancing - Conveyancer {$name}",
        'description'=> $metadesc,
        'url'        => $url,
        'telephone'  => '+61755129827',
        'email'      => 'info@djrconveyancing.com.au',
        'logo'       => $logo,
        'image'      => $logo,
        'address'    => array(
            '@type'           => 'PostalAddress',
            'streetAddress'   => 'Level 4, 91 Scarborough Street',
            'addressLocality' => 'Southport',
            'addressRegion'   => 'QLD',
            'postalCode'      => '4215',
            'addressCountry'  => 'AU',
        ),
        'geo'        => array('@type'=>'GeoCoordinates','latitude'=>'-27.9957','longitude'=>'153.4073'),
        'areaServed' => array(
            '@type'          => 'Place',
            'name'           => $name,
            'additionalType' => 'http://schema.org/Neighborhood',
            'postalCode'     => $postcode,
            'containedInPlace' => array(
                '@type' => 'City',
                'name'  => $region,
                'containedInPlace' => array('@type'=>'State','name'=>'Queensland'),
            ),
        ),
        'priceRange'         => '$$',
        'currenciesAccepted' => 'AUD',
        'paymentAccepted'    => 'Credit Card, Bank Transfer, PEXA',
        'openingHoursSpecification' => array(
            '@type'     => 'OpeningHoursSpecification',
            'dayOfWeek' => array('Monday','Tuesday','Wednesday','Thursday','Friday'),
            'opens'     => '08:30',
            'closes'    => '17:00',
        ),
        'hasOfferCatalog' => array(
            '@type' => 'OfferCatalog',
            'name'  => 'Conveyancing Services',
            'itemListElement' => array(
                array('@type'=>'Offer','itemOffered'=>array('@type'=>'Service','name'=>'Property Purchase Conveyancing')),
                array('@type'=>'Offer','itemOffered'=>array('@type'=>'Service','name'=>'Property Sale Conveyancing')),
                array('@type'=>'Offer','itemOffered'=>array('@type'=>'Service','name'=>'Contract Review')),
                array('@type'=>'Offer','itemOffered'=>array('@type'=>'Service','name'=>'PEXA Electronic Settlement')),
            ),
        ),
        'sameAs' => array(
            'https://www.facebook.com/onestoplegal',
            'https://www.linkedin.com/company/onestop-legal',
        ),
    );
    echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) . '</script>' . "\n";

    $faq_answers = osl_cq_extract_faq_answers($post->post_content, $name);
    $faqs = array(
        array('q'=>"How much does a conveyancer cost in {$name}?",                     'a'=>$faq_answers['cost']),
        array('q'=>"Do I need a conveyancer or solicitor to buy property in {$name}?", 'a'=>$faq_answers['solicitor']),
        array('q'=>"How long does conveyancing take in {$name}?",                      'a'=>$faq_answers['timeline']),
        array('q'=>"Can you handle conveyancing remotely for {$name} properties?",     'a'=>$faq_answers['remote']),
    );
    $faq_schema = array('@context'=>'https://schema.org','@type'=>'FAQPage','mainEntity'=>array());
    foreach ($faqs as $faq) {
        $faq_schema['mainEntity'][] = array(
            '@type'=>'Question','name'=>$faq['q'],
            'acceptedAnswer'=>array('@type'=>'Answer','text'=>$faq['a']),
        );
    }
    echo '<script type="application/ld+json">' . json_encode($faq_schema, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) . '</script>' . "\n";

    $breadcrumb_schema = array(
        '@context' => 'https://schema.org',
        '@type'    => 'BreadcrumbList',
        'itemListElement' => array(
            array('@type'=>'ListItem','position'=>1,'name'=>'Home',        'item'=>'https://djrconveyancing.com.au/'),
            array('@type'=>'ListItem','position'=>2,'name'=>'Conveyancing','item'=>'https://djrconveyancing.com.au/conveyancing/'),
            array('@type'=>'ListItem','position'=>3,'name'=>$region,       'item'=>esc_url($hub_url)),
            array('@type'=>'ListItem','position'=>4,'name'=>"Conveyancer {$name}",'item'=>$url),
        ),
    );
    echo '<script type="application/ld+json">' . json_encode($breadcrumb_schema, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) . '</script>' . "\n";
}

// ── PAGE TITLE FOR REGIONAL SUBURB PAGES ──────────────────────────────────
add_filter('pre_get_document_title', 'osl_cq_regional_suburb_page_title');
function osl_cq_regional_suburb_page_title($title) {
    if (!is_singular('page')) return $title;
    global $post;
    $template = get_post_meta($post->ID, '_wp_page_template', true);
    if ($template !== 'page-suburb-landing-regional.php') return $title;
    $suburbs = osl_cq_get_all_regional_suburbs();
    if (!preg_match('/\[osl_quote_form suburb="([^"]+)"/', $post->post_content, $m)) return $title;
    $slug = $m[1];
    if (!isset($suburbs[$slug])) return $title;
    $name     = $suburbs[$slug]['name'];
    $postcode = $suburbs[$slug]['postcode'];
    $region   = $suburbs[$slug]['region'];
    return "Conveyancer {$name} {$postcode} | Fixed-Fee {$region} Conveyancing | DJR Conveyancing";
}

// ── REMOVE THEME SEO ON REGIONAL SUBURB PAGES ─────────────────────────────
add_action('wp', 'osl_cq_remove_theme_seo_regional');
function osl_cq_remove_theme_seo_regional() {
    if (!is_singular('page')) return;
    global $post;
    $template = get_post_meta($post->ID, '_wp_page_template', true);
    if ($template !== 'page-suburb-landing-regional.php') return;
    remove_action('wp_head', 'osl_custom_seo_meta', 1);
    remove_filter('pre_get_document_title', 'osl_custom_document_title', 10);
    remove_action('wp_head', 'rel_canonical');
    remove_action('wp_head', 'wp_shortlink_wp_head');
}

// ── SEO META FOR REGIONAL HUB PAGES ───────────────────────────────────────
add_action('wp_head', 'osl_cq_regional_hub_seo_meta', 1);
function osl_cq_regional_hub_seo_meta() {
    if (!is_singular('page')) return;
    global $post;
    $template = get_post_meta($post->ID, '_wp_page_template', true);
    if ($template !== 'page-conveyancing-region.php') return;

    $region_name = get_post_meta($post->ID, '_osl_region_name', true);
    if (!$region_name) return;

    $url      = get_permalink();
    $title    = "Conveyancer {$region_name} | Fixed-Fee Conveyancing | DJR Conveyancing";
    $metadesc = "DJR Conveyancing provides fixed-fee conveyancing across all {$region_name} suburbs — buying or selling. Instant online quote, no hidden costs, electronic settlement via PEXA.";
    $logo     = "https://djrconveyancing.com.au/wp-content/uploads/2024/03/cropped-onestop-legal-512-x-512-270x270.png";

    echo '<meta name="description" content="' . esc_attr($metadesc) . '" />' . "\n";
    echo '<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />' . "\n";
    echo '<link rel="canonical" href="' . esc_url($url) . '" />' . "\n";
    echo '<meta property="og:type" content="website" />' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '" />' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($metadesc) . '" />' . "\n";
    echo '<meta property="og:url" content="' . esc_url($url) . '" />' . "\n";
    echo '<meta property="og:site_name" content="DJR Conveyancing" />' . "\n";
    echo '<meta property="og:image" content="' . esc_url($logo) . '" />' . "\n";
    echo '<meta property="og:locale" content="en_AU" />' . "\n";
    echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '" />' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($metadesc) . '" />' . "\n";
    echo '<meta name="twitter:image" content="' . esc_url($logo) . '" />' . "\n";

    $breadcrumb_schema = array(
        '@context' => 'https://schema.org',
        '@type'    => 'BreadcrumbList',
        'itemListElement' => array(
            array('@type'=>'ListItem','position'=>1,'name'=>'Home',        'item'=>'https://djrconveyancing.com.au/'),
            array('@type'=>'ListItem','position'=>2,'name'=>'Conveyancing','item'=>'https://djrconveyancing.com.au/conveyancing/'),
            array('@type'=>'ListItem','position'=>3,'name'=>"Conveyancing {$region_name}",'item'=>$url),
        ),
    );
    echo '<script type="application/ld+json">' . json_encode($breadcrumb_schema, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) . '</script>' . "\n";
}

add_filter('pre_get_document_title', 'osl_cq_regional_hub_page_title');
function osl_cq_regional_hub_page_title($title) {
    if (!is_singular('page')) return $title;
    global $post;
    $template = get_post_meta($post->ID, '_wp_page_template', true);
    if ($template !== 'page-conveyancing-region.php') return $title;
    $region_name = get_post_meta($post->ID, '_osl_region_name', true);
    if (!$region_name) return $title;
    return "Conveyancer {$region_name} | Fixed-Fee Conveyancing | DJR Conveyancing";
}

add_action('wp', 'osl_cq_remove_theme_seo_regional_hub');
function osl_cq_remove_theme_seo_regional_hub() {
    if (!is_singular('page')) return;
    global $post;
    $template = get_post_meta($post->ID, '_wp_page_template', true);
    if ($template !== 'page-conveyancing-region.php') return;
    remove_action('wp_head', 'osl_custom_seo_meta', 1);
    remove_filter('pre_get_document_title', 'osl_custom_document_title', 10);
    remove_action('wp_head', 'rel_canonical');
    remove_action('wp_head', 'wp_shortlink_wp_head');
}
