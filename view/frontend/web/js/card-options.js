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
            var labelHtml = $label.html() || '';
            var priceMatch = labelHtml.match(/(\+?\$[\d,.]+)/);
            var priceText = priceMatch ? priceMatch[1] : '';

            // Create card wrapper
            var $card = $('<label class="panth-choice-card"></label>');
            $card.append($input.clone(true));
            $card.append('<span class="panth-choice-label">' + $label.text().replace(/\+?\$[\d,.]+/, '').trim() + '</span>');
            if (priceText) {
                $card.append('<span class="panth-choice-price">' + priceText + '</span>');
            }

            $choice.empty().append($card);
        });

        // Style select dropdowns
        $el.find('select.product-custom-option').addClass('panth-input');
    };
});
