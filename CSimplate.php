<?php
/******************************************************************************
 *$AI Módulo de Implementação
 *    Nome:                   Classe Simplate
 *    Copyright/Proprietário: 2010-2011 / Silos Software e Tecnologia da
 *                               Informacao LTDA ME
 *    Projeto:                Silos Web Framework
 *    Gestor do Arquivo:      Renato da Silva Louro (@rslouro)
 *                               renato@silostecnologia.com.br
 *    Arquivo:                CSimplate.php
 *    Identificação:          SPL
 *    Versão corrente:        00.001 Alfa
 *    Data de Aprovação:      2011/02/07
 *    Licença: GNU General    Public License, version 3 (GPLv3)
 *    Copyright: 2010-2011    Renato da Silva Louro
 *
 *    Autor: Renato da Silva Louro (@rslouro) renato@silostecnologia.com.br
 *    Colaboradores:
 *       Diego da Costa Chavão (@chavao)  chavao@silostecnologia.com.br
 *          - Implementações
 *          - Acertos de Faltas
 *          - Autoria e Implementação da função show
 *       Lucas Souza - (@LucasZeta) lucas@silostecnologia.com.br
 *          - Implementações
 *          - Acertos de Faltas
 *          - Autoria e Implementação da função show
 *
 *****************************************************************************/

/*****************************************************************************
 *$HA Alteração
 *   Versão: 0.002 Alfa (REV.130)
 *   Data: 2011/02/11
 *   Autor: @rslouro
 *   Tipo:Evolutiva
 *   Solicitação: Definição de configuração padrão  para o Scope.
 *$ED
 *   comandShow()
 *      Padrão do scope=outer para o comando show
 *    mountPhpNode(...)
 *      Padrão do scope=inner para o comando bid
 *
 */

/*****************************************************************************
 *$HA Alteração
 *   Versão: 0.002 Alfa (REV.129)
 *   Data: 2011/02/08
 *   Autor: @chavao
 *   Tipo: Corretiva
 *   Solicitação: scopo inner e alter trocados.ao utilizar o comando
 *   show.
 *$ED
 *   comandShow()
 *      Correção de carga de objeto auxiliar de acordo com o scope
 */

require_once('lib/simple_html_dom.php');

/*
 *
 *****************************************************************************/
/******************************************************************************
 *$CC Classe Simplate
 *$ED Descrição da Classe
 *   Classe Principal da Enginie Simplate PHP. Em resumo esta classe:
 *   1 - Recebe como entrada um ou mais templates HMTL;
 *   2 - Processa - Compila ou Interpreta - os comandos embutidos no(s)
 *       template(s)
 *   3 - Gera um Arquivo cache - no caso de compilação - ou exibe o resultado
 *       - no caso de interpretação.
 *
 *$EU Modo de Utilizar a Classe
 *   Na maioria dos casos, para a utilização desta Classe são necessários 3
 *   passos:
 *   1- Criar uma instancia da Classe, ver detalhes na função construtora;
 *   2- Chamar a função record do objeto criado. Isto fará criar ou atualizar o
 *      arquivo cache se for o caso;
 *   3- Chamar/Executar o arquivo cache com, por exemplo, o comando
 *      require_once do php
 *
 *   Exemplo:
 *      $objSimp=new CSimplate("meu_template.html", "cache_gerado.php");
 *      $objSimp->record();
 *      require_once("cache_gerado.php");
 *
 *$EH Hipóteses Assumidas pela Classe
 *   Espera-se que exista permissão de escrita no diretório dos arquivos cache
 *      e/ou em seus arquivos para o usuário responsável pela execução do
 *      script PHP.
 *   Espera-se que seja definida, antes da execução da instrução record, a
 *   constante booleana SPL_FORCE.
 *****************************************************************************/
