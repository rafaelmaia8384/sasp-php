<?php

    $ip = utilsObterIP();

    $result_array = array('ip' => $ip);

    $this->db->saspSuccess('Endereço IP.', $result_array);

?>
