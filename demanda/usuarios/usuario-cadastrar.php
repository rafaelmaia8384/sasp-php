<?php

    if (!empty($_POST['cpf']) &&
        !empty($_POST['matricula']) &&
        !empty($_POST['email']) &&
        !empty($_POST['telefone']) &&
        !empty($_POST['senha']) &&
        !empty($_POST['id_aparelho']) &&
        !empty($_POST['imei']) &&
        !empty($_POST['mac'])) {

        require 'sistema/MetaphonePTBR.php';

        $metaphone = new Metaphone();

        $cpf = $_POST['cpf'];
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        $matricula = $_POST['matricula'];
        $matricula = preg_replace('/[^0-9]/', '', $matricula);
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $senha = hash('sha512', $_POST['senha']);
        $id_aparelho = $_POST['id_aparelho'];
        $imei = $_POST['imei'];
        $mac = $_POST['mac'];

        if (!utilsValidaCPF($cpf)) {

            $this->db->saspError('CPF inválido.');
        }

        //Verificar se o CPF já está cadastrado no sistema
        $result = $this->db->dbRead("SELECT cadastro_analisado, cadastro_negado FROM tb_usuarios WHERE cpf = {$cpf} AND conta_excluida = 0 ORDER BY data_registro DESC LIMIT 1");

        if (is_array($result)) {

            if ($result[0]['cadastro_analisado'] == 0) {

                $this->db->saspError('O CPF informado está sob análise de cadastro.');
            }
            else if ($result[0]['cadastro_negado'] == 0) {

                $this->db->saspError('O CPF informado já está cadastrado no sistema.');
            }
        }

        //Verificar se a matrícula já está cadastrada no sistema
        $result = $this->db->dbRead("SELECT cadastro_analisado, cadastro_negado FROM tb_usuarios WHERE matricula = {$matricula} AND conta_excluida = 0 ORDER BY data_registro DESC LIMIT 1");

        if (is_array($result)) {

            if ($result[0]['cadastro_analisado'] == 0) {

                $this->db->saspError('A matrícula informada está sob análise de cadastro.');
            }
            else if ($result[0]['cadastro_negado'] == 0) {

                $this->db->saspError('A matrícula informada já está cadastrada no sistema.');
            }
        }

        if (!empty($_FILES['img_principal'])) {

            if ($_FILES['img_principal']['error'] != 0) {

                $this->db->saspError('Erro no envio da imagem. Tente novamente mais tarde.');
            }

            if (($_FILES['img_principal']['size'] / 1024) > FILE_SIZE_UPLOAD_MAX) {

                $this->db->saspError('O tamanho máximo da imagem deve ser de '. FILE_SIZE_UPLOAD_MAX .'kB.');
            }
        }
        else {

            $this->db->saspError('Imagem não anexada.');
        }

        if (!empty($_FILES['img_busca'])) {

            if ($_FILES['img_principal']['error'] != 0) {

                $this->db->saspError('Erro no envio da imagem. Tente novamente mais tarde.');
            }

            if (($_FILES['img_principal']['size'] / 1024) > FILE_SIZE_UPLOAD_MAX) {

                $this->db->saspError('O tamanho máximo da imagem deve ser de '. FILE_SIZE_UPLOAD_MAX .'kB.');
            }
        }
        else {

            $this->db->saspError('Imagem não anexada.');
        }

        //Fazer solicitação de informações do militar ao servidor do EM8
        $result = utilsObterDadosMilitar($matricula);

        if (is_array($result)) {

            if (array_key_exists('error', $result)) {

                if ($result['error'] == 1) {

                    $this->db->saspError("Falha na autenticação do servidor externo.\n\nInforme ao administrador do sistema.");
                }
                else {

                    $this->db->saspError("Militar não encontrado.\n\nSua matrícula está correta?");
                }
            }
        }
        else {

            $this->db->saspError("Erro de comunicação com servidor externo.\n\nPor favor, tente novamente após alguns minutos.");
        }

        $nome_completo = $result['cop']['fullName'];
        $nome_completo = ucwords(strtolower(trim($nome_completo)));
        $nome_funcional = $result['cop']['name'];
        $nome_funcional = ucwords(strtolower(trim($nome_funcional)));
        $nome_completo_soundex = $metaphone->getPhraseMetaphone($nome_completo);
        $nome_funcional_soundex = $metaphone->getPhraseMetaphone($nome_funcional);
        $grau_hierarquico = trim($result['cop']['rank']);
        $lotacao_atual =  $result['cop']['unit']['name'];
        $ip = utilsObterIP();

        $imgBuscaName = time() . '-' . $_FILES['img_busca']['name'];
        $imgPrincipalName = time() . '-' . $_FILES['img_principal']['name'];

        $agora = date('Y-m-d H:i:s', time());

        $cadastro = array(

            'nome_completo'                 => $nome_completo,
            'nome_completo_soundex'         => $nome_completo_soundex,
            'nome_funcional'                => $nome_funcional,
            'nome_funcional_soundex'        => $nome_funcional_soundex,
            'img_busca'                     => $imgBuscaName,
            'img_principal'                 => $imgPrincipalName,

            //'img_pmpb'                      => $img_pmpb,

            'cpf'                           => $cpf,
            'email'                         => $email,
            'telefone'                      => $telefone,
            'matricula'                     => $matricula,
            'senha'                         => $senha,
            'grau_hierarquico'              => $grau_hierarquico,
            'lotacao_atual'                 => $lotacao_atual,
            'ultimo_login'                  => $agora,
            'ultima_atualizacao_documental' => $agora,              //lembrar de implementar um modo de atualizar as informações do usuário
            'id_aparelho'                   => $id_aparelho,
            'ip_cadastro'                   => $ip,
            'ip_ultimo'                     => $ip,
            'imei_cadastro'                 => $imei,
            'imei_ultimo'                   => $imei,
            'mac_cadastro'                  => $mac,
            'mac_ultimo'                    => $mac,
            'nivel_de_acesso'               => 1,
            'cadastro_analisado'            => 0,
            'cadastro_analisado_por'        => 0,
            'cadastro_negado'               => 0,
            'conta_excluida'                => 0,
            'conta_bloqueada'               => 0
        );

        $imgBuscaPath = utilsGetSaspImgFolder('usuarios', true) . $imgBuscaName;
        $imgPrincipalPath = utilsGetSaspImgFolder('usuarios', false) . $imgPrincipalName;

        if (!move_uploaded_file($_FILES['img_busca']['tmp_name'], $imgBuscaPath)) {

			$this->db->saspError("Erro\n\n" . $this->db->dbEscapeString(json_encode(error_get_last(), JSON_UNESCAPED_UNICODE)));
		}

        if (!move_uploaded_file($_FILES['img_principal']['tmp_name'], $imgPrincipalPath)) {

			$this->db->saspError("Erro\n\n" . $this->db->dbEscapeString(json_encode(error_get_last(), JSON_UNESCAPED_UNICODE)));
		}

        $this->db->dbInsert('tb_usuarios', $cadastro);

        $resultSessao = $this->db->dbRead("SELECT id FROM tb_sistema_sessao WHERE cpf = {$cpf} LIMIT 1");

        if (!is_array($resultSessao)) {

            $sessao = array('cpf' => $cpf, 'token' => '0');
            $this->db->dbInsert('tb_sistema_sessao', $sessao);
        }

        $this->db->saspSuccess('Solicitação enviada.');
    }
    else {

        $this->db->saspError("Acesso negado.\n\nInforme todos os campos para solicitar o acesso.");
    }

?>
