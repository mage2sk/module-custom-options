# Panth Custom Options -- User Guide

Panth_CustomOptions replaces the default Magento product custom-options
rendering with a modern, beautifully styled UI designed specifically for
Hyva-based storefronts.

---

## Table of contents

1. [Installation](#1-installation)
2. [Verifying the module is active](#2-verifying-the-module-is-active)
3. [Configuration](#3-configuration)
4. [Theming with CSS custom properties](#4-theming-with-css-custom-properties)
5. [Supported option types](#5-supported-option-types)
6. [Troubleshooting](#6-troubleshooting)

---

## 1. Installation

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

> `Panth_Core` is a required dependency and will be pulled in
> automatically by Composer.

---

## 2. Verifying the module is active

```bash
bin/magento module:status Panth_CustomOptions
# Module is enabled
```

Visit any product page that has custom options configured -- you should
see the new styled UI.

---

## 3. Configuration

Navigate to **Stores -> Configuration -> Panth Extensions ->
Custom Options**.

| Setting | Default | What it does |
|---|---|---|
| **Enable Custom Options Styling** | Yes | Toggles the styled templates. When disabled, Magento uses its default custom-options rendering. |

---

## 4. Theming with CSS custom properties

Every visual aspect of the custom options UI is driven by CSS custom
properties. You can override them in your child theme's stylesheet or
via the module's `theme-config.json`.

| Variable | Default | Controls |
|---|---|---|
| `--custom-options-primary` | `#0D9488` | Primary accent (badges, focus rings, checked states) |
| `--custom-options-border` | `#E5E7EB` | Default border colour |
| `--custom-options-border-focus` | `#0D9488` | Border colour on focus |
| `--custom-options-bg` | `#FFFFFF` | Container background |
| `--custom-options-input-bg` | `#F9FAFB` | Input background |
| `--custom-options-text` | `#171717` | Primary text colour |
| `--custom-options-text-muted` | `#6B7280` | Secondary / muted text |
| `--custom-options-label` | `#374151` | Label text colour |
| `--custom-options-price` | `#0D9488` | Price badge colour |
| `--custom-options-required` | `#EF4444` | Required asterisk colour |
| `--custom-options-radius` | `10px` | Border radius for inputs |
| `--custom-options-font` | `'DM Sans', sans-serif` | Font family |

---

## 5. Supported option types

| Magento option type | Rendering |
|---|---|
| Text Field | Styled text input with character counter |
| Text Area | Styled textarea with character counter |
| File Upload | Drag-and-drop upload zone with file-type and size hints |
| Drop-down | Styled select dropdown |
| Radio Buttons | Card-style rows with custom radio indicators |
| Checkboxes | Card-style rows with custom checkbox indicators |
| Multiple Select | Styled multi-select dropdown |
| Date | Native HTML5 date picker |
| Time | Native HTML5 time picker |
| Date and Time | Native HTML5 datetime-local picker |

All option types support dynamic price display (incl. tax and excl. tax)
and required-field validation.

---

## 6. Troubleshooting

| Symptom | Cause | Fix |
|---|---|---|
| Custom options look unstyled | Module disabled or cache stale | Check `Stores -> Configuration -> Panth Extensions -> Custom Options -> Enable`; flush cache |
| Prices do not update dynamically | JavaScript error | Open browser console; ensure Alpine.js is loaded (Hyva requirement) |
| Date picker not appearing | Browser does not support HTML5 date inputs | Use a modern browser (Chrome, Firefox, Safari, Edge) |
| "Class Panth\Core\ViewModel\ThemeConfig does not exist" | `Panth_Core` not installed | `composer require mage2kishan/module-core && bin/magento setup:upgrade` |

---

## Support

For all questions, bug reports, or feature requests:

- **Email:** kishansavaliyakb@gmail.com
- **Website:** https://kishansavaliya.com
- **WhatsApp:** +91 84012 70422

Free email support is provided on a best-effort basis.
