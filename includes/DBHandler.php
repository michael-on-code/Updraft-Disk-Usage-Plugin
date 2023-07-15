<?php
if (!defined('ABSPATH')) {
    die;
}

class DBHandler{

    private $_dbTableNameSuffix = "updraft_disk_usage_options";
    private $_idKey = "option_id";
    private $_nameKey = "option_name";
    private $_valueKey = "option_value";

    public function __construct()
    {
    }

    private function getFullDbTableName(){
        global $wpdb;
        return $wpdb->base_prefix.$this->_dbTableNameSuffix;
    }

    public function createTable(){
        //global $wpdb;
        $tableName = $this->getFullDbTableName();
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $sql = "CREATE TABLE IF NOT EXISTS {$tableName} ({$this->_idKey} INT AUTO_INCREMENT NOT NULL,
                {$this->_nameKey} VARCHAR(255) NOT NULL,
                {$this->_valueKey} LONGTEXT,
                PRIMARY KEY ({$this->_idKey})
    )";
        dbDelta($sql);
        /*$is_error = empty( $wpdb->last_error);
		var_dump($is_error);exit;*/
    }

    public function getOption(string $key){
        global $wpdb;
        $tableName = $this->getFullDbTableName();
        $sql = "SELECT {$this->_valueKey} from {$tableName} where {$this->_nameKey} = '{$key}'";
        return self::maybeNullOrEmpty($wpdb->get_row($sql), $this->_valueKey);
    }

    public function insertOrUpdateOption(string $key, string $value){
        global $wpdb;
        $tableName = $this->getFullDbTableName();
        $sql = "SELECT COUNT({$this->_idKey}) AS nbr FROM 
        {$tableName} WHERE {$this->_nameKey} = '{$key}'";
        $keyExist = (bool) self::maybeNullOrEmpty($wpdb->get_row($sql), 'nbr', 0);
        if(!$keyExist){
           return $wpdb->insert($tableName, [
                $this->_nameKey=>$key,
                $this->_valueKey=>$value
            ]);
        }else{
           return $wpdb->update($tableName, [
                $this->_nameKey=>$key,
                $this->_valueKey=>$value
            ], [
                $this->_nameKey=>$key
            ]);
        }
    }

    static function maybeNullOrEmpty( $element, $property, $defaultValue = "" ) {
        if ( is_object( $element ) ) {
            $element = (array) $element;
        }
        if ( isset( $element[ $property ] ) ) {
            return $element[ $property ];
        } else {
            return $defaultValue;
        }

    }
}