<?php
require_once __DIR__ . '/../models/Homeroom.php';

class HomeroomController {
    private $model;

    public function __construct($db) {
        $this->model = new HomeroomModel($db);
    }

    public function getHomerooms($class, $room, $term, $pee) {
        return $this->model->fetchHomerooms($class, $room, $term, $pee);
    }

    public function getHomeroomById($id) {
        return $this->model->fetchHomeroomById($id);
    }

    public function getTypes() {
        return $this->model->fetchHomeroomTypes();
    }

    public function deleteHomeroom($id) {
        return $this->model->deleteHomeroom($id);
    }

    public function updateHomeroom($id, $type, $title, $detail, $result, $image1, $image2) {
        return $this->model->updateHomeroom($id, $type, $title, $detail, $result, $image1, $image2);
    }

    public function insertHomeroom($type, $title, $detail, $result, $date, $major, $room, $term, $pee, $image1 = null, $image2 = null) {
        return $this->model->insertHomeroom($type, $title, $detail, $result, $date, $major, $room, $term, $pee, $image1, $image2);
    }
}

?>
