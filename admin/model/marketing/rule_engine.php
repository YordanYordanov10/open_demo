<?php
class ModelMarketingRuleEngine extends Model {

    // Добавяне
    public function addRuleEngine($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "rules SET name = '" . $this->db->escape($data['name']) . "', description = '" . $this->db->escape($data['description']) . "', code = '" . $this->db->escape($data['code']) . "', date_added = NOW()");
        return $this->db->getLastId();
    }

    // Редакция
    public function editRuleEngine($rule_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "rules SET name = '" . $this->db->escape($data['name']) . "', description = '" . $this->db->escape($data['description']) . "', code = '" . $this->db->escape($data['code']) . "' WHERE rule_id = '" . (int)$rule_id . "'");
    }

    // Изтриване
    public function deleteRuleEngine($rule_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "rules WHERE rule_id = '" . (int)$rule_id . "'");
    }

    // Един запис
    public function getRuleEngine($rule_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "rules WHERE rule_id = '" . (int)$rule_id . "'");
        return $query->row;
    }

    // Масив от записи за листинг
    public function getRuleEngines($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "rules";

        $implode = array();

        if (!empty($data['filter_name'])) {
            $implode[] = "name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (!empty($data['filter_code'])) {
            $implode[] = "code = '" . $this->db->escape($data['filter_code']) . "'";
        }

        if (!empty($data['filter_date_added'])) {
            $implode[] = "DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }

        if ($implode) {
            $sql .= " WHERE " . implode(" AND ", $implode);
        }

        // Сортиране
        $sort_data = array('name', 'code', 'date_added');
        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY name";
        }

        if (isset($data['order']) && $data['order'] == 'DESC') {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        // Лимит
        if (isset($data['start']) && isset($data['limit'])) {
            if ($data['start'] < 0) $data['start'] = 0;
            if ($data['limit'] < 1) $data['limit'] = 20;
            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows; // <--- Връща масив
    }

    // Брой записи
    public function getTotalRuleEngines($data = array()) {
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "rules";

        $implode = array();

        if (!empty($data['filter_name'])) {
            $implode[] = "name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (!empty($data['filter_code'])) {
            $implode[] = "code = '" . $this->db->escape($data['filter_code']) . "'";
        }

        if (!empty($data['filter_date_added'])) {
            $implode[] = "DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }

        if ($implode) {
            $sql .= " WHERE " . implode(" AND ", $implode);
        }

        $query = $this->db->query($sql);

        return (int)$query->row['total']; // <--- Връща int
    }

    public function getRuleConditions($rule_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "rule_conditions WHERE rule_id = '" . (int)$rule_id . "'");
        return $query->rows;
    }

    public function getRuleActions($rule_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "rule_actions WHERE rule_id = '" . (int)$rule_id . "'");
        return $query->rows;
    }
}