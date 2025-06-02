<?php
/*
Plugin Name: WP Prometheus Exporter
Plugin URI: https://github.com/cloudmentor/wp-prometheus-exporter
Description: WordPress Prometheus metrics exporter with configurable metrics and admin UI
Version: 1.5.1
Author: CloudMentor
Requires at least: 6.7.2
Requires PHP: 8.1
*/

require_once plugin_dir_path(__FILE__) . 'admin-page.php';

function wp_prometheus_get_option($key, $default = true)
{
    return get_option("wp_prometheus_{$key}", $default);
}

add_action('init', function () {
    if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] === '/v1/metrics') {
        header('Content-Type: text/plain');
        global $wpdb;

        if (wp_prometheus_get_option('post_count')) {
            $post_types = get_post_types(['public' => true], 'names');
            foreach ($post_types as $type) {
                $count = wp_count_posts($type)->publish ?? 0;
                echo "# HELP wp_post_count Number of published posts for post type {$type}\n";
                echo "# TYPE wp_post_count gauge\n";
                echo "wp_post_count{post_type=\"{$type}\"} {$count}\n";
            }
        }

        if (wp_prometheus_get_option('user_count')) {
            $users = get_users();
            $role_counts = [];
            foreach ($users as $user) {
                foreach ($user->roles as $role) {
                    $role_counts[$role] = ($role_counts[$role] ?? 0) + 1;
                }
            }
            foreach ($role_counts as $role => $count) {
                echo "# HELP wp_user_count Number of users with role {$role}\n";
                echo "# TYPE wp_user_count gauge\n";
                echo "wp_user_count{role=\"{$role}\"} {$count}\n";
            }
        }

        if (wp_prometheus_get_option('comments')) {
            $comments = wp_count_comments();
            echo "# HELP wp_approved_comments Number of approved comments\n";
            echo "# TYPE wp_approved_comments gauge\n";
            echo "wp_approved_comments {$comments->approved}\n";
        }

        if (wp_prometheus_get_option('options')) {
            $options_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->options}");
            echo "# HELP wp_options_count Number of options in wp_options\n";
            echo "# TYPE wp_options_count gauge\n";
            echo "wp_options_count {$options_count}\n";
        }

        if (wp_prometheus_get_option('plugin_theme_counts')) {
            echo "# HELP wp_plugin_total Total installed plugins\n";
            echo "# TYPE wp_plugin_total gauge\n";
            echo "wp_plugin_total " . count(get_plugins()) . "\n";
            echo "# HELP wp_theme_total Total installed themes\n";
            echo "# TYPE wp_theme_total gauge\n";
            echo "wp_theme_total " . count(wp_get_themes()) . "\n";
        }

        if (wp_prometheus_get_option('updates')) {
            $plugin_updates = function_exists('get_plugin_updates') ? get_plugin_updates() : [];
            $theme_updates = function_exists('get_theme_updates') ? get_theme_updates() : [];
            echo "# HELP wp_plugin_updates Plugin updates available\n";
            echo "# TYPE wp_plugin_updates gauge\n";
            echo "wp_plugin_updates " . count($plugin_updates) . "\n";
            echo "# HELP wp_theme_updates Theme updates available\n";
            echo "# TYPE wp_theme_updates gauge\n";
            echo "wp_theme_updates " . count($theme_updates) . "\n";
        }

        if (wp_prometheus_get_option('emails_sent')) {
            $emails = get_transient('wp_prometheus_sent_emails') ?: 0;
            echo "# HELP wp_emails_sent Number of emails sent\n";
            echo "# TYPE wp_emails_sent counter\n";
            echo "wp_emails_sent {$emails}\n";
        }

        if (wp_prometheus_get_option('php_errors')) {
            $errors = get_transient('wp_prometheus_error_count') ?: 0;
            echo "# HELP wp_php_errors Number of PHP errors thrown\n";
            echo "# TYPE wp_php_errors counter\n";
            echo "wp_php_errors {$errors}\n";
        }

        if (wp_prometheus_get_option('db_queries')) {
            $query_count = 0;
            $query_time = 0;
            if (defined('SAVEQUERIES') && SAVEQUERIES && isset($wpdb->queries)) {
                $query_count = count($wpdb->queries);
                $query_time = array_sum(array_column($wpdb->queries, 1));
            }
            echo "# HELP wp_db_query_count Number of database queries\n";
            echo "# TYPE wp_db_query_count gauge\n";
            echo "wp_db_query_count {$query_count}\n";
            echo "# HELP wp_db_query_time Total time of database queries\n";
            echo "# TYPE wp_db_query_time gauge\n";
            echo "wp_db_query_time {$query_time}\n";
        }

        exit;
    }
}, 0);

add_action('wp_mail', function () {
    $c = get_transient('wp_prometheus_sent_emails') ?: 0;
    set_transient('wp_prometheus_sent_emails', $c + 1, 12 * HOUR_IN_SECONDS);
});

set_error_handler(function () {
    $c = get_transient('wp_prometheus_error_count') ?: 0;
    set_transient('wp_prometheus_error_count', $c + 1, 12 * HOUR_IN_SECONDS);
});
