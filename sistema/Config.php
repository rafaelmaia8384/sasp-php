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

	//Definições de LOG do sistema

	define('SISTEMA_LOG_USUARIO_LOGIN', 'USUARIO_LOGIN');
	define('SISTEMA_LOG_USUARIO_CADASTRO', 'USUARIO_CADASTRO');

	//Definições de tempo e prazos

	define('TEMPO_USUARIO_IDAPARELHO_HORAS', 96);						//96 horas para o usuário acessar o sistema com um aparelho diferente.
	define('TEMPO_USUARIO_SESSAO_EXPIRADA_MINUTOS', 30);				//30 minutos para expirar a sessão do usuário.

	//Definições de limites

	define('MYSQL_RETURN_SEARCH_LIMIT', 15);							//15 linhas por vez no retorno dos resultados MYSQL
	define('FILE_SIZE_UPLOAD_MAX', 800);								//800 kb de tamanho máximo para imagens enviadas pro servidor.

?>
