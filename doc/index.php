
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Orion :: documentação</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 0.19.1" />
</head>

<body>
	<h1>Orion</h1>
	<h2>Extração e conversão de dados.</h2>
	
	<p>Orion é um sistema para extração e conversão de dados de arquivos texto com campos separados por ponto-e-vírgula ou campos de largura fixa.</p>
	<p>O projeto Orion é derivado da classe <a href="http://www.vivaolinux.com.br/contribuir/scripts/verScript.php?codigo=4903">TxtXtrator</a></p>
	<p>Também é possível extrair dados de relatórios-texto compatíveis com o formato de colunas com largura fixa.</p>
	<p>Recomendamos acompanhar a página do projeto <a href="http://code.google.com/p/orion-data-extract/">Orion</a>. Lá, além de ficar por dentro do que passa no projeto e fazer o download das últimas versões, você pode conhecer a versão anterior, com interface em ExtJS.</p>
	<p>Orion utiliza extensivamente as funcionalidades do projeto XtPHP, portanto, se deseja aprofundar seus conhecimentos sobre este projeto, recomendamos que você conheça também o projeto <a href="http://code.google.com/p/xtphp-lib/">XtPHP</a>.</p>
	
	<h2>Licenciamento</h2>
	<p>Orion é distribuído com a licença GPL. Para detalhes consulte o arquivo licence.txt</p>
	
	<h2>Próximos passos</h2>
	<p>O próximo passo deste projeto é o desenvolvimento de uma interface gráfica (provavelmente utilizando ExtJS) para a criação e edição dos arquivos XML de configurações de projeto e para execução das extrações/conversões.</p>
	
	<h2>Requisitos</h2>
	<p>Para utilizar Orion é necessário ter isntalado Apache (ou outro servidor web compatível com PHP) e PHP5 comsuporte a XML.</p>
	<p>Também é necessário ter um pouco de conhecimento sobre PHP, já que a utilização do Orion depende de script PHP do usuário.</p>
	
	<h2>Instalação</h2>
	<p>Descompacte o diretório com os arquivos do Orion no diretório web do seu servidor (p. e. /var/www/html/).</p>
	
	<h2>Configuração global</h2>
	<p>Orion usa o arquivo config.php, no diretório "config" para as configurações globais.</p>
	
	<h2>Usando Orion</h2>
	<p>Para utilizar o Orion, crie um script fazendo nele require() para os arquivos config.php (diretório "config") e orion2.php (no diretório "app").</p>
	<p>Para iniciar a extração/conversão faça:</p>
	$data = Orion::parseSource($project, $source);
	<p>Onde $project é o caminho para o XML do projeto e $source é o caminho para o arquivo-fonte de dados.</p>
	<p>A variável $data (ou outra qualquer) irá receber os dados convertidos conforme segue:</p>
	<ul>
		<li>Saída CSV corresponde a um array onde cada elemento é uma linha CSV, sendo a primeira, o nome das colunas.</li>
		<li>Saída XML: uma string XML.</li>
		<li>Saída JSON: uma string JSON.</li>
		<li>Saída SQL: um array contendo string INSERT.</li>
	</ul>
	<p>Para exemplos de utilização, consulte o diretório "examples" e carregue no seu navegador os arquivos *.php.</p>
	
	<h2>Configuração do projeto</h2>
	<p>Orion utiliza arquivos XML para configurar cada projeto.</p>
	<p>Um arquivo de configuração de projeto tem a seguinte estrutura:</p>
	<?php highlight_file('../examples/fixed2csv.xml'); ?>
	<p>Observe que algumas tags são obrigatórias sempre e que outras são obrigatórias em função da configurações de outras tags.</p>
	<h3>Tags obrigatórias</h3>
	<ul>
		<li>orion</li>
		<li>source_type</li>
		<li>column</li>
		<li>output</li>
	</ul>
	<h4>tag "orion"</h4>
	<p>É a tag pai de todas as demais tags.</p>
	<p>Possui o atributo opcional "name" que é o nome do projeto.</p>
	<h4>tag "source_type"</h4>
	<p>Especifica o tipo de arquivo do qual os dados serão extraídos.</p>
	<p>Use apenas "csv" ou "fixed" (minúsculo) para configurar como arquivo-fonte um arquivo separado por ";" ou de largura fixa (inclusive arquivos compatíveis).</p>
	<h4>tag "column"</h4>
	<p>Esta é a tag que configura o layout das colunas. Para cada coluna de dados, usa-se uma tag "column".</p>
	<p>A "column" possui os seguintes atributos:</p>
	<ul>
		<li>name: obrigatório sempre e carrega um nome de coluna. Recomenda-se não utilizar espaçoes ou caracteres especiais.</li>
		<li>label: opcional e recebe um rótulo de coluna. Utilizado como nome de coluna quando se converte para CSV. Se não informado, é utilizado "name".</li>
		<li>start: obrigatório quando "source_type" é "fixed" e representa o caractere de início do campo. A contagem de caracteres inicia no zero.</li>
		<li>length: obrigatório quando se utiliza "start" e representa o tamanho em caracteres do campo.</li>
		<li>sql: opcional e representa o nome de coluna utilizado quando a saída é em SQL. Se não informado, é utilizado "name".</li>
		<li>fn: opcional e recebe o nome de uma função pré-definida (em um dos arquivos do diretório "libs") que será aplicada sobre todos os valores desse campo.</li>
	</ul>
	<h4>tag "output"</h4>
	<p>Esta tag informa o tipo de saída dos dados. Pode ser "csv", "xml", "sql" ou "json", em minúsculas. Embora obrigatória, se não informada ou informado outro valor que não os permitidos, assume "xml".</p>
	
	<h3>Tags opcionais</h3>
	<p>Algumas tags são opcionais, independentemente do contexto.</p>
	<ul>
		<li>description: armazena uma descrição do projeto.</li>
		<li>filter: especifica o tipo de filtro de linha utilizado. Usada apenas se "source_type" for "fixed". Para mais detalhes, veja a seção específica desta tag.</li>
		<li>csv: armazena configurações sobre arquivos-fonte do tipo CSV e para saídas CSV. Veja a seção específica desta tag para mais detalhes.</li>
		<li>sql: armazena configurações para saídas SQL. Para detalhes, veja a seção específica também.</li>
		<li>special: armazena qualquer código PHP para aplicação no conjunto de dados extraídos. Não devem ser utilizadas tags de abertura e fechamento de código PHP (< ?php e ? >). Todo o trabalho do código inserido nesta tag deve ser feito sobre a variável $store (que armazena os dados extraídos, ainda não convertidos) e deve retornar uma variável $store ao final. Para saber mais detalhes sobre $store, consulte a documentação de XtPHP.</li>
	</ul>
	
	<h3>Usando a tag "filter"</h3>
	<p>A tag "filter" serve para especificar como o Orion irá selecionar as linhas para extração (no caso de "source_type" ser "fixed").</p>
	<p>"filter" aceita "none", "len", "regex" e "fn".</p>
	<ul>
		<li>none: nenhuma validação é feita, ou seja, todas as linhas serão consideradas.</li>
		<?php highlight_file('../examples/filter-none.xml'); ?>
		<li>len: somente aceita linhas cujo tamanho em caracteres corresponda ao inteiro informado no interior da tag "filter".</li>
		<?php highlight_file('../examples/filter-len.xml'); ?>
		<li>regex: no interior da tag "filter" deve-se colocar uma expressão regular compatível com a função do PHP eregi(). Todas as linhas serão testadas e aquelas que coincidirem com a expressão regular serão consideradas.</li>
		<?php highlight_file('../examples/filter-regex.xml'); ?>
		<li>fn: recebe um código PHP que recebe a linha inteira como parâmetro através da variável $row e deve retornar true ou false, aceitando ou rejeitando a linha.</li>
		<?php highlight_file('../examples/filter-fn.xml'); ?>
	</ul>
	
	<h3>Usando a tag "csv"</h3>
	<p>A tag "csv" configura como os arquivos CSV serão interpretados e estabelece a configuração de saída CSV.</p>
	<p>O atributo "delimiter" recebe o delimitador de campos, geralmente ";".</p>
	<p>O interior da tag recebe o caractere qualificador de texto, geralmente aspas. Deve-se colocar apenas um caracter, por exemplo, se os valores de texto estiverem delimitados por aspas-duplas (""), coloque apenas uma aspa-dupla (").</p>
	
	<h3>Usando a tag "sql"</h3>
	<p>A tag "sql" con figura a saída em SQL.</p>
	<p>O atributo "table" é obrigatório e recebe o nome da tabela SQL.</p>
	<p>O atributo "direct" deve receber true ou false.</p>
	<p>Se "direct" for false, a saída oferecida pelo Orion será um array onde cada elemento é uma string INSERT.</p>
	<p>Porém, se "direct" for true, o Orion irá inserir os dados num banco Mysql, desde que os seguintes atributos também estejam configurados:</p>
	<ul>
		<li>host: host do servidor Mysql.</li>
		<li>user: usuário para conexão ao Mysql.</li>
		<li>password: senha do usuário Mysql.</li>
		<li>dbname: nome do banco de dados.</li>
	</ul>
	
	<h3>Usando a tag "special"</h3>
	<p>A tag "special" é uma espécie de coringa. Nela pode ser escrito código para dar um tratamento final ao conjunto de dados, antes de ser convertido na saída final.</p>
	<p>Esta tag é ideal para fazer coisas do tipo: adicionar ou excluir colunas, ordenar os dados ou modificar o conteúdo dos dados, por exemplo.</p>
	<p>Embora deixar que qualquer tipo de código seja executado seja uma "falha de segurança", Orion foi desenvolvido para dar flexibilidade à extração e conversão de dados, por isso, a funcionalidade de "special" é necessária.</p>
	<p>Ao utilizar "special", tenha em mente que os dados estão armazenados no array bidimensional $store. Esse array tem a primeira dimensão representando as linhas, cujas chaves são numéricas. A segunda dimensão representa as colunas e as chaves são os nomes das colunas indicados no atributo "name" da tag "column".</p>
	<p>Atente para a necessidade de que todo o código deve resultar em uma variável $store válida.</p>
	
</body>

</html>
