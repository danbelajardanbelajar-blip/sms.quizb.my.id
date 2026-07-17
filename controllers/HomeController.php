<?php
class HomeController {
    public function index() {
        $model = new ScheduleModel();
        require_once __DIR__ . '/../models/LogModel.php';
        $logModel = new LogModel();
        
        $schedules = $model->getAllSchedules();
        $logs = $logModel->getAllLogs();
        
        require __DIR__ . '/../views/home.php';
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new ScheduleModel();
            
            // Format time string to epoch ms for Android
            $timeString = $_POST['scheduled_time'] ?? '';
            $timeInMillis = strtotime($timeString) * 1000;
            if ($timeInMillis <= 0) $timeInMillis = time() * 1000; // fallback

            $data = [
                'name' => $_POST['name'] ?? '',
                'phoneNumber' => $_POST['phoneNumber'] ?? '',
                'message' => $_POST['message'] ?? '',
                'timeInMillis' => $timeInMillis,
                'repeatMode' => $_POST['repeatMode'] ?? 'NONE'
            ];
            
            $model->addSchedule($data);
        }
        header("Location: index.php");
        exit;
    }

    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $model = new ScheduleModel();
            
            $timeString = $_POST['scheduled_time'] ?? '';
            $timeInMillis = strtotime($timeString) * 1000;
            if ($timeInMillis <= 0) $timeInMillis = time() * 1000;

            $data = [
                'name' => $_POST['name'] ?? '',
                'phoneNumber' => $_POST['phoneNumber'] ?? '',
                'message' => $_POST['message'] ?? '',
                'timeInMillis' => $timeInMillis,
                'repeatMode' => $_POST['repeatMode'] ?? 'NONE'
            ];
            
            $model->updateSchedule($_POST['id'], $data);
        }
        header("Location: index.php");
        exit;
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $model = new ScheduleModel();
            $model->deleteSchedule($_GET['id']);
        }
        header("Location: index.php");
        exit;
    }
}
