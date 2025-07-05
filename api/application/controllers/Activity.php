<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity extends MY_Controller {
    
    public function logs() {
        $this->require_role(['admin']);
        
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
        
        // Get activity logs with user info
        $result = $this->db->query(
            "SELECT a.*, u.full_name as user_name
             FROM activity_logs a
             LEFT JOIN users u ON a.user_id = u.id
             ORDER BY a.created_at DESC
             LIMIT ?",
            [$limit]
        );
        
        $logs = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $this->db->fetch_array($result)) {
                $logs[] = [
                    'id' => (int)$row['id'],
                    'action' => $row['action'],
                    'details' => $row['details'],
                    'user' => $row['user_id'] ? [
                        'fullName' => $row['user_name']
                    ] : null,
                    'createdAt' => $row['created_at']
                ];
            }
        }
        
        $this->json_response($logs);
    }
}