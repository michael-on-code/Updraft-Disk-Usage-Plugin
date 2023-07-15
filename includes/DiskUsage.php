<?php
if (!defined('ABSPATH')) {
    die;
}

class DiskUsage
{

    private $_pluginName = "Disk Usage";
    private $_pluginSlug = "updraft-disk-usage-plugin";
    private $_textDomain = "updraft-disk-usage-plugin";
    public $_nonceSlug = 'updraft-disk-usage-nonce';
    public $_fileIdentificator = "disk_usage_is_file_";
    private $_optionTreeKey = "updraft-disk-usage-tree";
    private $_optionTreeSavedKey = "updraft-disk-usage-is-tree-saved";
    private $_optionTreeLengthKey = "updraft-disk-usage-chunks-length";
    private $_chunkPrefixKey = "updraft-disk-usage-chunk-element-";

    public $_timeoutFieldName ="updraft_disk_usage_timeout-field";

    private $_chunkSize = 1;
    private $_defaultTimeout = 5;//seconds
    
    private $_chunksTimeoutKey = "updraft-disk-usage-timeout";

    private function getChunksTimeout(){
        require_once 'DBHandler.php';
        $dbHandler = new DBHandler();
        $previouslySavedTimeout = $dbHandler->getOption($this->_chunksTimeoutKey);
        if($previouslySavedTimeout === "" || absint($previouslySavedTimeout) <= 0){
            $previouslySavedTimeout = $this->_defaultTimeout;
        }
        return $previouslySavedTimeout;
    }

