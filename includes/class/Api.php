<?php

class Api extends AbstractFrontendController
{

    public function indexAction()
    {
        $data = ExportHelper::export();
        ExportHelper::cacheHeaders();
        echo $data;
    }
}