<?php

    $value = false;

    $buff = array(
        'val1'  => 1,
        'val2'  => 2,
        'val3'  => $value ? 3 : null
    );

    print_r($buff);

    die();
    
 ?>
