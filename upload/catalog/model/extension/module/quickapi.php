<?php

class ModelExtensionModuleQuickapi extends Model
{
    public function getApiSessions($api_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "api_session` WHERE api_id = '" . (int)$api_id . "'");
        return $query->rows;
    }

    public function validateSession($api_id, $api_token)
    {
        if (!isset($api_token)){
            return false;
        }

        $api_sessions = $this->getApiSessions($api_id);
        $validated = false;
        foreach($api_sessions as $api_session){
            if ($api_token == $api_session['session_id']){
                $validated = true;
                break;
            }
        }

        return $validated;
    }
}