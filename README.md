# Scripts & Styles Lite Tweaks

Performance optimization WordPress plugin for selectively disabling unnecessary scripts and styles.

## Description

Scripts & Styles Lite Tweaks helps improve your WordPress site's performance by allowing you to disable unnecessary scripts and styles that WordPress loads by default. This plugin is designed with security and best practices in mind, following WordPress coding standards.

## Features

- **Disable jQuery Migrate** - Removes jQuery Migrate compatibility script for older code
- **Disable Emoji Scripts** - Removes WordPress emoji detection scripts (modern browsers support emojis natively)
- **Disable WordPress Embeds** - Removes automatic embedding of external content scripts
- **Disable Admin Bar Scripts (Frontend)** - Removes admin bar scripts from frontend for non-logged users
- **Disable Dashicons** - Removes WordPress admin icons from frontend for non-logged users
- **Enable Selective Block Loading** - Only loads CSS styles for Gutenberg blocks that are actually present on each page
- **Disable Global Styles** - Removes WordPress's default CSS for block editor global styles
- **Disable Classic Theme Styles** - Removes backward compatibility CSS for classic themes
- **Disable Recent Comments Style** - Removes default styling for recent comments widget
- **Cleanup on Uninstall** - Option to remove all plugin data when uninstalling

## Installation

1. Upload the `scripts-styles-lite-tweaks` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **Tools > Scripts & Styles** to configure settings

## Usage

1. Navigate to **Tools > Scripts & Styles** in your WordPress admin
2. Check the boxes for the features you want to enable
3. Click "Save Changes"

**Important Notes:**

- **jQuery Migrate**: Only disable if you're sure your theme and plugins don't need it. Some older themes/plugins may break without it.
- **Selective Block Loading**: Only works on singular posts and pages. Analyzes content and loads only necessary block styles.
- **Test thoroughly**: After enabling options, test your site to ensure everything works correctly.

## Security Features

- All database queries use prepared statements to prevent SQL injection
- Nonce verification for all form submissions
- Capability checks for admin access
- Input sanitization and output escaping
- Custom database table for settings (doesn't bloat wp_options)

## Performance Benefits

This plugin can significantly improve your site's performance by:

- Reducing HTTP requests
- Decreasing page size
- Improving load times
- Better Core Web Vitals scores

## File Structure

```
scripts-styles-lite-tweaks/
├── scripts-styles-lite-tweaks.php  # Main plugin file
├── README.md                       # This file
├── uninstall.php                   # Cleanup on uninstall
├── index.php                       # Security stub
├── assets/
│   ├── admin.css                   # Admin styles
│   ├── admin.js                    # Admin scripts
│   └── index.php                   # Security stub
└── includes/
    ├── class-database.php          # Database operations
    ├── class-admin.php             # Admin interface
    ├── class-core.php              # Core functionality
    └── index.php                   # Security stub
```

## System Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- MySQL 5.6+ or MariaDB 10.0+

## Frequently Asked Questions

**Q: Will this break my site?**

A: The plugin is designed to be safe, but some features may affect certain themes or plugins. Always test on a staging site first.

**Q: Can I undo the changes?**

A: Yes, simply uncheck the options and save. All changes are reversible.

**Q: Does this plugin delete data on uninstall?**

A: Only if you enable the "Cleanup on Uninstall" option. Otherwise, your settings are preserved.

**Q: Will this work with caching plugins?**

A: Yes, but you may need to clear your cache after changing settings to see the effects.

## Changelog

### 1.0.0
- Initial release
- 9 optimization options
- Custom database table for settings
- Admin interface under Tools menu
- Uninstall cleanup option

## License

This plugin is released under the GPL v2 or later license.