class CSimplate
{
    public  $m_sHtmlPath  = null;
    private $p_sPhpPath   = null;
    private $p_splMaster  = null;
    private $p_sEntryPoint = null;
    private $p_sCurrObjName = null;
    private static $p_iCounter = 0;

    /******************************************************************************
     *$FC CSimplate Método Construtor da Classe Simplate
     *
     *$EP Parâmetros da Função
     *$P $psHtmlPath Caminho físico do arquivo template
     *      Ao Entrar: String apontando para o arquivo pré-existente template.
     *         Parametro obrigatório.
     *$P $psPHPPath Caminho físico do arquivo cache
     *      Ao Entrar: String apontando para o arquivo cache sendo este
     *         pré-existente ou não. Parametro obrigatório.
     *$P $psplMaster Objeto Simplate da Masterpage
     *      Ao Entrar: Objeto Simplate da Masterpage
     *         Parametro Opcional.
     *$P $psEntryPoint Identificador do Ponto de Entrada
     *      Ao Entrar: Identificador (ID) das tags do template e Masterpage que
     *         marca o ponto de inclusão do template na masterpage.
     *         Se $psplMaster<>null: Parâmetro obrigatório string<>""
     *         Se $psplMaster==null: NULL ou ""

     *****************************************************************************/

    function CSimplate($psHtmlPath, $psPHPPath=null, $psplMaster=null, $psEntryPoint="")
    {

        /******************************************************************************
         * Configuração do Módulo
         */
        if (!defined('SPL_FORCE')) define('SPL_FORCE', false);
        if (!defined('SPL_PROJECT_NAME')) define('SPL_PROJECT_NAME','Silos Framework');

        $this->m_sHtmlPath  = $psHtmlPath;
        $this->p_sPhpPath   = $psPHPPath;
        $this->p_splMaster =  $psplMaster;
        $this->p_sEntryPoint = $psEntryPoint;
    }


    /**********************
     * Verifica a necessidade de gerar ou regerar o cache,
     * se sim:
     *    Cria cabeçalho do arquivo
     *    Chama a compilação do template
     *    Chama a compilação da masterpage se existente
     *    Cola o template compilado dentro da masterpage compilada tendo como base
     *       p_sEntryPoint
     *    Salva o resultado em p_sPhpPath
     */
    function record()
    {
        if ((SPL_FORCE == true) || (!file_exists($this->p_sPhpPath)) || (filemtime($this->p_psHtmlPath) > filemtime($this->p_sPhpPath)))
        {
            $sComment  = "<?php \n";
            $sComment .= "//***************************************** \n";
            $sComment .= "//\$AI Módulo de implementação.\n";
            $sComment .= "//\tARQUIVO GERADO. Não edite este arquivo. \n";
            $sComment .= "//\tGerado a partir de: ".__FILE__." \n";
            $sComment .= "// \n";
            $sComment .= "//\tProprietário: Silos \n";
            $sComment .= "//\tProjeto: ".SPL_PROJECT_NAME." \n";
            $sComment .= "//\tArquivo:".$this->p_sPhpPath." \n";
            $sComment .= "//\$. ************************************** \n";
            $sComment .= "?> \n";

            $objDomHtml = $this->compile();
            if (isset($this->p_splMaster))
            {
                $objDomMaster = $this->p_splMaster->compile();

                $tagMasterEntryPoint = $objDomMaster->getElementById($this->p_sEntryPoint);
                $tagThisEntryPoint =   $objDomHtml->getElementById($this->p_sEntryPoint);
                $tagMasterEntryPoint->outertext = $tagThisEntryPoint;

                $objDomMaster->root->innertext = $sComment.$objDomMaster->root->outertext;
                $objDomMaster->save($this->p_sPhpPath);
            }
            else
            {
                if($this->p_sEntryPoint!="")
                {
                    $tagThisEntryPoint = $objDomHtml->getElementById($this->p_sEntryPoint);

                    $objDomHtml->root->innertext = $tagThisEntryPoint->outertext;
                }
                $objDomHtml->root->innertext = $sComment.$objDomHtml->root->outertext;
                $objDomHtml->save($this->p_sPhpPath);
            }
        }
        return $this->p_sPhpPath;
    }

