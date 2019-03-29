<?php

    if (!empty($_POST['placa'])	&&
        !empty($_POST['tipo_placa'])	&&
        !empty($_POST['categoria'])	&&
        !empty($_POST['descricao'])) {

        $placa = $_POST['placa'];
        $tipo_placa = $_POST['tipo_placa'];
        $categoria = $_POST['categoria'];
        $descricao = $_POST['descricao'];
    
        do {

            $id_veiculo = generateId();

            $result = $this->db->dbRead("SELECT id FROM tb_veiculos WHERE id_veiculo = {$id_veiculo} LIMIT 1");
        }
        while (is_array($result));

        $veiculo = array(

            'id_veiculo'            => $id_veiculo,
            'cpf_usuario'           => $cpf,
            'tipo_placa'            => $tipo_placa,
            'placa'                 => $placa,
            'categoria'             => $categoria,
            'descricao'             => $descricao,
            'veiculo_excluido'      => 0
        );

        $this->db->dbInsert('tb_veiculos', $veiculo);

        if (!empty($_POST['imagens']) && is_array($_POST['imagens'])) {

            $imagens = $_POST['imagens'];
            $max = count($imagens);

            for ($a = 0; $a < $max; $a++) {

                $img = array (

                    'id_veiculo'         => $id_veiculo,
                    'cpf_usuario'       => $cpf,
                    'img_busca'         => $imagens[$a]['img_busca'],
                    'img_principal'     => $imagens[$a]['img_principal'],
                    'img_enviada'       => 0,
                    'imagem_excluida'   => 0
                );

                $this->db->dbInsert('tb_veiculos_imagem', $img);
            }
        }

        $info_veiculo = array(

            'id_veiculo' => $id_veiculo,
            'tipo_placa' => $tipo_placa,
            'placa' => $placa
        );

        $this->db->saspSuccess('Veículo cadastrado.', $info_veiculo);
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
