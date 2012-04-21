<?php

class Data {
    const MIN_ID_LENGTH = 32;
    const ID_REGEX = '/^[\w\d_]{32}$/i';
    public $db = NULL;
    public $collection_name = NULL;
    public $collection = NULL;

    static function validate_profile_id($id) {
        return strlen($id) == self::MIN_ID_LENGTH && preg_match(self::ID_REGEX, $id);
    }

    function __construct($db, $collection_name) {
        $this->db = $db;
        $this->collection_name = $collection_name;
        $this->collection = $this->db->{$collection_name};
    }

    function get_items($profile, $limit=10) {
        $filter = array();
        $languages_filter = $profile['languages'];
        if ($languages_filter) {
            $filter['language'] = array('$in' => $profile['languages']);
        }
        $series_filter = $profile['series'];
        if ($series_filter) {
            $filter['series'] = array('$in' => $profile['series']);
        }
        
        return $this->collection->items->find($filter)->limit($limit);
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
        $result = $this->db->command(array('distinct' => $this->collection_name . '.items', 'key' => 'series'));
        $series = $result['values'];
        sort($series);
        return $series;
    }
    
    function get_languages() {
        $result = $this->db->command(array('distinct' => $this->collection_name . '.items', 'key' => 'language'));
        $languages = $result['values'];
        sort($languages);
        return $languages;
    }

    function get_profile($id) {
        return $this->collection->profiles->findOne(array('_id' => $id));
    }

    function get_subscriptions($profile_id) {
        $profile = $this->get_profile($profile_id);
        return $profile;
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
        $profile['series'] = $data['series'];
        $profile['languages'] = $data['languages'];
        $profile['title'] = filter_var($data['title'], FILTER_SANITIZE_STRING);
        $this->collection->profiles->update(array('_id' => $profile_id), $profile);
        message("Subscriptions saved to profile &lt;$profile_id>");
        return $profile_id;
    }
}