    function compile()
    {
        $objHtml = file_get_html($this->m_sHtmlPath);
        $this->mountPhpPage($objHtml);
        return ($objHtml);
    }


    /******************************************************************************
     *  Compila a página template ou o DOM em forma de objeto em página php. Função
     *  recursiva: Acessa cada um dos nós filhos chamando a função mountPhpNode
     *  que compila cada um dos nós em separado. Depois acessa cada um dos filhos
     *  do nó para recursivamente, compilar os nós internos.
     *****************************************************************************/
    function mountPhpPage(&$pobjDom=null, $pideep=0, $pbFlag=false)
    {
        //if(!isset($pobjDom)) $pobjDom=file_get_html($this->m_sHtmlPath);

        if ($pobjDom instanceof simple_html_dom_node)
        {
            if($pbFlag) return $this->mountPhpNode($pobjDom, $pideep, $pbFlag);
            $this->mountPhpNode($pobjDom, $pideep, $pbFlag);
        }
        foreach ($pobjDom->childNodes() as $childNode)
        {
            if($pbFlag) return $this->mountPhpPage($childNode, $pideep, $pbFlag);
            $this->mountPhpPage($childNode, $pideep, $pbFlag);
        }
    }

    /******************************************************************************
     *$FC mountPhpNode Compila o objeto nó em php+html
     *
     *$ED Descrição da Função
     *    Compila o objeto nó corrente retirando as marcações do Simplate e inserindo
     *    comandos em PHP no corpo do nó. Também compila os filhos existentes deste
     *    mesmo nó através de chamadas recursivas.
     *
     *$EP Parâmetros da Função
     *$P $pobjNode Objeto representando um Nó da estrutura DOM
     *      Ao Entrar: Objeto não compilado, isto é, html + comandos do Simplate
     *      Ao Sair:   Objeto já compilado, isto é, html + comandos PHP
     *$P $pideep Profundidade
     *      Ao Entrar: Indica a profundidade em termos de recursão desta instância
     *         de função. Utilizado para marcar as váriaveis do código compilado
     *         evitando 'colisão' entre as mesmas.
     *$P $pbFlag Marcador de Retorno
     *      Ao Entrar: Indica se a função apenas modificará o $objNode ou se
     *         o código compilado pelo comando return.
     *         False - Apenas modificará o objeto $objNode.
     *         True  - Também retornará o código compilado via 'return'
     *****************************************************************************/
    function mountPhpNode(&$objNode, $pideep=0, $pbFlag=false)
    {
        $pideep++;
        $this->p_iCounter++;

        $sScript = "";

        if(isset($objNode->scope)) $sScope = $objNode->scope;
        $objNode->__unset('scope');

        if ($objNode->__isset('show')) $this->commandShow($objNode, $sScope);

        /* ****** INICIO COMANDO BIND ********** */
        if(!$sScope) $sScope='inner';//Valor Padrão do Scope no Bind
        if ($objNode->__isset('bind'))
        {
            $this->p_sCurrObjName=$objNode->bind;
            $sCurrObjName=$this->p_sCurrObjName;
            $objNode->__unset('bind');

            if (strtolower($this->p_sCurrObjName)=='fake')
            {
                $objNode->outertext = '';
                return;
            }

            if(isset($objNode->as)) $sScript .= $this->commandBindAs($objNode, $pideep, $sScope);
            else $sScript .= $this->commandBind($objNode, $pideep);

            if($sScope!="tag") $objNode->innertext = $sScript;
            $temScape=$this->replaceAttributes($objNode, 'temp_'.$pideep);
            if($sScope=="outer")
            {
                if(!$temScape) $objNode->outertext = '<?php $temp_'.$pideep.'=$'.$sCurrObjName.';?>'.$sScript;
                else $objNode->outertext = '<?php $temp_'.$pideep.'=$'.$sCurrObjName.';if ($temp_'.$pideep.'){ ?>'.$objNode->outertext.'<?php } ?>';
            }
            else $objNode->outertext = '<?php $temp_'.$pideep.'=$'.$sCurrObjName.';if ($temp_'.$pideep.'){ ?>'.$objNode->outertext.'<?php } ?>';
        }

        if($pbFlag) return $sScript;
    }

