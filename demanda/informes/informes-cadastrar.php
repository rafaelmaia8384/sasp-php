<?php

    if (!empty($_POST['cpf_usuario'])	&&
        !empty($_POST['natureza'])	&&
        !empty($_POST['area_opm'])	&&
        !empty($_POST['municipio'])	&&
        !empty($_POST['usuario_latitude'])	&&
        !empty($_POST['usuario_longitude'])	&&
        !empty($_POST['informe'])	&&
        !empty($_POST['latitude'])	&&
		!empty($_POST['longitude'])) {

        $cpf_usuario = $_POST['cpf_usuario'];
        $natureza = $_POST['natureza'];
        $area_opm = $_POST['area_opm'];
        $municipio = $_POST['municipio'];
        $texto_informe = $_POST['informe'];

        $usuario_latitude = $_POST['usuario_latitude'];
        $usuario_latitude = str_replace(',', '.', $usuario_latitude);
        $usuario_longitude = $_POST['usuario_longitude'];
        $usuario_longitude = str_replace(',', '.', $usuario_longitude);

        $latitude = $_POST['latitude'];
        $latitude = str_replace(',', '.', $latitude);
        $longitude = $_POST['longitude'];
        $longitude = str_replace(',', '.', $longitude);

        do {

            $id_informe = generateId();

            $result = $this->db->dbRead("SELECT id FROM tb_informes WHERE id_informe = {$id_informe} LIMIT 1");
        }
        while (is_array($result));

        $informe = array(

            'id_informe'            => $id_informe,
            'cpf_usuario'           => $cpf_usuario,
            'natureza'              => $natureza,
            'area_opm'              => $area_opm,
            'municipio'             => $municipio,
            'informe'               => $texto_informe,
            'latitude'              => $latitude,
            'longitude'             => $longitude,
            'usuario_latitude'      => $usuario_latitude,
            'usuario_longitude'     => $usuario_longitude
        );

        $this->db->dbInsert('tb_informes', $informe);

        if (!empty($_POST['imagens']) && is_array($_POST['imagens'])) {

            $imagens = $_POST['imagens'];
            $max = count($imagens);

            for ($a = 0; $a < $max; $a++) {

                $img = array (

                    'id_informe'        => $id_informe,
                    'cpf_usuario'       => $cpf_usuario,
                    'img_busca'         => $imagens[$a]['img_busca'],
                    'img_principal'     => $imagens[$a]['img_principal']
                );

                $this->db->dbInsert('tb_informes_imagem', $img);
            }
        }

        if (!empty($_POST['pessoas']) && is_array($_POST['pessoas'])) {

            $pessoas = $_POST['pessoas'];
            $max = count($pessoas);

            for ($a = 0; $a < $max; $a++) {

                $pessoa = array();

                if ($pessoas[$a]['servidor_publico'] === '1') {

                    require 'sistema/MetaphonePTBR.php';
                    $metaphone = new Metaphone();

                    $nome_soundex = $metaphone->getPhraseMetaphone($pessoas[$a]['servidor_nome']);
                    $nome = ucwords(strtolower(trim($pessoas[$a]['servidor_nome'])));

                    $pessoa = array (

                        'id_informe'            => $id_informe,
                        'id_pessoa'             => $pessoas[$a]['id_pessoa'],
                        'servidor_publico'       => $pessoas[$a]['servidor_publico'],
                        'servidor_matricula'    => $pessoas[$a]['servidor_matricula'],
                        'servidor_nome'         => $nome,
                        'servidor_nome_soundex' => $nome_soundex,
                        'servidor_municipio'    => $pessoas[$a]['servidor_municipio']
                    );
                }
                else {

                    $pessoa = array (

                        'id_informe'      => $id_informe,
                        'id_pessoa'       => $pessoas[$a]['id_pessoa']
                    );
                }

                $this->db->dbInsert('tb_informes_pessoa', $pessoa);
            }
        }

        if (!empty($_POST['veiculos']) && is_array($_POST['veiculos'])) {

            $veiculos = $_POST['veiculos'];
            $max = count($veiculos);

            for ($a = 0; $a < $max; $a++) {

                $id_veiculo = $veiculos[$a]['id_veiculo'];

                $veiculo = array(

                    'id_veiculo'    => $id_veiculo,
                    'id_informe'  => $id_informe
                );

                $this->db->dbInsert('tb_informes_veiculo', $veiculo);
            }
        }

        $this->db->saspSuccess('Informe cadastrado.');
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
