jQuery(document).ready(function($) {

    var quoteRequest = null;

    // GET QUOTE
    $(document).on('click', '#osl-cq-get-quote', function(e) {
        e.preventDefault();
        if (quoteRequest !== null) quoteRequest.abort();

        var btn = $(this);
        var data = {
            action: 'osl_cq_calculate',
            nonce: OslCQ.nonce,
            property_for: $("input[name='osl_property_for']:checked").val(),
            council: $("#osl_council").val(),
            property_type: $("#osl_property_type").val()
        };

        quoteRequest = $.ajax({
            type: 'POST', url: OslCQ.ajaxurl, data: data, dataType: 'json',
            beforeSend: function() {
                btn.html('<span class="osl-cq-loading"></span> Calculating...').prop('disabled', true);
                $('#osl-cq-result').html('<div style="text-align:center;padding:30px;"><span class="osl-cq-loading"></span> Loading your quote...</div>');
            },
            success: function(response) {
                btn.html('GET INSTANT QUOTE').prop('disabled', false);
                if (response.success) {
                    $('#osl-cq-result').html(response.data.html);
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
                    // 1. Reveal all hidden prices
                    $('.osl-cq-real-price').each(function() {
                        $(this).removeAttr('style');
                    });

                    // 2. Remove the dots
                    $('.osl-cq-dots').fadeOut(300, function() { $(this).remove(); });

                    // 3. Show total
                    $('.osl-cq-hidden-total').slideDown(500);

                    // 4. Replace gate box with confirmation
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

                    // Scroll to total
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

    // Enter key on gate email
    $(document).on('keypress', '#osl-cq-gate-email', function(e) {
        if (e.which === 13) { e.preventDefault(); $('#osl-cq-unlock-btn').click(); }
    });

    // Landing page action boxes
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