    /******************************************************************************
     *$FC replaceAttributes Método Construtor da Classe Simplate
     *
     *$EP Parâmetros da Função
     *$P $pobjNode Objeto representando um Nó da estrutura DOM
     *      Ao Entrar: Nó com atributos ainda não processados
     *      Ao Sair:   Nó contendo seus atributos já processados
     *$P $psObject Nome 'String' do objeto corrente em bind
     *****************************************************************************/
    function replaceAttributes($pobjNode, $psObject)
    {
        $temScape=false;
        $arrParam = $pobjNode->getAllAttributes();
        foreach($arrParam as $sParam => $sValue)
        {
            if(substr($sParam,0,1)=="#")
            {
                $temScape=true;
                $sParam = substr($sParam,1);
                $pobjNode->$sParam='<?php if(is_object($'.$psObject.')) if(is_object($'.$psObject.'->'.$sParam.')) echo($'.$psObject.'->'.$sParam.'->'.$sParam.'); else echo($'.$psObject.'->'.$sParam.'); else if (!is_object($'.$psObject.') || method_exists($'.$psObject.', \'__toString\')) echo($'.$psObject.'); ?>';
                $pobjNode->__unset("#".$sParam);
            }
        }
        return $temScape;
    }

    function commandShow($pobjNode, $psScope)
    {
        if(!$psScope) $psScope='outer';//Valor Padrão do Scope no Show

        $sCurrObjName=$pobjNode->show;
        $pobjNode->__unset('show');

        $objAux = new simple_html_dom();
        if($psScope=="outer") $objAux->load($pobjNode->outertext);
        else if($psScope=="inner") $objAux->load($pobjNode->innertext);

        $this->mountPhpPage($objAux,$pideep);
        $sAux='<?php if(is_object($'.$sCurrObjName.')) { ?>'."\n".$objAux->outertext."<?php } ?> \n";
        if($psScope=="outer") $pobjNode->outertext = $sAux;
        else if($psScope=="inner") $pobjNode->innertext = $sAux;
        else
        {
            $objAux->innertext="?>".$objAux->innertext."<?php if(is_object($'.$sCurrObjName.')) { ?>";
            $sAux='<?php if(is_object($'.$sCurrObjName.')) { ?>'."\n".$objAux->outertext."<?php } ?> \n";
            $pobjNode->outertext = $sAux;
        }
    }

    /******************************************************************************
     *$FC commandBind  Compilador do Comando Bind
     *
     *$ED Descrição da Função
     *   Monta o Script compilado para tratamento do comando Bind quando este vem sem
     *   o parâmetro 'as'.
     *
     *$EP Parâmetros da Função
     *$P $pobjNode Objeto nó Corrente
     *      Ao Entrar: Nó não compilado.
     *$P $pideep Profundidade
     *      Ao Entrar: Indica a profundidade em termos de recursão desta instância
     *         de função. Utilizado para marcar as váriaveis do código compilado
     *         evitando 'colisão' entre as mesmas.
     *****************************************************************************/
    function commandBind($pobjNode, $pideep)
    {
        $sScript="";
        $sScript  .= '<?php '."\n";
        $sScript  .= '   if ($temp_'.$pideep.'){ '."\n"; //Se a variável for null ou false retorna vazio
        $sScript  .= '   if (!is_object($temp_'.$pideep.') || method_exists($temp_'.$pideep.', \'__toString\')) '."\n";
        $sScript  .= '   {?> ';
        $sScript  .= '      <?=$temp_'.$pideep.' ?> '."\n";
        $sScript  .= '<?php } '."\n";
        $sScript  .= '   else if (is_a($temp_'.$pideep.', "CSimplate")) '."\n";
        $sScript  .= '   { '."\n";
        $sScript  .= '      $file = $temp_'.$pideep.'->record();'."\n";
        $sScript  .= '      if(is_file($file)) {'."\n";
        $sScript  .= '          require($file); '."\n";
        $sScript  .= '      }'."\n";
        $sScript  .= '   } '."\n";
        $sScript .= ' else { ?>'.$pobjNode->innertext.'<?php }} ?>'."\n";
        return $sScript;
    }

