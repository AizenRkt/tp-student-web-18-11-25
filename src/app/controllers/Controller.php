<?php

namespace app\controllers;

use app\models\Status;
use Flight;

class Controller {

    public function __construct() {
    }

    public function acceuil() {
        $status = new Status(); 
        $data = $status->getStatus();

        Flight::render('acceuil', ['status' => $data]);
    }
}
