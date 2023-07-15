<?php
$diskUsage = new DiskUsage();
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.animashaunmichael.com
 * @since      1.0.0
 *
 * @package    Updraft_Disk_Usage_Plugin
 * @subpackage Updraft_Disk_Usage_Plugin/admin/partials
 */
$nonce = wp_create_nonce($diskUsage->_nonceSlug);
$timeout = $diskUsage->handleSettingsPageForm()
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap ibleducation-setting-page-container">
    <div id="icon-themes" class="icon32"></div>
    <h2 style="padding-bottom: 20px">Disk Usage Settings Page View</h2>
    <!--NEED THE settings_errors below so that the errors/success messages are shown after submission - wasn't working once we started using add_menu_page and stopped using add_options_page so needed this-->
    <?php settings_errors(); ?>
    <form method="POST">
        <div class="form-fields">
            <label for="timeout-field" >Worker Time (seconds)</label>
            <input id="timeout-field" required min="1"
                   name="<?php echo $diskUsage->_timeoutFieldName ?>"
                   placeholder="timeout" value="<?php echo $timeout ?>" />
            <input type="hidden" name="nonce" value="<?php echo $nonce ?>" />
        </div>

        <?php submit_button(); ?>
    </form>

</div>