    /******************************************************************************
     *$FC commandBindAs  Compilador do Comando Bind + As
     *
     *$ED Descrição da Função
     *    Monta o Script compilado para tratamento do Loop com Iterator identificado
     *    pelo parâmetro 'as' na tag
     *
     *$EP Parâmetros da Função
     *$P $pobjNode Objeto nó Corrente
     *      Ao Entrar: Nó e seus filhos ainda não compilados, contendo o parâmetro 'as'
     *         definido e, possivelmente, o parâmetro 'limit'.
     *      Ao Sair: Objeto nó corrente com os filhos já compilados e com os
     *      parâmetros 'as' e 'limit'
     *$P $psScope Escopo
     *      Ao Entrar:
     *         - inner - indica que apenas o conteúdo do nó serão repetidos no loop;
     *         - outer - indica que todo o nó será repetido no loop.
     *$P $pideep Profundidade
     *      Ao Entrar: Indica a profundidade em termos de recursão desta instância
     *         de função. Utilizado para marcar as váriaveis do código compilado
     *         evitando 'colisão' entre as mesmas.
     *****************************************************************************/
    function commandBindAs($pobjNode, $pideep, $psScope='inner')
    {
        $sAs = $pobjNode->as;
        $pobjNode->__unset('as');

        if(isset($pobjNode->limit))
        {
            $iLimit = $pobjNode->limit;
            $pobjNode->__unset('limit');
            $iItr = reset(explode(",",$iLimit));
            $iLimit = end(explode(",",$iLimit));
        }

        $sScript="<?php ";
        $sScript  .= '   $temp_'.$pideep.'=$'.$this->p_sCurrObjName.";\n";
        $sScript  .= '   if ($temp_'.$pideep.'){ '."\n"; //Se a variável for null ou false retorna vazio
        $sScript  .= '   if (is_a($temp_'.$pideep.', "IIterator")) '."\n";
        $sScript  .= '   { '."\n";
        $sScript  .= '      $objIt'.$pideep."_".$this->p_iCounter.'=$temp_'.$pideep.";\n";
        if($iLimit)
        {
            $sScript .= "for(\$i=0;\$i<".$iLimit.";++\$i){\n";
            $sScript .= '	if($objIt'.$pideep."_".$this->p_iCounter.'->hasNext()){'."\n";
        }
        else
        {
            $sScript  .= '      while($objIt'.$pideep."_".$this->p_iCounter.'->hasNext())'."\n";
            $sScript  .= '      {'."\n";
        }
        $sScript  .= '         $'.$sAs.'=$objIt'.$pideep."_".$this->p_iCounter.'->next();'."\n";
        $this->mountPhpPage($pobjNode, $pideep);
        $sScript .= '?>'."\n";
        $this->replaceAttributes($pobjNode, $sAs);

        if($psScope=='outer') $sScript .=$pobjNode->outertext;
        else $sScript .= $pobjNode->innertext;
        if($iLimit)
        {
            $sScript  .= ' <?php } ?>'."\n";
        }
        $sScript .= ' <?php } ?>'."\n";
        $sScript .= ' <?php } }?>'."\n";
        return $sScript;
    }

}
?>