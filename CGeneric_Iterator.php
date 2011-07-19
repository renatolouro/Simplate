<?php
/******************************************************************************
 *$AI Módulo de Implementação
 *    Nome:                   Classe CGeneric_Iterator
 *    Copyright/Proprietário: 2010-2011 / Silos Software e Tecnologia da
 *                               Informacao LTDA ME
 *    Projeto:                Silos Web Framework     
 *    Gestor do Arquivo:      Renato da Silva Louro (@rslouro) 
 *                               renato@silostecnologia.com.br
 *    Arquivo:                CGeneric_Iterator.php
 *    Identificação:          SPL
 *    Versão corrente:        00.001 Alfa
 *    Data de Aprovação:      2011/02/07
 *    Licença: GNU General    Public License, version 3 (GPLv3)
 *    
 *    Autor(es): 
 *       Diego da Costa Chavão (@Chavao)  chavao@silostecnologia.com.br
 *       Lucas Souza - (@LucasZeta) lucas@silostecnologia.com.br
 *       
 *$ED Descrição da Classe
 *   Iterador sobre Arrays
 *   Descrição dos métodos:
 *      next    - Retorna o próximo valor do array e avança o ponteiro;
 *      hasNext - Retorna:
 *         - true  - caso exista um próximo valor; e
 *         - false - caso não exista um próximo valor. 
 *      close - deve ser chamado ao final da iteração. Libera recursos alocados
 *         caso existam.
 *      rewind - Volta com o ponteiro para a posição inicial do array.
 *****************************************************************************/
require_once('IIterator.php');

class CGeneric_Iterator implements IIterator
{
	private $p_arrObj;
	private $p_obj;

	function CGeneric_Iterator($parrObj)
	{
		$this->p_arrObj = $parrObj;
		     
		if(is_array($this->p_arrObj)) $this->p_obj=reset($this->p_arrObj);
	}

	function next()
	{
		$objReturn = $this->p_obj;
		     
		if(is_array($this->p_arrObj)) $this->p_obj=next($this->p_arrObj);
		return $objReturn;
	}
	
	function rewind()
	{
		if(is_array($this->p_arrObj)) $this->p_obj=reset($this->p_arrObj);
	}

	function hasNext()
	{
		return $this->p_obj!=false;
	}

	function close()
	{
		unset($this->p_arrObj);
	}

}?>