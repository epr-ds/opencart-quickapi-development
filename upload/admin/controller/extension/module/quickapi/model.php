<?php

class ModelExtensionModuleQuickapi%s extends Model
{
    public function getApiSessions($api_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "api_session` WHERE api_id = '" . (int)$api_id . "'");
        return $query->rows;
    }

    %s
}