<?php

    if (!empty($_POST['img_busca'])	&&
        !empty($_POST['img_principal'])	&&
        !empty($_POST['cpf_usuario'])	&&
        !empty($_POST['relato'])	&&
        !empty($_POST['latitude'])	&&
		!empty($_POST['longitude'])) {

        $img_busca = $_POST['img_busca'];
        $img_principal = $_POST['img_principal'];
        $cpf_usuario = $_POST['cpf_usuario'];
        $relato = $_POST['relato'];
        $latitude = $_POST['latitude'];
        $latitude = str_replace(',', '.', $latitude);
        $longitude = $_POST['longitude'];
        $longitude = str_replace(',', '.', $longitude);

        // if (!utilsValidaCPF($cpf_usuario)) {

        //     $this->db->saspError('Acesso negado.');
        // }

        do {

            $id_abordagem = generateId();

            $result = $this->db->dbRead("SELECT id FROM tb_abordagens WHERE id_abordagem = {$id_abordagem} LIMIT 1");
        }
        while (is_array($result));



        $abordagem = array(

            'id_abordagem'          => $id_abordagem,
            'cpf_usuario'           => $cpf_usuario,
            'img_busca'             => $img_busca,
            'img_principal'         => $img_principal,
            'img_enviada'           => 0,
            'relato'                => $relato,
            'latitude'              => $latitude,
            'longitude'             => $longitude
        );

        $this->db->dbInsert('tb_abordagens', $abordagem);

        if (!empty($_POST['imagens']) && is_array($_POST['imagens'])) {

            $imagens = $_POST['imagens'];
            $max = count($imagens);

            for ($a = 0; $a < $max; $a++) {

                $img = array (

                    'id_abordagem'      => $id_abordagem,
                    'cpf_usuario'       => $cpf_usuario,
                    'img_busca'         => $imagens[$a]['img_busca'],
                    'img_principal'     => $imagens[$a]['img_principal']
                );

                $this->db->dbInsert('tb_abordagens_imagem', $img);
            }
        }
        else {

            $this->db->saspError('Acesso negado.');
        }

        if (!empty($_POST['pessoas']) && is_array($_POST['pessoas'])) {

            $pessoas = $_POST['pessoas'];
            $max = count($pessoas);

            for ($a = 0; $a < $max; $a++) {

                $pessoa = array (

                    'id_abordagem'      => $id_abordagem,
                    'id_pessoa'         => $pessoas[$a]['id_pessoa']
                );

                $this->db->dbInsert('tb_abordagens_pessoa', $pessoa);
            }
        }
        else {

            $this->db->saspError('Acesso negado.');
        }

        if (!empty($_POST['matriculas']) && is_array($_POST['matriculas'])) {

            $matriculas = $_POST['matriculas'];
            $max = count($matriculas);

            for ($a = 0; $a < $max; $a++) {

                $matricula = array (

                    'id_abordagem'      => $id_abordagem,
                    'matricula'         => $matriculas[$a]['matricula']
                );

                $this->db->dbInsert('tb_abordagens_matricula', $matricula);
            }
        }
        else {

            $this->db->saspError('Acesso negado.');
        }

        $this->db->saspSuccess('Abordagem cadastrada.');
	}
	else {

		$this->db->saspError('Acesso negado.');
    }
    
    function generateId() {

		return hexdec(generateHexNumberId());
	}

	function generateHexNumberId() {

	    $characters = '0123456789ABCDEF';
    	$charactersLength = strlen($characters);
    	$randomString = '';

    	for ($i = 0; $i < 8; $i++) {

        	if ($i == 0) $randomString .= $characters[rand(1, $charactersLength - 1)];
			else $randomString .= $characters[rand(0, $charactersLength - 1)];
    	}

    	return $randomString;
	}

?>