    public function handleSettingsPageForm(){
        require_once 'DBHandler.php';
        $dbHandler = new DBHandler();
        if(isset($_POST[$this->_timeoutFieldName])){
            if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], $this->_nonceSlug)) {
                $rawInput = sanitize_text_field($_POST[$this->_timeoutFieldName]);
                $safeInput = absint($rawInput);
                if($safeInput <= 0){
                    //INPUT IS NOT A VALID NUMBER
                    add_settings_error("", "INVALID", __("Invalid input! Try again with a valid number", $this->_textDomain));
                }else{
                    //STORE IN DB
                    $dbHandler->insertOrUpdateOption($this->_chunksTimeoutKey, $safeInput);
                    add_settings_error("", "VALID", __("Worker time saved !", $this->_textDomain), "success");
                    return $safeInput;
                }
            }else{
                add_settings_error("", "NONCE", __("Nonce unavailable ! Operation aborted, can't continue", $this->_textDomain));
                //NONCE PROBLEM
            }
        }
        return $this->getChunksTimeout();
    }

    public function gatherResultFromAjax()
    {
        if (!wp_verify_nonce($_REQUEST['nonce'], $this->_nonceSlug)) {
            exit("No naughty business please");
        }
        require_once 'DBHandler.php';
        $dbHandler = new DBHandler();
        if (isset($_REQUEST['useSavedDataOnly'])) {
            if ($dbHandler->getOption($this->_optionTreeSavedKey) === '1') {
                $chunkLength = $dbHandler->getOption($this->_optionTreeLengthKey);
                if (isset($_REQUEST['chunkKey'])
                    && is_numeric($_REQUEST['chunkKey'])
                    && (int)$_REQUEST['chunkKey'] < $chunkLength) {
                    $actualChunkIndex = (int)$_REQUEST['chunkKey'];
                    $toBeShowedChunkIndex = $actualChunkIndex + 1;
                    echo json_encode([
                        "status" => "1",
                        "message" => "Chunk {$toBeShowedChunkIndex} generated",
                        'data' => [
                            'chunk' => maybe_unserialize($dbHandler->getOption($this->_chunkPrefixKey . $actualChunkIndex)),
                            'can_continue' => ($actualChunkIndex + 1) < $chunkLength ? "1" : "0",
                            "nonce" => wp_create_nonce($this->_nonceSlug)
                        ]
                    ]);
                } else {
                    echo json_encode([
                        "status" => "1",
                        "message" => "Pre Saved Data generated. {$chunkLength} chunks to be loaded",
                        "data" => [
                            "timeout" => $this->getChunksTimeout(),
                            "nonce" => wp_create_nonce($this->_nonceSlug),
                            "file_prefix" => $this->_fileIdentificator
                        ]
                    ]);
                }
            } else {
                echo json_encode([
                    "status" => "0",
                    "message" => "No data to be loaded. Click on the gathering result button to load data !",
                ]);
            }
            die();
        }
        $generatedTree = $this->dir_tree(ABSPATH);
        $generatedChunkedTree = $this->chunkifyTree($generatedTree);
        if (!empty($generatedChunkedTree)) {
            foreach ($generatedChunkedTree as $key => $chunk) {
                $dbHandler->insertOrUpdateOption($this->_chunkPrefixKey . $key, maybe_serialize($chunk));
            }
        }
        $dbHandler->insertOrUpdateOption($this->_optionTreeLengthKey, $chunkLength = count($generatedChunkedTree));
        $dbHandler->insertOrUpdateOption($this->_optionTreeSavedKey, "1");
        echo json_encode([
            "status" => "1",
            "message" => "Data Generated. {$chunkLength} chunks to be loaded",
            "data" => [
                "timeout" => $this->getChunksTimeout(),
                "nonce" => wp_create_nonce($this->_nonceSlug),
                "file_prefix" => $this->_fileIdentificator
            ]
        ]);
        die();
    }

    private function chunkifyTree(array $generatedTree)
    {
        return array_chunk($generatedTree, $this->_chunkSize, true);

    }

    public function mustAuthenticate()
    {
        echo json_encode([
            "status" => "0",
            "message" => "You must be authenticated first"
        ]);

        die();
    }

    private function dir_tree($dir_path)
    {
        $rdi = new \RecursiveDirectoryIterator($dir_path);

        $rii = new \RecursiveIteratorIterator($rdi);

        $tree = [];

        foreach ($rii as $splFileInfo) {
            $file_name = $splFileInfo->getFilename();

            // Skip hidden files and directories.
            if ($file_name[0] === '.') {
                continue;
            }

            $path = $splFileInfo->isDir() ? array($file_name => array()) : array($this->_fileIdentificator . $file_name => [
                'size' => $splFileInfo->getSize(),
                'name' => $file_name,
            ]);

            for ($depth = $rii->getDepth() - 1; $depth >= 0; $depth--) {
                $path = array($rii->getSubIterator($depth)->current()->getFilename() => $path);
            }

            $tree = array_merge_recursive($tree, $path);
        }

        return $tree;
    }


    public function addPluginAdminMenus()
    {
        add_menu_page(__($this->_pluginName . ' View', $this->_textDomain),
            __($this->_pluginName, $this->_textDomain),
            'manage_options', $this->_pluginSlug . '-main', [
                $this, 'displayPluginMainPage'
            ]
        );
        add_submenu_page($this->_pluginSlug . '-main',
            __($this->_pluginName . ' Settings', $this->_textDomain),
            __('Settings', $this->_textDomain),
            'manage_options',
            $this->_pluginSlug . '-settings', [
                $this,
                'displayPluginSettingPage'
            ]);
    }

    public function displayPluginMainPage()
    {
        require_once 'partials/mainpage-display.php';
    }

    public function displayPluginSettingPage()
    {
        if (isset($_GET['error_message'])) {
            add_action('admin_notices', array($this, 'settingsPageSettingsMessages'));
            do_action('admin_notices', $_GET['error_message']);
        }
        require_once 'partials/settingpage-display.php';

    }


    public function settingsPageSettingsMessages($error_message)
    {
        $message = "";
        $err_code = "";
        $setting_field = "";
        $type = 'error';
        switch ($error_message) {
            case '1':
                $message = __('There was an error adding this setting. Please try again.  If this persists, shoot us an email.', $this->_textDomain);
                $err_code = esc_attr('settings_page_example_setting');
                $setting_field = 'settings_page_example_setting';
                break;
        }
        add_settings_error(
            $setting_field,
            $err_code,
            $message,
            $type
        );
    }


}