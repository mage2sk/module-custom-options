# Panth Custom Options

[![Magento 2.4.4 - 2.4.8](https://img.shields.io/badge/Magento-2.4.4%20--%202.4.8-orange)]()
[![PHP 8.1 - 8.4](https://img.shields.io/badge/PHP-8.1%20--%208.4-blue)]()
[![Hyva Theme](https://img.shields.io/badge/Hyva-Compatible-teal)]()

Beautifully styled **product custom options** for Hyva-based Magento 2
storefronts. Replaces the default custom-options rendering with a
modern, accessible UI featuring styled text inputs, textareas, file
upload drop-zones, select / radio / checkbox controls, and native HTML5
date & time pickers -- all governed by CSS custom properties for easy
theming.

---

## Features

- **Modern input styling** -- text fields, textareas, selects, radios,
  checkboxes, and file-upload zones all receive a clean, consistent
  look that matches Hyva storefronts out of the box.
- **Native HTML5 date/time pickers** -- no external JS date-picker
  libraries; uses the browser's built-in `<input type="date">`,
  `<input type="time">`, and `<input type="datetime-local">`.
- **CSS-variable theming** -- every colour, border radius, and font is
  controlled via CSS custom properties. Override them in your child
  theme or via the `theme-config.json` file to match your brand.
- **Accessible** -- proper `<label>` associations, focus rings, and
  keyboard navigation.
- **Lightweight** -- zero external JS dependencies; all logic runs via
  Alpine.js (already bundled with Hyva).
- **Admin toggle** -- enable or disable the styled options from
  `Stores -> Configuration -> Panth Extensions -> Custom Options`.

---

## Installation

### Composer (recommended)

```bash
composer require mage2kishan/module-custom-options
bin/magento module:enable Panth_CustomOptions
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
bin/magento cache:flush
```

### Manual zip

1. Download the extension package zip from the Marketplace
2. Extract to `app/code/Panth/CustomOptions`
3. Run the same `module:enable ... cache:flush` commands above

> **Note:** `Panth_Core` is a required dependency and will be pulled in
> automatically by Composer.

---

## Requirements

| | Required |
|---|---|
| Magento | 2.4.4 -- 2.4.8 (Open Source / Commerce / Cloud) |
| PHP | 8.1 / 8.2 / 8.3 / 8.4 |
| Hyva Theme | 1.1+ |
| Panth_Core | ^1.0 |

---

## Configuration

Open **Stores -> Configuration -> Panth Extensions -> Custom Options**.

| Setting | Default | What it does |
|---|---|---|
| Enable Custom Options Styling | Yes | Toggles the styled custom-options templates on or off. When disabled, Magento falls back to its default templates. |

### Theming via CSS custom properties

The module ships a `theme-config.json` that defines the following
variables (shown with their defaults):

| Variable | Default |
|---|---|
| `--custom-options-primary` | `#0D9488` |
| `--custom-options-border` | `#E5E7EB` |
| `--custom-options-border-focus` | `#0D9488` |
| `--custom-options-bg` | `#FFFFFF` |
| `--custom-options-input-bg` | `#F9FAFB` |
| `--custom-options-text` | `#171717` |
| `--custom-options-text-muted` | `#6B7280` |
| `--custom-options-label` | `#374151` |
| `--custom-options-price` | `#0D9488` |
| `--custom-options-required` | `#EF4444` |
| `--custom-options-radius` | `10px` |
| `--custom-options-font` | `'DM Sans', sans-serif` |

Override any of these in your child theme's CSS to match your brand.

---

## Support

| Channel | Contact |
|---|---|
| Email | kishansavaliyakb@gmail.com |
| Website | https://kishansavaliya.com |
| WhatsApp | +91 84012 70422 |

---

## License

Proprietary -- see `LICENSE.txt`. Distribution is restricted to the
Adobe Commerce Marketplace.

---

## About the developer

Built and maintained by **Kishan Savaliya** -- https://kishansavaliya.com.
Builds high-quality, security-focused Magento 2 extensions and themes
for both Hyva and Luma storefronts.
