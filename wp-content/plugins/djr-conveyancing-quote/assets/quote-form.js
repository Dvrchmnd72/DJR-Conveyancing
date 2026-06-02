jQuery(document).ready(function($) {
    var quoteRequest = null;
    var quoteStartedLogged = false;
    var currentQuoteMeta = {};

    function makeQuoteToken() {
        return 'cq-' + Date.now().toString(36) + '-' + Math.random().toString(36).slice(2, 10);
    }

    var quoteToken = makeQuoteToken();

    function getUrlParam(name) {
        try {
            return new URLSearchParams(window.location.search).get(name) || '';
        } catch (e) {
            return '';
        }
    }

    function baseTrackingData() {
        return {
            action: 'osl_cq_log_event',
            nonce: OslCQ.nonce,
            quote_token: quoteToken,
            page_url: window.location.href,
            page_path: window.location.pathname,
            property_for: $("input[name='osl_property_for']:checked").val() || '',
            transaction_type: $("input[name='osl_property_for']:checked").val() || '',
            council: $("#osl_council").val() || '',
            property_type: $("#osl_property_type").val() || '',
            utm_source: getUrlParam('utm_source'),
            utm_medium: getUrlParam('utm_medium'),
            utm_campaign: getUrlParam('utm_campaign'),
            utm_term: getUrlParam('utm_term'),
            utm_content: getUrlParam('utm_content'),
            gclid: getUrlParam('gclid'),
            fbclid: getUrlParam('fbclid')
        };
    }

    function pushAnalytics(eventName, payload) {
        payload = payload || {};
        var eventPayload = $.extend({}, payload, { event: eventName });

        try {
            if (window.dataLayer && Array.isArray(window.dataLayer)) {
                window.dataLayer.push(eventPayload);
            }
        } catch (e) {}

        try {
            if (window.OSLTracking && typeof window.OSLTracking.pushEvent === 'function') {
                window.OSLTracking.pushEvent(eventName, payload);
            }
        } catch (e) {}
    }

    function logActivity(eventName, extra, navigationSafe) {
        extra = extra || {};
        var data = $.extend({}, baseTrackingData(), currentQuoteMeta, extra, { event_name: eventName });

        pushAnalytics(eventName, data);

        if (navigationSafe && navigator.sendBeacon) {
            try {
                var params = new URLSearchParams();
                Object.keys(data).forEach(function(key) {
                    if (data[key] !== undefined && data[key] !== null) params.append(key, data[key]);
                });
                var blob = new Blob([params.toString()], { type: 'application/x-www-form-urlencoded;charset=UTF-8' });
                navigator.sendBeacon(OslCQ.ajaxurl, blob);
                return;
            } catch (e) {}
        }

        if (navigationSafe && window.fetch) {
            try {
                var fetchParams = new URLSearchParams();
                Object.keys(data).forEach(function(key) {
                    if (data[key] !== undefined && data[key] !== null) fetchParams.append(key, data[key]);
                });
                fetch(OslCQ.ajaxurl, {
                    method: 'POST',
                    body: fetchParams,
                    credentials: 'same-origin',
                    keepalive: true,
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' }
                });
                return;
            } catch (e) {}
        }

        $.ajax({ type: 'POST', url: OslCQ.ajaxurl, data: data, dataType: 'json' });
    }

    function logQuoteStartedOnce() {
        if (quoteStartedLogged) return;
        quoteStartedLogged = true;
        logActivity('quote_started');
    }

    // GET QUOTE
    $(document).on('click', '#osl-cq-get-quote', function(e) {
        e.preventDefault();
        if (quoteRequest !== null) quoteRequest.abort();

        logQuoteStartedOnce();

        var btn = $(this);
        var data = $.extend({}, {
            action: 'osl_cq_calculate',
            nonce: OslCQ.nonce,
            quote_token: quoteToken,
            property_for: $("input[name='osl_property_for']:checked").val(),
            council: $("#osl_council").val(),
            property_type: $("#osl_property_type").val()
        }, {
            page_url: window.location.href,
            page_path: window.location.pathname,
            utm_source: getUrlParam('utm_source'),
            utm_medium: getUrlParam('utm_medium'),
            utm_campaign: getUrlParam('utm_campaign'),
            utm_term: getUrlParam('utm_term'),
            utm_content: getUrlParam('utm_content'),
            gclid: getUrlParam('gclid'),
            fbclid: getUrlParam('fbclid')
        });

        quoteRequest = $.ajax({
            type: 'POST', url: OslCQ.ajaxurl, data: data, dataType: 'json',
            beforeSend: function() {
                btn.html('<span class="osl-cq-loading"></span> Calculating...').prop('disabled', true);
                $('#osl-cq-result').html('<div style="text-align:center;padding:30px;"><span class="osl-cq-loading"></span> Loading your quote...</div>');
            },
            success: function(response) {
                btn.html('GET INSTANT QUOTE').prop('disabled', false);
                if (response.success) {
                    currentQuoteMeta = {
                        quote_total: response.data.quote_total || '',
                        quote_total_band: response.data.quote_total_band || '',
                        transaction_type: response.data.transaction_type || data.property_for || '',
                        property_type: response.data.property_type || data.property_type || '',
                        council: response.data.council || '',
                        suburb: response.data.suburb || ''
                    };
                    $('#osl-cq-result').html(response.data.html);
                    pushAnalytics('quote_generated', $.extend({}, baseTrackingData(), currentQuoteMeta));
                    $('html, body').animate({ scrollTop: $('#osl-cq-result').offset().top - ($(window).width() > 767 ? 100 : 10) }, 800);
                } else {
                    $('#osl-cq-result').html('<div style="color:red;padding:20px;">Error loading quote. Please try again.</div>');
                }
            },
            error: function(jqXhr, textStatus) {
                btn.html('GET INSTANT QUOTE').prop('disabled', false);
                if (textStatus !== 'abort') $('#osl-cq-result').html('<div style="color:red;padding:20px;">Error loading quote. Please try again.</div>');
            }
        });
    });

    // Result CTA tracking
    $(document).on('click', '.osl-cq-result-contact, .osl-cq-result-email, #osl-cq-result a[href^="mailto:"]', function() {
        var href = this.href || '';
        var eventName = $(this).hasClass('osl-cq-result-email') || href.indexOf('mailto:') === 0 ? 'quote_email_clicked' : 'quote_contact_clicked';
        var payload = {
            cta_location: $(this).closest('[data-cta-location]').data('cta-location') || 'quote_result',
            link_url: href
        };

        if (eventName === 'quote_email_clicked' && href.indexOf('mailto:') === 0) {
            payload.email = href.replace(/^mailto:/i, '').split('?')[0];
        }

        logActivity(eventName, payload, true);
    });

    // UNLOCK GATE
    $(document).on('click', '#osl-cq-unlock-btn', function(e) {
        e.preventDefault();
        var btn = $(this);
        var emailInput = $('#osl-cq-gate-email');
        var email = emailInput.val().trim();

        if (!email || !validateEmail(email)) {
            emailInput.addClass('osl-cq-error').attr('placeholder', 'Please enter a valid email').focus();
            return false;
        }
        emailInput.removeClass('osl-cq-error');

        var data = {
            action: 'osl_cq_unlock',
            nonce: OslCQ.nonce,
            email: email,
            property_for: $("input[name='osl_property_for']:checked").val(),
            council: $("#osl_council").val(),
            property_type: $("#osl_property_type").val()
        };

        $.ajax({
            type: 'POST', url: OslCQ.ajaxurl, data: data, dataType: 'json',
            beforeSend: function() {
                btn.html('<span class="osl-cq-loading"></span> Unlocking...').prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    $('.osl-cq-real-price').each(function() { $(this).removeAttr('style'); });
                    $('.osl-cq-dots').fadeOut(300, function() { $(this).remove(); });
                    $('.osl-cq-hidden-total').slideDown(500);
                    $('#osl-cq-gate').slideUp(500, function() {
                        $(this).replaceWith(
                            '<div class="osl-cq-confirmed" style="background:#f0faf0;border:2px solid #4CAF50;border-radius:12px;padding:25px;text-align:center;margin-top:20px;">' +
                            '<h3 style="color:#2e7d32;margin:0 0 10px 0;">&#10003; Quote Unlocked!</h3>' +
                            '<p style="color:#555;margin:0 0 15px 0;">A copy of this quote has been sent to <strong>' + email + '</strong></p>' +
                            '<p style="color:#555;margin:0 0 15px 0;">One of our conveyancing specialists will be in touch shortly.</p>' +
                            '<a href="/contact/" class="osl-cq-button" style="display:inline-block;">CONTACT US NOW</a>' +
                            '</div>'
                        );
                    });
                    setTimeout(function() {
                        $('html, body').animate({ scrollTop: $('.osl-cq-hidden-total').offset().top - 100 }, 800);
                    }, 600);
                } else {
                    btn.html('UNLOCK QUOTE').prop('disabled', false);
                    emailInput.addClass('osl-cq-error');
                }
            },
            error: function() {
                btn.html('UNLOCK QUOTE').prop('disabled', false);
            }
        });
    });

    $(document).on('keypress', '#osl-cq-gate-email', function(e) {
        if (e.which === 13) { e.preventDefault(); $('#osl-cq-unlock-btn').click(); }
    });

    $(document).on('click', '.osl-cq-action-box', function(e) {
        e.preventDefault();
        var target = $(this).data('for');
        if (target) $("input[name='osl_property_for'][value='" + target + "']").prop('checked', true);
        $('html, body').animate({ scrollTop: $('.osl-cq-form-body').offset().top - ($(window).width() > 767 ? 200 : 10) }, 800);
    });

    function validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email);
    }
});
