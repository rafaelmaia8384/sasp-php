<?php

    if (!empty($_POST['cpf']) &&
        !empty($_POST['senha']) &&
        !empty($_POST['imei']) &&
        !empty($_POST['mac'])) {

        $cpf = $_POST['cpf'];
        $senha = hash('sha512', $_POST['senha']);
        $imei = $_POST['imei'];
        $mac = $_POST['mac'];

        $result = $this->db->dbRead("SELECT * FROM tb_usuarios WHERE cpf = {$cpf} AND senha = '{$senha}' ORDER BY data_registro DESC LIMIT 1");

        if (is_array($result)) {

            $result[0]['senha'] = '';

            if ($device !== $result[0]['id_aparelho']) {

                $data1 = new DateTime($result[0]['ultimo_login']);
                $data2 = new DateTime(date('Y-m-d H:i:s', time()));

                $intervalo = $data1->diff($data2);

                $horas = $intervalo->days * 24;
                $horas += $intervalo->h;

                $resto = TEMPO_USUARIO_IDAPARELHO_HORAS - $horas;

                if ($horas < TEMPO_USUARIO_IDAPARELHO_HORAS) {

                    $this->db->saspError("Acesso negado.\n\nVocê deve aguardar ". TEMPO_USUARIO_IDAPARELHO_HORAS ." horas para acessar o sistema usando um aparelho diferente.\n\nResta(m): {$resto} hora(s).");
                }
            }

            if ($result[0]['cadastro_analisado'] == 1) {

                if ($result[0]['cadastro_negado'] == 1) {

                    $this->db->saspError('Solicitação de acesso negada.'); //Tratar esse erro no frontend.
                }
            }
            else {

                $this->db->saspError('Solicitação de acesso em análise.'); //Tratar esse erro no frontend.
            }

            if ($result[0]['conta_bloqueada'] == 1) {

                $this->db->saspError("Acesso negado.\n\nSua conta está temporariamente bloqueada.");
            }

            //LOGIN EFETUADO

            $token = utilsGenerateHash(); //Usar token para manejar as sessões de usuário.
            $result[0]['token'] = $token;

            $this->db->dbExecute("UPDATE tb_sistema_sessao SET token = '{$token}', data_registro = NOW() WHERE cpf = {$cpf} LIMIT 1");
            $this->db->dbExecute("UPDATE tb_usuarios SET id_aparelho = '{$device}', imei_ultimo = '{$imei}', mac_ultimo = '{$mac}', ultimo_login = NOW() WHERE cpf = {$cpf} AND conta_excluida = 0 LIMIT 1");
            $this->db->saspSuccess("Login efetuado.", $result[0]);
        }
        else {

            $this->db->saspError("Acesso negado.\n\nVerifique seu CPF ou senha.");
        }
    }
    else {

        $this->db->saspError("Acesso negado.\n\nInforme seu CPF e senha.");
    }

?>
