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
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap ibleducation-setting-page-container">
    <div id="icon-themes" class="icon32"></div>
    <h2>Disk Usage Main Page View</h2>
    <div>
        <div class="tab">
            <button class="tablinks defaultOpen" id="showResultTab" onclick="openTab(event, 'showResult')">Result</button>
            <button class="tablinks "  onclick="openTab(event, 'gatherResult')">Control</button>
        </div>

        <!-- Tab content -->
        <div id="gatherResult" class="tabcontent">
            <h3>Click on this button to start gathering result</h3>
            <p class="gather-result-btn">
                <input type="button"
                       name="gather-result"
                       id="gather-result"
                       class="button button-primary"
                       data-nonce="<?= $nonce ?>"
                       value="Gather result">
            </p>
            <div id="gather_result_log">

            </div>
        </div>
        <div id="showResult" class="tabcontent" data-nonce="<?= $nonce ?>">
            <div class="log">

            </div>
            <div class="container">
                <table id="result-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Percentage</th>
                            <th>Size</th>
                            <th>Items</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>


    </div>
</div>