<?php

class Api extends AbstractFrontendController
{

    public function indexAction()
    {
        $data = ExportHelper::export();
        ExportHelper::cacheHeaders();
        echo $data;
    }

    public function displayAuthenticationForm() {
        echo json_encode(array('error' => 'Invalid user or pass'));
    }

    public function postAuthentication($user)
    {
        return true;
    }
}