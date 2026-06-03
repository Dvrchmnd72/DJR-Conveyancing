<?php
if (!defined('ABSPATH')) exit;

function osl_cq_render_form($atts) {
    $atts = shortcode_atts(array(
        'suburb' => '',
        'council' => '',
        'heading' => '',
    ), $atts);

    $councils = osl_cq_get_council_choices();
    $property_types = osl_cq_get_property_types();
    $suburb_name = $atts['suburb'] ? ucwords(str_replace('-', ' ', $atts['suburb'])) : '';
    $heading = $atts['heading'] ? $atts['heading'] : ($suburb_name ? $suburb_name . ' Conveyancing Quote' : 'Get an Instant Conveyancing Quote');

    ob_start();
    ?>
    <div class="osl-cq-wrapper" id="osl-cq-wrapper">
        <div class="osl-cq-form-container">
            <div class="osl-cq-form-header">
                <div class="osl-cq-logo">
                    <img src="<?php echo OSL_CQ_URL; ?>assets/logo-quote.png" alt="DJR Conveyancing" onerror="this.style.display='none'">
                </div>
                <div class="osl-cq-header-text">
                    <h3><?php echo esc_html($heading); ?></h3>
                    <span>Get an instant no obligation online quote for your property transaction</span>
                </div>
                <div class="osl-cq-clear"></div>
            </div>

            <div class="osl-cq-form-body">
                <div class="osl-cq-row">
                    <label class="osl-cq-label">I am</label>
                    <div class="osl-cq-radio-group">
                        <input type="radio" name="osl_property_for" id="osl_purchasing" value="purchasing" checked>
                        <label for="osl_purchasing">Purchasing a Property</label>
                        <input type="radio" name="osl_property_for" id="osl_selling" value="selling">
                        <label for="osl_selling">Selling a Property</label>
                    </div>
                </div>

                <div class="osl-cq-row osl-cq-row-2col">
                    <div class="osl-cq-col">
                        <label class="osl-cq-label">State</label>
                        <select name="osl_state" id="osl_state">
                            <?php foreach (osl_cq_get_supported_states() as $state_key => $state_label): ?>
                                <option value="<?php echo esc_attr($state_key); ?>" <?php selected($state_key, osl_cq_get_default_council_state()); ?>>
                                    <?php echo esc_html($state_label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="osl-cq-col osl-cq-council-field">
                        <label class="osl-cq-label">Local Council / Shire</label>
                        <select name="osl_council" id="osl_council">
                            <?php foreach ($councils as $key => $council): ?>
                                <option value="<?php echo esc_attr($key); ?>" <?php echo ($atts['council'] === $key) ? 'selected' : ''; ?>>
                                    <?php echo esc_html($council['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="osl-cq-row">
                    <div class="osl-cq-col">
                        <label class="osl-cq-label">Property Type</label>
                        <select name="osl_property_type" id="osl_property_type">
                            <?php foreach ($property_types as $key => $label): ?>
                                <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="osl-cq-row osl-cq-row-submit">
                    <button type="button" id="osl-cq-get-quote" class="osl-cq-button" style="-webkit-appearance:none;-moz-appearance:none;appearance:none;border-radius:10px;border:none;">GET INSTANT QUOTE</button>
                </div>
            </div>
        </div>

        <div class="osl-cq-result-container" id="osl-cq-result"></div>
    </div>
    <?php
    return ob_get_clean();
}
