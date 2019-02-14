<?php

    $result_array = array('date_time' => date('Y-m-d H:i:s', time()));

    $this->db->saspSuccess('Resultado', $result_array);

?>
