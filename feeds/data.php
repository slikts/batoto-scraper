<?php

class Data {
    const MIN_ID_LENGTH = 32;
    public $db = NULL;
    public $collection_name = NULL;
    public $collection = NULL;

    static function validate_profile_id($id) {
        return strlen($id) == self::MIN_ID_LENGTH;
    }

    function __construct($db, $collection_name) {
        $this->db = $db;
        $this->collection_name = $collection_name;
        $this->collection = $this->db->{$collection_name};
    }

    function get_items() {
        return $this->collection->items->find();
    }

    function generate_profile_id() {
        $id = str_replace('.', '', uniqid(rand(), TRUE));
        $id = str_pad($id, self::MIN_ID_LENGTH, '0');
        if ($this->get_profile($id)) {
            $id = $this->generate_profile_id();
        }
        return $id;
    }

    function get_series() {
        $series = $this->db->command(array('distinct' => $this->collection_name . '.items', 'key' => 'series'));
        return $series['values'];
    }

    function get_profile($id) {
        return $this->collection->profiles->findOne(array('_id' => $id));
    }

    function get_subscriptions($profile_id) {
        $profile = $this->get_profile($profile_id);
        return isset($profile['subscriptions']) ? $profile['subscriptions'] : array();
    }

    function create_profile($id) {
        if (!self::validate_profile_id($id)) {
            $id = $this->generate_profile_id();
        }
        $this->collection->profiles->insert(array(
            '_id' => $id,
        ));
        return array($this->get_profile($id), $id);
    }

    function update_profile($data, $profile_id=NULL) {
        $profile = $this->get_profile($profile_id);
        if (!$profile) {
            list($profile, $profile_id) = $this->create_profile($profile_id);
        }
        $profile['subscriptions'] = $data;
        $this->collection->profiles->update(array('_id' => $profile_id), $profile);
        return $profile_id;
    }
}
