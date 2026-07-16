<?php
class ScheduleModel {
    private $db_file = __DIR__ . '/../data/schedules.json';

    public function __construct() {
        if (!file_exists($this->db_file)) {
            if (!is_dir(dirname($this->db_file))) {
                mkdir(dirname($this->db_file), 0777, true);
            }
            $initialData = [
                [
                    "id" => uniqid(),
                    "name" => "Budi Santoso",
                    "phoneNumber" => "+6281234567890",
                    "message" => "Halo Budi, ini pesan otomatis dari server.",
                    "timeInMillis" => time() * 1000 + (60 * 60 * 1000), // 1 hour from now
                    "repeatMode" => "NONE"
                ]
            ];
            file_put_contents($this->db_file, json_encode($initialData, JSON_PRETTY_PRINT));
        }
    }

    public function getAllSchedules() {
        $data = file_get_contents($this->db_file);
        return json_decode($data, true);
    }

    public function addSchedule($data) {
        $schedules = $this->getAllSchedules();
        $data['id'] = uniqid(); // Generate new ID
        // Default values for missing data
        if(!isset($data['name'])) $data['name'] = '';
        if(!isset($data['repeatMode'])) $data['repeatMode'] = 'NONE';
        
        $schedules[] = $data;
        $this->save($schedules);
        return $data;
    }

    public function updateSchedule($id, $data) {
        $schedules = $this->getAllSchedules();
        foreach ($schedules as $key => $sched) {
            if ($sched['id'] === $id) {
                // Keep the old ID, update other fields
                $data['id'] = $id;
                // Merge data
                $schedules[$key] = array_merge($sched, $data);
                $this->save($schedules);
                return true;
            }
        }
        return false;
    }

    public function deleteSchedule($id) {
        $schedules = $this->getAllSchedules();
        $newSchedules = array_filter($schedules, function($sched) use ($id) {
            return $sched['id'] !== $id;
        });
        
        if (count($schedules) !== count($newSchedules)) {
            $this->save(array_values($newSchedules));
            return true;
        }
        return false;
    }

    public function mergeSchedules($newSchedules) {
        $current = $this->getAllSchedules();
        $currentMap = [];
        foreach($current as $c) {
            $currentMap[$c['id']] = $c;
        }

        foreach($newSchedules as $ns) {
            // Validate required fields
            if(isset($ns['phoneNumber']) && isset($ns['message']) && isset($ns['timeInMillis'])) {
                $id = isset($ns['id']) ? $ns['id'] : uniqid();
                $ns['id'] = $id;
                
                // Defaults
                if(!isset($ns['name'])) $ns['name'] = '';
                if(!isset($ns['repeatMode'])) $ns['repeatMode'] = 'NONE';

                $currentMap[$id] = $ns;
            }
        }

        $this->save(array_values($currentMap));
        return true;
    }

    private function save($schedules) {
        file_put_contents($this->db_file, json_encode($schedules, JSON_PRETTY_PRINT));
    }
}
