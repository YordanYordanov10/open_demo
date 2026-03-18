<?php
class ModelTestExample extends Model {

    public function getExample($id) {

        $query = $this->db->query("
            SELECT *
            FROM " . DB_PREFIX . "example
            WHERE id = '" . (int)$id . "'
        ");

        return $query->row;
    }

}