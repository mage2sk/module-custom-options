# Magento 2 Custom Options

"Panth Custom Options" (module `Panth_CustomOptions`) replaces the templates that Magento uses to render product customizable options on the product page. The options themselves are still defined per product in the admin under Catalog > Products > Customizable Options; the module only changes the front-end markup and styling: one bordered row per option, the option price shown as a badge next to the title, styled text inputs, textareas and selects, card-style radio and checkbox rows, a drop-zone style file upload area, and native HTML5 date, time and datetime-local inputs in the Hyva template set.

The module ships two template sets, one written for Hyva (Alpine.js and Hyva view models) and one for Luma (Magento's `priceOptions` widget and RequireJS). It is meant for stores whose products carry customizable options (engraving text, gift messages, uploaded artwork, delivery dates) and that want those fields to match the rest of the theme without rewriting the option templates.

Product page: [Custom Product Options for Magento 2 (Hyva + Luma)](https://kishansavaliya.com/magento-2-custom-options.html)

## Features

- Replaces the `product.info.options` container template and the text, select, file and date option-type templates of `Magento_Catalog` on the product page.
- Hyva template set: styled option rows, a price badge on each option that Alpine.js keeps in sync with the product final price (including the incl./excl. tax display modes handled by `Hyva\Theme\ViewModel\ProductPrice`), `data-price-amount` and `data-price-type` attributes on every input, native `date`, `time` and `datetime-local` inputs that fill Magento's hidden day, month, year, hour, minute and day-part fields, and a mobile breakpoint at 640px.
- Luma template set: a styled container that initialises Magento's standard `priceOptions` widget, the standard `remaining-characters` counter for options with a maximum length, radio and checkbox values wrapped into card rows by the module's `card-options.js`, and file, date and time controls rendered by Magento's own option blocks inside the styled wrapper.
- Required options are marked with an asterisk; text inputs get a placeholder built from the option title.
- File options display the allowed extensions and the maximum image width and height configured on the option, and show the already uploaded file with a "Change" link (Hyva) or "Change" and "Delete" controls (Luma) when a cart item is edited.
- Colours, border radius and font of the Hyva template set are CSS custom properties (`--custom-options-*`). Their defaults ship in `etc/theme-config.json` and are printed into `:root` on every frontend page by `Panth_Core`; a theme can override them in its own `web/tailwind/theme-config.json` or in CSS.
- One admin setting that switches both template sets on or off; when it is off the active theme's own option templates render.
- No database tables, cron jobs, plugins, observers, controllers, routes or web API endpoints.

## Compatibility

| Requirement | Supported |
|---|---|
| Magento Open Source / Adobe Commerce | 2.4.4 to 2.4.8 (as published on the product page) |
| PHP | 8.1, 8.2, 8.3, 8.4 (`~8.1.0\|\|~8.2.0\|\|~8.3.0\|\|~8.4.0` in composer.json) |
| Themes | Hyva and Luma (separate template sets, see Usage) |

Composer constraints on Magento packages: `magento/framework` ^103.0, `magento/module-catalog` ^104.0, `magento/module-config` ^101.2, `magento/module-store` ^101.1.

## Requirements

- Magento Open Source or Adobe Commerce 2.4.4 to 2.4.8.
- PHP 8.1, 8.2, 8.3 or 8.4.
- `mage2kishan/module-core` ^1.0 (Magento module `Panth_Core`), required in composer.json and listed in the module sequence. It provides the "Panth Extensions" configuration tab and the `Panth\Core\ViewModel\ThemeConfig` view model that turns `etc/theme-config.json` into CSS variables.
- For the Hyva template set: a Hyva theme, because those templates use `Hyva\Theme\Model\ViewModelRegistry`, `Hyva\Theme\ViewModel\ProductPrice` and `Hyva\Theme\ViewModel\CustomOption`. Hyva is not declared as a Composer dependency.
- composer.json has no `suggest` section.

## Installation

```bash
composer require mage2kishan/module-custom-options
bin/magento module:enable Panth_Core Panth_CustomOptions
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
bin/magento cache:flush
```

- `setup:di:compile` is only needed in production mode; in developer mode Magento compiles on demand.
- `setup:static-content:deploy -f` is needed because the module ships `view/frontend/web/js/card-options.js`, `view/frontend/web/js/date-labels.js` and `view/frontend/requirejs-config.js`, which the Luma template set loads through RequireJS.

Check the result:

```bash
bin/magento module:status Panth_CustomOptions
```

## Configuration

Admin path: Stores > Configuration > Panth Extensions > Custom Options. The section is available at default, website and store view scope and is protected by the ACL resource `Panth_CustomOptions::config`.

### General Settings

| Setting | Default | What it does |
|---|---|---|
| Enable Custom Options Styling | Yes | Controls whether the module assigns its templates. The layout files set every option template through `<action method="setTemplate" ifconfig="panth_customoptions/general/enabled">`, so with Yes the Hyva set renders on Hyva themes and the Luma set on Luma, and with No the blocks keep the templates of the active theme (for a Hyva child theme, its `Magento_Catalog` copies; for Luma, Magento's own). The value is read at store view scope, so website and store view overrides apply. |

Config path: `panth_customoptions/general/enabled` (default `1` in `etc/config.xml`).

There is no admin grid, admin form or product-form addition. Visual defaults are not admin settings; they are the keys of `etc/theme-config.json`:

| Key | Default | CSS variable |
|---|---|---|
| custom-options-primary | #0D9488 | `--custom-options-primary` |
| custom-options-border | #E5E7EB | `--custom-options-border` |
| custom-options-border-focus | #0D9488 | `--custom-options-border-focus` |
| custom-options-bg | #FFFFFF | `--custom-options-bg` |
| custom-options-input-bg | #F9FAFB | `--custom-options-input-bg` |
| custom-options-text | #171717 | `--custom-options-text` |
| custom-options-text-muted | #6B7280 | `--custom-options-text-muted` |
| custom-options-label | #374151 | `--custom-options-label` |
| custom-options-price | #0D9488 | `--custom-options-price` |
| custom-options-required | #EF4444 | `--custom-options-required` |
| custom-options-radius | 10px | `--custom-options-radius` |
| custom-options-font | 'DM Sans', sans-serif | `--custom-options-font` |

These variables are consumed by the Hyva template set. The Luma template set defines its own `--pco-*` variables inline in `luma/options.phtml` and does not read `theme-config.json`.

## Usage

### Which templates render

Two layout files assign the module templates to the existing `Magento_Catalog` option blocks, each through an `ifconfig` action on `panth_customoptions/general/enabled`. `catalog_product_view.xml` assigns the Luma set:

| Block | Template |
|---|---|
| product.info.options | Panth_CustomOptions::product/view/options/luma/options.phtml |
| product.info.options.text | Panth_CustomOptions::product/view/options/luma/type/text.phtml |
| product.info.options.select | Panth_CustomOptions::product/view/options/luma/type/select.phtml |
| product.info.options.file | Panth_CustomOptions::product/view/options/luma/type/file.phtml |
| product.info.options.date | Panth_CustomOptions::product/view/options/luma/type/date.phtml |

`hyva_catalog_product_view.xml` is only loaded on Hyva themes and runs after every `catalog_product_view` file, including the theme's, so on Hyva it replaces those assignments with the Hyva set (and loads `css/hyva-options.css`):

| Block | Template |
|---|---|
| product.info.options | Panth_CustomOptions::product/view/options/options.phtml |
| product.info.options.text | Panth_CustomOptions::product/view/options/type/text.phtml |
| product.info.options.select | Panth_CustomOptions::product/view/options/type/select.phtml |
| product.info.options.file | Panth_CustomOptions::product/view/options/type/file.phtml |
| product.info.options.date | Panth_CustomOptions::product/view/options/type/date-html5.phtml |

The Hyva set therefore works on any Hyva theme, not only on themes that ship their own copies of these templates. The options wrapper block keeps the theme's `wrapper.phtml`.

The templates apply wherever the `catalog_product_view` layout handle is used, which includes the product page and the page that opens when a cart item is edited. Nothing changes in the cart or checkout; option values are submitted with the same field names Magento expects, so cart and order display are Magento's own.

### Option types

| Magento option type | Hyva template set | Luma template set |
|---|---|---|
| Field | Text input with `maxlength` and a "Maximum N characters" note | Text input with Magento's remaining-characters counter |
| Area | Textarea, 4 rows, same length note | Textarea, 4 rows, same counter |
| File | Drop-zone style area with an upload icon and "Drag & drop or browse" text; after a file is chosen, a preview row with an image thumbnail (or file icon), the file name and size, and a 44px "Remove file" button that clears the input and returns focus to it; extension and size hints below | Drop-zone style area showing the chosen file name; "Allowed files", "Max width" and "Max height" notes |
| Drop-down, Multiple Select | Rendered by `Hyva\Theme\ViewModel\CustomOption::getOptionHtml()` inside the styled row | Magento's `getValuesHtml()` output; selects get the `panth-input` class from `card-options.js` |
| Radio Buttons, Checkbox | Rendered by `Hyva\Theme\ViewModel\CustomOption::getOptionHtml()` inside a `radiogroup` or `group` labelled by the option title; each choice row is at least 44px high | Each `.field.choice` is rewrapped into a `.panth-choice-card` label by `card-options.js` |
| Date, Date & Time, Time | Native `date`, `datetime-local` or `time` input that fills Magento's hidden date and time fields on change | Magento's own `getDateHtml()` and `getTimeHtml()` output inside a fieldset labelled by the option title; `date-labels.js` gives each select an accessible name (Month, Day, Year, Hour, Minute, AM/PM) |

### Overriding templates

All templates are ordinary `.phtml` files and can be overridden in a theme under `app/design/frontend/<Vendor>/<theme>/Panth_CustomOptions/templates/product/view/options/`.

## Developer Notes

- Module name: `Panth_CustomOptions`. Composer package: `mage2kishan/module-custom-options`. PHP namespace: `Panth\CustomOptions` (the module contains no PHP classes besides `registration.php`).
- Module sequence: `Magento_Catalog`, `Panth_Core`.
- `etc/frontend/di.xml` adds `Panth_CustomOptions` to the `registeredModules` argument of `Panth\Core\ViewModel\ThemeConfig`, which is how `etc/theme-config.json` is picked up.
- `view/frontend/web/js/card-options.js` (RequireJS module `Panth_CustomOptions/js/card-options`, initialised from `luma/type/select.phtml` for radio and checkbox options) clones each radio or checkbox input into a `.panth-choice-card` label, moves the text of Magento's `.price-notice` element (the formatted option price, in any currency) into a `.panth-choice-price` span, and adds the `panth-input` class to `select.product-custom-option` elements. Titles and prices are inserted as text, not HTML.
- `view/frontend/web/js/date-labels.js` (RequireJS module `Panth_CustomOptions/js/date-labels`, initialised from `luma/type/date.phtml`) sets a translatable `aria-label` on each date and time select from the last part of its field name (`month`, `day`, `year`, `hour`, `minute`, `day_part`).
- CSS hooks: `.panth-custom-options`, `.panth-opt-row`, `.panth-opt-label`, `.panth-opt-price`, `.panth-choice-row`, `.panth-file-zone` (Hyva set); `.panth-options-container`, `.panth-option-group`, `.panth-option-label`, `.panth-price-badge`, `.panth-choice-card`, `.panth-file-zone`, `.panth-input` (Luma set).
- ACL resource: `Panth_CustomOptions::config` ("Panth Custom Options") under `Magento_Config::config`.
- Database: no `db_schema.xml`, no tables.

## Uninstallation

```bash
bin/magento module:disable Panth_CustomOptions
composer remove mage2kishan/module-custom-options
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

The module creates no tables. A saved value of `panth_customoptions/general/enabled` remains in `core_config_data`. `Panth_Core` (`mage2kishan/module-core`) is left installed; remove it separately if no other Panth module needs it.

## Support

- Product page: [Custom Product Options for Magento 2 (Hyva + Luma)](https://kishansavaliya.com/magento-2-custom-options.html)
- Contact form: [kishansavaliya.com/contact](https://kishansavaliya.com/contact)
- Email: kishansavaliyakb@gmail.com
- Bug reports: [GitHub issues](https://github.com/mage2sk/module-custom-options/issues)

## Documentation

[USER_GUIDE.md](USER_GUIDE.md) covers installation, checking that the module is active, the admin setting, the CSS custom properties, the supported option types and a troubleshooting table.

## License

Commercial software license. See [LICENSE.txt](LICENSE.txt) in this repository.

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## Links

- Website: [kishansavaliya.com](https://kishansavaliya.com)
- All extensions: [Magento extensions catalogue](https://kishansavaliya.com/magento-extensions.html)
- GitHub: [mage2sk/module-custom-options](https://github.com/mage2sk/module-custom-options)
- Packagist: [mage2kishan/module-custom-options](https://packagist.org/packages/mage2kishan/module-custom-options)
