<?php
class ModelMarketingRuleEngine extends Model {


    public function addRuleEngine($data) {

        $this->db->query("INSERT INTO " . DB_PREFIX . "rules SET
            name = '" . $this->db->escape($data['name']) . "',
            description = '" . $this->db->escape($data['description'] ?? '') . "',
            rule_type = 'cart_total',
            priority = '" . (int)($data['priority'] ?? 0) . "',
            stop_processing = '" . (int)($data['stop_processing'] ?? 0) . "',
            status = '" . (int)($data['status'] ?? 1) . "',
            date_start = NULL,
            date_end = NULL,
            date_added = NOW(),
            date_modified = NOW()
        ");

        $rule_id = $this->db->getLastId();

        // Conditions
        if (!empty($data['conditions'])) {
            foreach ($data['conditions'] as $condition) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "rule_conditions SET
                    rule_id = '" . (int)$rule_id . "',
                    type = '" . $this->db->escape($condition['type']) . "',
                    operator = '" . $this->db->escape($condition['operator']) . "',
                    value = '" . $this->db->escape($condition['value']) . "'
                ");
            }
        }

        // Actions
        if (!empty($data['actions'])) {
            foreach ($data['actions'] as $action) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "rule_actions SET
                    rule_id = '" . (int)$rule_id . "',
                    type = '" . $this->db->escape($action['type']) . "',
                    value = '" . $this->db->escape($action['value']) . "'
                ");
            }
        }

        return $rule_id;
    }

  
    public function editRuleEngine($rule_id, $data) {

        $this->db->query("UPDATE " . DB_PREFIX . "rules SET
            name = '" . $this->db->escape($data['name']) . "',
            description = '" . $this->db->escape($data['description'] ?? '') . "',
            priority = '" . (int)($data['priority'] ?? 0) . "',
            stop_processing = '" . (int)($data['stop_processing'] ?? 0) . "',
            status = '" . (int)($data['status'] ?? 1) . "',
            date_modified = NOW()
            WHERE rule_id = '" . (int)$rule_id . "'
        ");

        // Clean old
        $this->db->query("DELETE FROM " . DB_PREFIX . "rule_conditions WHERE rule_id = '" . (int)$rule_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "rule_actions WHERE rule_id = '" . (int)$rule_id . "'");

        // Reinsert conditions
        if (!empty($data['conditions'])) {
            foreach ($data['conditions'] as $condition) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "rule_conditions SET
                    rule_id = '" . (int)$rule_id . "',
                    type = '" . $this->db->escape($condition['type']) . "',
                    operator = '" . $this->db->escape($condition['operator']) . "',
                    value = '" . $this->db->escape($condition['value']) . "'
                ");
            }
        }

        // Reinsert actions
        if (!empty($data['actions'])) {
            foreach ($data['actions'] as $action) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "rule_actions SET
                    rule_id = '" . (int)$rule_id . "',
                    type = '" . $this->db->escape($action['type']) . "',
                    value = '" . $this->db->escape($action['value']) . "'
                ");
            }
        }
    }

  
    public function deleteRuleEngine($rule_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "rule_conditions WHERE rule_id = '" . (int)$rule_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "rule_actions WHERE rule_id = '" . (int)$rule_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "rules WHERE rule_id = '" . (int)$rule_id . "'");
    }


    public function getRuleEngine($rule_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "rules WHERE rule_id = '" . (int)$rule_id . "'");
        return $query->row;
    }

  
    public function getRuleEngines($data = array()) {

        $sql = "SELECT * FROM " . DB_PREFIX . "rules WHERE 1";

        if (!empty($data['filter_name'])) {
            $sql .= " AND name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND status = '" . (int)$data['filter_status'] . "'";
        }

        // Sorting
        $sort_data = array('name', 'priority', 'status', 'date_added');

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY name";
        }

        $sql .= (isset($data['order']) && $data['order'] == 'DESC') ? " DESC" : " ASC";

        // Limit
        if (isset($data['start']) && isset($data['limit'])) {
            $start = max(0, (int)$data['start']);
            $limit = max(1, (int)$data['limit']);
            $sql .= " LIMIT " . $start . "," . $limit;
        }

        $query = $this->db->query($sql);
        return $query->rows;
    }


    public function getTotalRuleEngines($data = array()) {

        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "rules WHERE 1";

        if (!empty($data['filter_name'])) {
            $sql .= " AND name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND status = '" . (int)$data['filter_status'] . "'";
        }

        $query = $this->db->query($sql);
        return (int)$query->row['total'];
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