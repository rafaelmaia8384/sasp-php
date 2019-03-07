<?php

	//Definições de acesso do Banco de Dados

	define('DB_HOSTNAME', 'localhost');
	define('DB_USERNAME', 'root');
	define('DB_PASSWORD', 'rafael110786MYSQL');
	define('DB_DATABASE', 'db_sasp');
	define('DB_CHARSET', 'utf8');

	//Definições de acesso ao core da PMPB

	define('CORE_PMPB_URL_LOGIN', 'https://stagebo.apps.pm.pb.gov.br/api/login');
	define('CORE_PMPB_URL_MILITAR', 'https://stagebo.apps.pm.pb.gov.br/api/cops/');
	define('CORE_PMPB_LOGIN', 'test@test.com');
	define('CORE_PMPB_PASS', '12345678');

	//Definições de tempo e prazos

	define('TEMPO_USUARIO_IDAPARELHO_HORAS', 96);						//96 horas para o usuário acessar o sistema com um aparelho diferente.
	define('TEMPO_USUARIO_SESSAO_EXPIRADA_MINUTOS', 30);				//30 minutos para expirar a sessão do usuário.

	//Definições de limites

	define('MYSQL_RETURN_SEARCH_LIMIT', 15);							//15 linhas por vez no retorno dos resultados MYSQL
    define('FILE_SIZE_UPLOAD_MAX', 800);								//800 kb de tamanho máximo para imagens enviadas pro servidor.

    //Definições de permissões de usuários

    define('PERMISSAO_USUARIO_VER_USUARIOS', 0);
    define('PERMISSAO_USUARIO_BLOQUEAR_USUARIOS', 1);
    define('PERMISSAO_USUARIO_EXCLUIR_USUARIOS', 2);
    define('PERMISSAO_USUARIO_ANALISAR_SOLICITACOES_ACESSO', 3);
    define('PERMISSAO_USUARIO_EDITAR_CADASTROS', 4);
    define('PERMISSAO_USUARIO_EXCLUIR_CADASTROS', 5);
    define('PERMISSAO_USUARIO_VER_INFORMES_COMUNS', 6);
    define('PERMISSAO_USUARIO_VER_INFORMES_MILITARES', 7);
    define('PERMISSAO_USUARIO_EMITIR_ALERTAS', 8);
    define('PERMISSAO_USUARIO_MODIFICAR_PERMISSOES', 9);
    define('PERMISSAO_USUARIO_NIVEL5', 10);

?>
