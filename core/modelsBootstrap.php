<?php

namespace core;

use models\Notifications;

class modelsBootstrap
{
    /**
     * @var baseModel
     */
    public $model;

    /**
     * @var \models\Notifications
     */
    public $notificationsModel;

    public function __construct()
    {
        $this->__setModels();
    }

    private function __setModels()
    {
        $this->model              = new baseModel();
        $this->notificationsModel = new Notifications();
    }
}