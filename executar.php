<?php

    error_reporting(0);
    date_default_timezone_set('America/Araguaina');

    require 'sistema/SaspService.php';

    $ss = new SaspService();

    if ($ss->initService()) {

        $ss->runService();
    }
    else {

        $ss->serviceError("Erro ao iniciar serviço.\n\nProcure o administrador do sistema.");
    }

 ?>
