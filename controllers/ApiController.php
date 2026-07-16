<?php
class ApiController {
    private const API_KEY = "QUIZB-SMS-SECRET-2026";

    private function verifyApiKey() {
        $headers = getallheaders();
        $providedKey = $headers['X-API-Key'] ?? $headers['x-api-key'] ?? '';
        
        if ($providedKey !== self::API_KEY) {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "Unauthorized: Invalid API Key"]);
            exit;
        }
    }

    public function getSchedules() {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        
        $this->verifyApiKey();
        
        $model = new ScheduleModel();
        $schedules = $model->getAllSchedules();
        
        echo json_encode($schedules);
    }

    public function uploadSchedules() {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        
        $this->verifyApiKey();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (is_array($data)) {
                $model = new ScheduleModel();
                $model->mergeSchedules($data);
                echo json_encode(["status" => "success", "message" => "Schedules merged successfully"]);
            } else {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Invalid JSON array"]);
            }
        } else {
            http_response_code(405);
            echo json_encode(["status" => "error", "message" => "Method not allowed"]);
        }
    }
}
