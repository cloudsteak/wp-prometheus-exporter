<?php
add_action('admin_menu', function () {
    add_options_page('Prometheus Exporter', 'Prometheus Exporter', 'manage_options', 'wp-prometheus-exporter', 'wp_prometheus_exporter_settings_page');
});

add_action('admin_init', function () {
    $fields = ['post_count', 'user_count', 'comments', 'options', 'plugin_theme_counts', 'updates', 'emails_sent', 'php_errors', 'db_queries'];
    foreach ($fields as $field) {
        register_setting('wp_prometheus_exporter', "wp_prometheus_{$field}");
    }
});

function wp_prometheus_exporter_settings_page()
{
    ?>
    <div class="wrap">

        <h1>WP Prometheus Exporter <small style='font-weight:normal;'>
                <?php
                if (!function_exists('get_plugin_data')) {
                    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
                }
                $plugin_file = plugin_dir_path(__FILE__) . 'wp-prometheus-exporter.php';
                echo esc_html(get_plugin_data($plugin_file)['Version']);
                ?>
            </small></h1>


        <p><strong>Készítette:</strong> <a href='https://cloudmentor.hu' target='_blank'>Cloud Mentor</a></p>
        <p>Ez a plugin lehetővé teszi, hogy a <strong>WordPress</strong> oldalad statisztikai és teljesítmény adatait
            <strong>Prometheus</strong> kompatibilis formátumban exportáld. A metrikák vizualizálhatók
            <strong>Grafana</strong> segítségével, így valós idejű betekintést kapsz a weboldalad működésébe.
        </p>

        <form method="post" action="options.php">
            <?php settings_fields('wp_prometheus_exporter'); ?>
            <?php do_settings_sections('wp_prometheus_exporter'); ?>

            <style>
                .switch {
                    position: relative;
                    display: inline-block;
                    width: 50px;
                    height: 24px;
                }

                .switch input {
                    opacity: 0;
                    width: 0;
                    height: 0;
                }

                .slider {
                    position: absolute;
                    cursor: pointer;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-color: #ccc;
                    transition: .4s;
                    border-radius: 24px;
                }

                .slider:before {
                    position: absolute;
                    content: "";
                    height: 18px;
                    width: 18px;
                    left: 3px;
                    bottom: 3px;
                    background-color: white;
                    transition: .4s;
                    border-radius: 50%;
                }

                input:checked+.slider {
                    background-color: #2196F3;
                }

                input:checked+.slider:before {
                    transform: translateX(26px);
                }
            </style>

            <table class="form-table">
                <tr>
                    <th colspan="2">
                        <h2>Metrikák engedélyezése</h2>
                    </th>
                </tr>
                <?php
                $fields = [
                    'post_count' => 'Posztok száma',
                    'user_count' => 'Felhasználók száma',
                    'comments' => 'Hozzászólások',
                    'options' => 'Opciók száma',
                    'plugin_theme_counts' => 'Plugin / sablon száma',
                    'updates' => 'Frissítések',
                    'emails_sent' => 'Elküldött e-mailek',
                    'php_errors' => 'PHP hibák',
                    'db_queries' => 'Adatbázis lekérdezések'
                ];
                foreach ($fields as $key => $label) {
                    $val = get_option("wp_prometheus_{$key}", true);
                    echo "<tr><th scope='row'>{$label}</th><td>
                          <label class='switch'><input type='checkbox' name='wp_prometheus_{$key}' value='1' " . checked(1, $val, false) . "><span class='slider'></span></label>
                          </td></tr>";
                }
                ?>
                <tr>
                    <th>Savequeries aktív?</th>
                    <td>
                        <?php if (defined('SAVEQUERIES') && SAVEQUERIES): ?>
                            <strong style="color:green;">Igen ✅</strong>
                        <?php else: ?>
                            <strong style="color:red;">Nem ❌</strong><br>
                            <code>define('SAVEQUERIES', true);</code> szükséges a wp-config.php fájlban.
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Metrika végpont URL</th>
                    <td>
                        <code id="wp_prometheus_url"><?php echo esc_url(home_url('/v1/metrics')); ?></code>
                        <span id="wp_prometheus_copy_notice" style="display:none; color:green; margin-left:10px;">✔️
                            Vágólapra másolva</span>
                        <script>
                            document.addEventListener("DOMContentLoaded", function () {
                                const el = document.getElementById("wp_prometheus_url");
                                const msg = document.getElementById("wp_prometheus_copy_notice");
                                if (el) {
                                    el.style.cursor = "pointer";
                                    el.addEventListener("click", function () {
                                        navigator.clipboard.writeText(el.textContent)
                                            .then(() => {
                                                msg.style.display = "inline";
                                                setTimeout(() => msg.style.display = "none", 2000);
                                            });
                                    });
                                }
                            });
                        </script>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>

        <hr>

        <h2>Információ</h2>

        <p><strong>Készítette:</strong> <a href='https://cloudmentor.hu' target='_blank'>Cloud Mentor</a></p>
        <ul>
            <li><strong>Verzió:</strong>
                <?php
                if (!function_exists('get_plugin_data')) {
                    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
                }
                $plugin_file = plugin_dir_path(__FILE__) . 'wp-prometheus-exporter.php';
                echo esc_html(get_plugin_data($plugin_file)['Version']);
                ?>
            </li>
            <li><strong>Weboldal:</strong> <a href='https://cloudmentor.hu' target='_blank'>https://cloudmentor.hu</a></li>
            <li><strong>GitHub:</strong> <a href='https://github.com/cloudsteak/wp-prometheus-exporter'
                    target='_blank'>https://github.com/cloudsteak/wp-prometheus-exporter</a></li>
            <li><strong>Prometheus:</strong> <a href='https://prometheus.io' target='_blank'>https://prometheus.io</a></li>
            <li><strong>Grafana:</strong> <a href='https://grafana.com' target='_blank'>https://grafana.com</a></li>
        </ul>

    </div>
    <?php
}
