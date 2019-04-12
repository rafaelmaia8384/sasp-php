<?php

    if (!empty($_POST['img_busca']) &&
        !empty($_POST['img_principal']) &&
        !empty($_POST['nome_alcunha']) &&
        !empty($_POST['nome_completo']) &&
        !empty($_POST['nome_da_mae']) &&
        !empty($_POST['cpf_pessoa']) &&
        !empty($_POST['rg_pessoa']) &&
        !empty($_POST['data_nascimento']) &&
        !empty($_POST['crt_cor_pele']) &&
        !empty($_POST['crt_cor_olhos']) &&
        !empty($_POST['crt_cor_cabelos']) &&
        !empty($_POST['crt_tipo_cabelos']) &&
        !empty($_POST['crt_porte_fisico']) &&
        !empty($_POST['crt_estatura']) &&
        !empty($_POST['crt_deficiente']) &&
        !empty($_POST['crt_tatuagem']) &&
        !empty($_POST['relato']) &&
        !empty($_POST['historico_criminal']) &&
        !empty($_POST['areas_de_atuacao'])) {

        $img_busca = $_POST['img_busca'];
        $img_principal = $_POST['img_principal'];

        $nome_alcunha = $_POST['nome_alcunha'];
        $nome_completo = $_POST['nome_completo'];
        $nome_da_mae = $_POST['nome_da_mae'];
        $cpf_pessoa = $_POST['cpf_pessoa'];
        $rg_pessoa = $_POST['rg_pessoa'];
        $data_nascimento = $_POST['data_nascimento'];
        $crt_cor_pele = $_POST['crt_cor_pele'];
        $crt_cor_olhos = $_POST['crt_cor_olhos'];
        $crt_cor_cabelos = $_POST['crt_cor_cabelos'];
        $crt_tipo_cabelos = $_POST['crt_tipo_cabelos'];
        $crt_porte_fisico = $_POST['crt_porte_fisico'];
        $crt_estatura = $_POST['crt_estatura'];
        $crt_deficiente = $_POST['crt_deficiente'];
        $crt_tatuagem = $_POST['crt_tatuagem'];
        $relato = $_POST['relato'];
        $historico_criminal = $_POST['historico_criminal'];
        $areas_de_atuacao = $_POST['areas_de_atuacao'];

        require 'sistema/MetaphonePTBR.php';
        $metaphone = new Metaphone();

        $nome_alcunha_soundex = $metaphone->getPhraseMetaphone($nome_alcunha);
        $nome_alcunha = ucwords(strtolower(trim($nome_alcunha)));

        $nome_completo_soundex = '';

        if ($nome_completo != 'null') {

            $nome_completo_soundex = $metaphone->getPhraseMetaphone($nome_completo);
            $nome_completo = ucwords(strtolower(trim($nome_completo)));
        }
        else {

            $nome_completo = '';
        }

        if ($nome_da_mae == 'null') {

            $nome_da_mae = '';
        }

        if ($cpf_pessoa == 'null') {

            $cpf_pessoa = '0';
        }

        if ($rg_pessoa == 'null') {

            $rg_pessoa = '0';
        }

        if ($data_nascimento == 'null') {

            $data_nascimento = '0000-00-00';
        }

        $agora = date('Y-m-d H:i:s', time());

        $id_pessoa = '';

        do {

            $id_pessoa = generateId();

            $result = $this->db->dbRead("SELECT id FROM tb_pessoas WHERE id_pessoa = {$id_pessoa} LIMIT 1");
        }
        while (is_array($result));

        $pessoa = array(

            'cpf_usuario'				=> $cpf,
            'id_pessoa'                 => $id_pessoa,

            'img_principal' 			=> $img_principal,
            'img_busca'					=> $img_busca,
            'img_enviada'               => 0,

            'nome_alcunha'              => $nome_alcunha,
            'nome_alcunha_soundex'      => $nome_alcunha_soundex,
            'nome_completo'             => $nome_completo,
            'nome_completo_soundex'     => $nome_completo_soundex,
            'nome_da_mae'               => $nome_da_mae,
            'cpf_pessoa'                => $cpf_pessoa,
            'rg_pessoa'                 => $rg_pessoa,
            'data_nascimento'           => $data_nascimento,

            'historico_criminal'        => $historico_criminal,
            'areas_de_atuacao'          => $areas_de_atuacao,

            'crt_cor_pele'              => $crt_cor_pele,
            'crt_cor_olhos'             => $crt_cor_olhos,
            'crt_cor_cabelos'           => $crt_cor_cabelos,
            'crt_tipo_cabelos'          => $crt_tipo_cabelos,
            'crt_porte_fisico'          => $crt_porte_fisico,
            'crt_estatura'              => $crt_estatura,
            'crt_possui_deficiencia'    => $crt_deficiente,
            'crt_possui_tatuagem'       => $crt_tatuagem,

            'txt_relato'                => $relato,

            'num_visualizacoes'         => 0,

            'data_registro'             => $agora,
            'pessoa_excluida'           => 0
        );

        $this->db->dbInsert('tb_pessoas', $pessoa);

        if (!empty($_POST['marcas']) && is_array($_POST['marcas'])) {

            $marcas = $_POST['marcas'];
            $max = count($marcas);

            for ($a = 0; $a < $max; $a++) {

                $marca = array (

                    'id_pessoa'         => $id_pessoa,
                    'cpf_usuario'       => $cpf,
                    'img_busca'         => $marcas[$a]['img_busca'],
                    'img_principal'     => $marcas[$a]['img_principal'],
                    'img_enviada'       => 0,
                    'marca_tipo'        => $marcas[$a]['marca_tipo'],
                    'parte_corpo'       => $marcas[$a]['parte_corpo']
                );

                if ($marcas[$a]['descricao'] !== 'null') {

                    $descricao = ucwords(strtolower(trim($marcas[$a]['descricao'])));
                    $descricao_soundex = $metaphone->getPhraseMetaphone($descricao);
            
                    $marca['descricao'] = $descricao;
                    $marca['descricao_soundex'] = $descricao_soundex;
                }

                $this->db->dbInsert('tb_pessoas_marca_corporal', $marca);
            }
        }

        if (!empty($_POST['imagens']) && is_array($_POST['imagens'])) {

            $imagens = $_POST['imagens'];
            $max = count($imagens);

            for ($a = 0; $a < $max; $a++) {

                $img = array (

                    'id_pessoa'         => $id_pessoa,
                    'cpf_usuario'       => $cpf,
                    'img_busca'         => $imagens[$a]['img_busca'],
                    'img_principal'     => $imagens[$a]['img_principal'],
                    'img_enviada'       => 0,
                    'imagem_excluida'   => 0
                );

                $this->db->dbInsert('tb_pessoas_imagem', $img);
            }
        }

        $id_pessoa = array('id_pessoa' => $id_pessoa);

        $this->db->saspSuccess('Pessoa cadastrada.', $id_pessoa);
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
