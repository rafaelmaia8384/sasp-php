<?php

    //FUNCOES DE UTILIDADE GERAL

    function utilsGenerateHash() {

        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < 64; $i++) {

            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    function utilsValidaCPF($cpf) {

	    $cpf = preg_replace('/[^0-9]/', '', $cpf);

	    if (strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999') {

			return false;
	    }
		else {

	        for ($t = 9; $t < 11; $t++) {

			    for ($d = 0, $c = 0; $c < $t; $c++) {

	                $d += $cpf{$c} * (($t + 1) - $c);
	            }

	            $d = ((10 * $d) % 11) % 10;

	            if ($cpf{$c} != $d) {

	                return false;
	            }
	        }

	        return true;
	    }
	}

    function utilsGetSaspImgFolder($modulo, $isBusca) {

        return '../DATA/sasp-img/' . $modulo . ($isBusca ? '/busca/' : '/principal/');
    }

    function utilsObterDadosMilitar($matricula) {

        $url = CORE_PMPB_URL_LOGIN;
        $data = array('email' => CORE_PMPB_LOGIN, 'password' => CORE_PMPB_PASS);

        $options = array(

            'http' => array(

                'header'    => "Content-type: application/json\r\n",
                'method'    => 'POST',
                'timeout'   => 30,
                'content'   => json_encode($data)
            )
        );

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $result = json_decode($result, true);

        if (json_last_error() !== JSON_ERROR_NONE) {

            return array("error" => 1);
        }

        $token = $result['token'];

        $url = CORE_PMPB_URL_MILITAR . substr($matricula, 0, -1) . '-' . substr($matricula, -1);

        $options = array(

            'http' => array(

                'header'    => 'Authorization: ' . $token . "\r\n",
                'timeout'   => 30,
                'method'    => 'GET'
            )
        );

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $result = json_decode($result, true);

        if (json_last_error() !== JSON_ERROR_NONE) {

            return array("error" => 2);
        }

        return $result;
    }

    function utilsValidarIP($ip) {

        if (!empty($ip) && ip2long($ip) != -1) {

            $reserved_ips = array (

                array('0.0.0.0','2.255.255.255'),
                array('10.0.0.0','10.255.255.255'),
                array('127.0.0.0','127.255.255.255'),
                array('169.254.0.0','169.254.255.255'),
                array('172.16.0.0','172.31.255.255'),
                array('192.0.2.0','192.0.2.255'),
                array('192.168.0.0','192.168.255.255'),
                array('255.255.255.0','255.255.255.255')
            );

            foreach ($reserved_ips as $r) {

                $min = ip2long($r[0]);
                $max = ip2long($r[1]);

                if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) {

                    return false;
                }
            }

            return true;
       }
       else {

           return false;
       }
    }

    function utilsObterIP() {

        if (utilsValidarIP($_SERVER["HTTP_CLIENT_IP"])) {

            return $_SERVER["HTTP_CLIENT_IP"];
        }

        foreach (explode(",",$_SERVER["HTTP_X_FORWARDED_FOR"]) as $ip) {

            if (utilsValidarIP(trim($ip))) {

                return $ip;
            }
        }

        if (utilsValidarIP($_SERVER["HTTP_PC_REMOTE_ADDR"])) {

            return $_SERVER["HTTP_PC_REMOTE_ADDR"];
        }
        elseif (utilsValidarIP($_SERVER["HTTP_X_FORWARDED"])) {

            return $_SERVER["HTTP_X_FORWARDED"];
        }
        elseif (utilsValidarIP($_SERVER["HTTP_FORWARDED_FOR"])) {

            return $_SERVER["HTTP_FORWARDED_FOR"];
        }
        elseif (utilsValidarIP($_SERVER["HTTP_FORWARDED"])) {

            return $_SERVER["HTTP_FORWARDED"];
        }
        else {

            return $_SERVER["REMOTE_ADDR"];
        }
    }

?>
