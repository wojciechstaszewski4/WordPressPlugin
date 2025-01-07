# 🎸 WordPress Plugin - Guitar Manager

**Guitar Manager** is a WordPress plugin designed to streamline the management of guitar inventories in an online store. It provides a custom admin interface, meta boxes for detailed guitar information, and shortcodes to display guitar data on the front-end. Whether you manage a music shop or an online inventory, this plugin simplifies the process.

---

## 🌟 Features

- **Custom Admin Menu**: Manage guitar details directly from the WordPress admin dashboard.
- **Meta Boxes**: Add additional information about guitars (e.g., name, weight, and quantity).
- **Shortcodes**: Display guitar data dynamically on pages or posts using `[guitar_manager]`.
- **Custom Fields**:
  - Text fields (e.g., guitar name).
  - Number fields (e.g., weight, quantity).
  - Radio buttons (e.g., availability status).
- **Database Management**:
  - Creates custom tables on activation.
  - Cleans up database tables on deactivation.
- **Dynamic Display**: Supports different views such as list or table via shortcode parameters.

---

## 🛠️ Installation

1. Download the `guitar-manager.php` file.
2. Upload the file to the `/wp-content/plugins/` directory on your WordPress installation.
3. Go to the WordPress **Plugins** page and activate the plugin.

---

## 🚀 Usage

### Admin Panel
- Navigate to the **Guitar Manager** menu in the WordPress admin dashboard.
- Add, edit, or delete guitars and their details.

### Shortcodes
Use the `[guitar_manager]` shortcode to display the guitar list on any page or post. Example:

```html
[guitar_manager type="table"]
