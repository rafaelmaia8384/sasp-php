<?php

    require 'sistema/Config.php';
    require 'sistema/Database.php';
    require 'sistema/Utils.php';

	class SaspService {

		private $db;
        private $demanda;
        private $demandas;

		function __construct() {

			$this->db = new DataBase();

            $this->demandas = array(

                //Demandas do servidor
                '11'  => 'demanda/server-user-ip.php',
                '12'  => 'demanda/server-date-time.php',
                '13'  => 'demanda/server-upload-image.php',

                //Demandas de usuários (sem token)
                '101' => 'demanda/usuarios/usuario-login.php',
                '102' => 'demanda/usuarios/usuario-cadastrar.php',

                //Demandas de usuários (com token)
                '111' => 'demanda/usuarios/xxx1.php',
                '112' => 'demanda/usuarios/xxx2.php',
                '113' => 'demanda/usuarios/xxx3.php',

                //Demandas de pessoas
                '201' => 'demanda/pessoas/pessoas-ultimos-cadastros.php',
                '202' => 'demanda/pessoas/pessoas-perfil.php',
                '203' => 'demanda/pessoas/pessoas-perfil-imagens.php',
                '204' => 'demanda/pessoas/pessoas-perfil-comentarios.php',
                '205' => 'demanda/pessoas/pessoas-buscar.php',
                '206' => 'demanda/pessoas/pessoas-cadastrar.php',
                '207' => 'demanda/pessoas/pessoas-meus-cadastros.php',
                '208' => 'demanda/pessoas/pessoas-buscar-pessoa.php', 

                //Demandas de abordagens
                '301' => 'demanda/abordagens/abordagens-ultimos-cadastros.php',
                '302' => 'demanda/abordagens/abordagens-cadastrar.php'
            );
		}

		function initService() {

            //
            //
            // LEMBRAR DE AJEITAR ESSE ESCAPE CONTENT QUE NAO TA FUNCIONANDO
            //
            //

            $this->escapeContent($_POST);

            if (!empty($_POST['plataforma']) &&     //Web, Android, iOS
                !empty($_POST['versao']) &&         //versao atual do front-end
                !empty($_POST['device']) &&         //id atual do aparelho
                !empty($_POST['network']) &&        //tipo de conexao
                !empty($_POST['demanda'])) {        //demanda a ser solicitada

                if (array_key_exists($_POST['demanda'], $this->demandas)) {

                    $this->demanda = $_POST['demanda'];

                    return true;
                }
                else {

                    return false;
                }
            }
            else {

                return false;
            }
        }

        function runService() {

            $plataforma = $_POST['plataforma'];
            $versao = $_POST['versao'];
            $device = $_POST['device'];
            $network = $_POST['network'];
            $codigo = $_POST['codigo'];

            $cpf = 0;
            $search_limit = MYSQL_RETURN_SEARCH_LIMIT;

            if ($this->demanda > 110) {

                if ($this->isSessionExpired($_POST['token'])) {

                    $this->db->saspError("Sessão expirada.");
                }

                $cpf = $this->serviceLogToken($_POST['token']);
            }
            else if ($this->demanda > 100) {

                $this->serviceLogCPF($_POST['cpf']);
            }

            include($this->demandas[$this->demanda]);

            //Só será executada a próxima linha se der erro no include acima.
            $this->db->saspError("Erro ao executar script.\n\nContate o administrador do sistema."); //$this->db->dbEscapeString(json_encode(error_get_last(), JSON_UNESCAPED_UNICODE)));
        }

        function serviceLogCPF($cpf) {

            $ip = utilsObterIP();
            $post_content = json_encode($_POST, JSON_UNESCAPED_UNICODE);

            $log = array(

                'cpf'               => $cpf,
                'plataforma'        => $_POST['plataforma'],
                'id_aparelho'       => $_POST['device'],
                'ip'                => $ip,
                'network'           => $_POST['network'],
                'script'            => $this->demandas[$this->demanda],
                'post_content'      => $post_content
            );

            $this->db->dbInsert('tb_sistema_log', $log);
        }

        function serviceLogToken($token) {

            $result = $this->db->dbRead("SELECT cpf FROM tb_sistema_sessao WHERE token = '{$token}' ORDER BY data_registro DESC LIMIT 1");

            if (is_array($result)) {

                $this->serviceLogCPF($result[0]['cpf']);

                return $result[0]['cpf'];
            }

            return 0;
        }

        function isSessionExpired($token) {

            $result = $this->db->dbRead("SELECT data_registro FROM tb_sistema_sessao WHERE token = '{$token}' ORDER BY data_registro DESC LIMIT 1");

            if (is_array($result)) {

                $data1 = new DateTime($result[0]['data_registro']);
                $data2 = new DateTime(date('Y-m-d H:i:s', time()));

                $intervalo = $data1->diff($data2);

                $minutos = $intervalo->days * 24 * 60;
                $minutos += $invervalo->h * 60;
                $minutos += $intervalo->i;

                if ($minutos > TEMPO_USUARIO_SESSAO_EXPIRADA_MINUTOS) {

                    return true;
                }
                else {

                    $this->db->dbExecute("UPDATE tb_sistema_sessao SET data_registro = NOW() WHERE token = '{$token}'");

                    return false;
                }
            }
            else {

                return true;
            }
        }

        function escapeContent($array) {

            array_walk_recursive($array, function(&$leaf) {

                if (is_string($leaf)) {

                    $leaf = $this->db->dbEscapeString($leaf);
                }
            });
        }

        function serviceError($msg) {

            $this->db->saspError($msg);
        }
	}

?>
