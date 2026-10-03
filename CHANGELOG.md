# Changelog

All notable changes to this extension are documented here. The format
is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/).

## [1.0.11] - 2026-10-03

### Fixed
- The Hyva template set is now assigned by the module's `hyva_catalog_product_view.xml`, so custom options use the module templates on any Hyva theme instead of depending on copies inside the theme; the unused `wrapper.phtml` and duplicate `type/date.phtml` were removed.
- "Enable Custom Options Styling" now switches both template sets through `ifconfig`; when it is off the active theme's own option templates render, and the Luma container no longer reads configuration through the object manager.
- Hyva file option: a preview row with thumbnail, file name and size, and a 44px "Remove file" button that clears the file, updates the price and returns focus to the input.
- Hyva and Luma radio and checkbox options are exposed as a labelled `radiogroup` or `group` instead of a label pointing at no control; Hyva choice rows are at least 44px high.
- Luma date and time options: the fieldset is labelled by the option title and each select has an accessible name (Month, Day, Year, Hour, Minute, AM/PM).
- Luma multiple select no longer shows a drop-down arrow, and the file drop zone uses an icon instead of an emoji.
- Hyva single selects get a consistent drop-down arrow.
- The configuration field explains what the setting switches.
