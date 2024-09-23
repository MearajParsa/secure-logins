<?php

// افزودن لینک "Settings" به صفحه پلاگین‌ها
add_filter('plugin_action_links_' . SECURE_LOGINS_BASENAME, 'sl_plugin_action_links');
if (!function_exists('sl_plugin_action_links')) {
    function sl_plugin_action_links( $links ) {
        array_unshift($links, '<a href="' . admin_url( 'options-general.php?page=sl_settings' ) . '">' . __('Settings','secure-logins') . '</a>');

        return $links;
    }
}

// افزودن منو به داشبورد وردپرس
add_action('admin_menu', 'secure_logins_menu_page');
if (!function_exists('secure_logins_menu_page')) {
    function secure_logins_menu_page() {
        $title = __('Secure Login','secure-logins');

        // ایجاد صفحه تنظیمات مخصوص پلاگین
        add_options_page($title, $title, 'manage_options', 'sl_settings', 'sl_settings_page');
    }
}

// تابع نمایش صفحه تنظیمات پلاگین
if (!function_exists('sl_settings_page')) {
    function sl_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Secure Logins Settings','secure-logins'); ?></h1>
            <form method="post" action="options.php">
                <?php
                // بارگذاری تنظیمات ثبت‌شده
                settings_fields('sl_settings_group'); // گروه تنظیمات
                do_settings_sections('sl_settings_page'); // بخش‌های تنظیمات
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}

// تابعی برای ثبت تنظیمات و بخش‌های تنظیمات
add_action('admin_init', 'sl_admin_init');
if (!function_exists('sl_admin_init')) {
    function sl_admin_init() {
        // ثبت تنظیمات
        register_setting('sl_settings_group', 'sl_page', 'sanitize_title_with_dashes');
        register_setting('sl_settings_group', 'sl_redirect_admin', 'sanitize_title_with_dashes');

        // بخش تنظیمات
        add_settings_section(
            'secure-loginssection', // شناسه بخش
            null, // عنوان بخش
            'sl_section_desc', // توضیحات بخش
            'sl_settings_page' // صفحه تنظیمات پلاگین
        );

        // فیلد تنظیمات برای URL ورود
        add_settings_field(
            'sl_page', 
            __('Login URL','secure-logins'), 
            'sl_page_input', 
            'sl_settings_page', // صفحه تنظیمات پلاگین
            'secure-loginssection' // بخش تنظیمات
        );

        // فیلد تنظیمات برای URL ریدایرکت
        add_settings_field(
            'sl_redirect_admin', 
            __('Redirection URL','secure-logins'), 
            'sl_redirect_admin_input', 
            'sl_settings_page', // صفحه تنظیمات پلاگین
            'secure-loginssection' // بخش تنظیمات
        );
    }
}

// توضیحات بخش تنظیمات
if (!function_exists('sl_section_desc')) {
    function sl_section_desc() {
        _e('Configure the login and redirection URLs for your secure login system.','secure-logins');
    }
}

// فیلد ورود URL ورود
if (!function_exists('sl_page_input')) {
    function sl_page_input() {
        if (get_option('permalink_structure')) {
            _e('<code>' . trailingslashit(home_url()) . '</code> <input id="sl_page" type="text" name="sl_page" value="' . esc_attr(get_option('sl_page')) . '">' . (sl_use_trailing_slashes() ? ' <code>/</code>' : ''),'secure-logins');
        } else {
            _e('<code>' . trailingslashit(home_url()) . '?</code> <input id="sl_page" type="text" name="sl_page" value="' . esc_attr(get_option('sl_page')) . '">','secure-logins');
        }
        _e('<p class="description">' . __('Change the login URL to protect your site.','secure-logins') . '</p>','secure-logins');
    }
}

// فیلد ورود URL ریدایرکت
if (!function_exists('sl_redirect_admin_input')) {
    function sl_redirect_admin_input() {
        if (get_option('permalink_structure')) {
            _e('<code>' . trailingslashit(home_url()) . '</code> <input id="sl_redirect_admin" type="text" name="sl_redirect_admin" value="' . esc_attr(get_option('sl_redirect_admin')) . '">' . (sl_use_trailing_slashes() ? ' <code>/</code>' : ''),'secure-logins');
        } else {
            _e('<code>' . trailingslashit(home_url()) . '?</code> <input id="sl_redirect_admin" type="text" name="sl_redirect_admin" value="' . esc_attr(get_option('sl_redirect_admin')) . '">','secure-logins');
        }
        _e('<p class="description">' . __('The URL to redirect when accessing wp-admin while not logged in.','secure-logins') . '</p>','secure-logins');
    }
}

// نمایش پیغام برای لینک جدید ورود
add_action('admin_notices', 'sl_admin_notices');
if (!function_exists('sl_admin_notices')) {
    function sl_admin_notices() {
        global $pagenow;

        if (!is_network_admin() && $pagenow === 'options-general.php' && isset($_GET['settings-updated']) && !isset($_GET['page'])) {
            _e('<div class="updated notice is-dismissible"><p>' . sprintf(__('Your login page is now here: <strong><a href="%1$s">%2$s</a></strong>. Bookmark this page!','secure-logins'), sl_new_login_url(), sl_new_login_url()) . '</p></div>','secure-logins');
        }
    }
}

// تابعی برای ساخت URL جدید لاگین
if (!function_exists('sl_new_login_url')) {
    function sl_new_login_url() {
        $slug = get_option('sl_page', 'login');
        return home_url($slug);
    }
}

// تابعی برای مدیریت اضافه کردن اسلش در URL ها
if (!function_exists('sl_use_trailing_slashes')) {
    function sl_use_trailing_slashes() {
        return substr(get_option('permalink_structure'), -1) === '/';
    }
}

// تابعی برای دریافت slug جدید صفحه ورود
if (!function_exists('sl_new_login_slug')) {
    function sl_new_login_slug() {
        return get_option('sl_page', 'login');
    }
}

// تابعی برای دریافت slug جدید ریدایرکت
if (!function_exists('sl_new_redirect_slug')) {
    function sl_new_redirect_slug() {
        return get_option('sl_redirect_admin', 'home');
    }
}
