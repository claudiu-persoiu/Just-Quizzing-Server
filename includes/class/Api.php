<?php

class Api extends AbstractController {

    public function indexAction() {
        $data = ExportHelper::export();
        ExportHelper::cacheHeaders();
        echo $data;
    }
}