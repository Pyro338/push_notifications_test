<?php

return [
    'POST/send'      => ['controller' => 'notifications', 'method' => 'send'],
    'POST/mass_send' => ['controller' => 'notifications', 'method' => 'mass_send'],
    'POST/get'       => ['controller' => 'notifications', 'method' => 'get'],
    'POST/cron'      => ['controller' => 'notifications', 'method' => 'cron'],
];