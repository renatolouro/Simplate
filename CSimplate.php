<?php

require_once('lib/simple_html_dom.php');

/**
 * @desc Simplate parser class.
 * @author Renato da Silva Louro - @rslouro <renato@silostecnologia.com.br>
 * @author Diego Chavão - @Chavao <fale@chavao.net>
 * @author Lucas Souza - @LucasZeta <lucas@silostecnologia.com.br> 
 */
class CSimplate
{
    public  $m_sHtmlPath  = null;
    private $p_sPhpPath   = null;
    private $p_splMaster  = null;
    private $p_sEntryPoint = null;
    private $p_sCurrObjName = null;
    private static $p_iCounter = 0;

    /**
     * @desc Constructor of Simplate class.
     * @param string $psHtmlPath Path to the Simplate file.
     * @param string $psPHPPath Path to the PHP generated cache.
     * @param string $psplMaster Masterpage Simplate object.
     * @param string $psEntryPoint Entry point to template and masterpage.
     */
    function CSimplate($psHtmlPath, $psPHPPath=null, $psplMaster=null, $psEntryPoint="")
    {
        /* Module configuration. */
        if (!defined('SPL_FORCE')) define('SPL_FORCE', false);
        if (!defined('SPL_PROJECT_NAME')) define('SPL_PROJECT_NAME','Silos Framework');

        $this->m_sHtmlPath  = $psHtmlPath;
        $this->p_sPhpPath   = $psPHPPath;
        $this->p_splMaster =  $psplMaster;
        $this->p_sEntryPoint = $psEntryPoint;
    }

    /**
     * @desc Performs record the PHP generated cache. If masterpage exists, compiles and merge them to one file.
     * @return string HTML compiled 
     */
    function record()
    {
        if ((SPL_FORCE == true) || (!file_exists($this->p_sPhpPath)) || (filemtime($this->p_psHtmlPath) > filemtime($this->p_sPhpPath)))
        {
            $sComment  = "<?php \n";
            $sComment .= "/** \n";
            $sComment .= " * FILE GENERATED! Don't edit this file! \n";
            $sComment .= " * Generated from: ".__FILE__." \n";
            $sComment .= " * Project: ".SPL_PROJECT_NAME." \n";
            $sComment .= " * File: ".$this->p_sPhpPath." \n";
            $sComment .= " */ \n";
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

    /**
     * @desc Performs the Simplate compilation.
     * @return object DOM object compiled. 
     */
    function compile()
    {
        $objHtml = file_get_html($this->m_sHtmlPath);
        $this->mountPhpPage($objHtml);
        return ($objHtml);
    }

    /** 
     * @desc Parse DOM object and delegates to mount the nodes.
     * @param simple_html_dom_node $pobjDom DOM object
     * @param integer $pideep
     * @param booleann $pbFlag
     * @return string PHP script to be saved. 
     */
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

    /**
     * @desc Compiles the DOM object in a PHP and HTML result.
     * @param object $objNode DOM node
     * @param integer $pideep
     * @param booleand $pbFlag
     * @return string PHP script to be saved. 
     */
    function mountPhpNode(&$objNode, $pideep=0, $pbFlag=false)
    {
        $pideep++;
        $this->p_iCounter++;

        $sScript = "";

        if(isset($objNode->scope)) $sScope = $objNode->scope;
        $objNode->__unset('scope');

        if ($objNode->__isset('show')) $this->commandShow($objNode, $sScope);

        /* Command bind */
        if(!$sScope) $sScope='inner'; // Default value to the scope (bind)
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

    /**
     * @desc Replaces the keyword of the Simplate to the object value.
     * @param object $pobjNode DOM node
     * @param string $psObject
     * @return boolean 
     */
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

    /**
     * @desc Command show compiler.
     * @param object $pobjNode DOM node.
     * @param string $psScope Scope to perform compilation.
     */
    function commandShow($pobjNode, $psScope)
    {
        if(!$psScope) $psScope='outer'; // Default value to the scope (show)

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

    /**
     * @desc Command bind compiler.
     * @param object $pobjNode DOM node.
     * @param integer $pideep
     * @return string PHP script
     */
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

    /**
     * @desc Command bind + as compiler.
     * @param object $pobjNode DOM node
     * @param integer $pideep
     * @param string $psScope Scope to perform compilation.
     * @return string PHP script
     */
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
        $sScript  .= '   if ($temp_'.$pideep.'){ '."\n"; // If the variable is null or false, returns void.
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