define(['jquery', 'mage/translate'], function ($, $t) {
    'use strict';

    var labels = {
        month: $t('Month'),
        day: $t('Day'),
        year: $t('Year'),
        hour: $t('Hour'),
        minute: $t('Minute'),
        day_part: $t('AM/PM')
    };

    return function (config, element) {
        $(element).find('select').each(function () {
            var match = /\[([a-z_]+)\]$/.exec(this.name || '');

            if (match && labels[match[1]] && !this.hasAttribute('aria-label')) {
                this.setAttribute('aria-label', labels[match[1]]);
            }
        });
    };
});
