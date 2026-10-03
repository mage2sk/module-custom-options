define(['jquery'], function ($) {
    'use strict';

    return function (config, element) {
        var $el = $(element);

        // Wrap each radio/checkbox option in a card
        $el.find('.field.choice').each(function () {
            var $choice = $(this);
            var $input = $choice.find('input[type="radio"], input[type="checkbox"]');
            var $label = $choice.find('label');

            if (!$input.length || $choice.hasClass('panth-wrapped')) {
                return;
            }

            $choice.addClass('panth-wrapped');

            // Extract price from label text
            var priceText = $.trim($label.find('.price-notice').first().text());
            var $title = $label.clone();

            $title.find('.price-notice').remove();

            // Create card wrapper
            var $card = $('<label class="panth-choice-card"></label>');
            $card.append($input.clone(true));
            $card.append($('<span class="panth-choice-label"></span>').text($.trim($title.text())));
            if (priceText) {
                $card.append($('<span class="panth-choice-price"></span>').text(priceText));
            }

            $choice.empty().append($card);
        });

        // Style select dropdowns
        $el.find('select.product-custom-option').addClass('panth-input');
    };
});
