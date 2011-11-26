<?php
/******************************************************************************
 *$AD Módulo de Declaração
 *    Copyright/Proprietário: 2010-2011 / Silos Software e Tecnologia da
 *                            Informacao LTDA ME
 *    Projeto:                Silos Web Framework
 *    Gestor:                 Renato da Silva Louro (@rslouro)
 *                               renato@silostecnologia.com.br
 *    Arquivo:                CSimplate.php
 *    Identificação:          SPL
 *    Versão corrente:        00.001 Alfa
 *    Data de Aprovação:      NÃO APROVADO
 *    Licença: GNU General    Public License, version 3 (GPLv3)
 *    Copyright: 2010-2011    Renato da Silva Louro
 *
 *    Autor: Renato da Silva Louro (@rslouro) renato@silostecnologia.com.br
 *
 *$ED Descrição da Interface
 *   Interface que define os métodos obrigatórios de uma classe iteradora.
 *      No Simplate um Iterador pode ser utilizado no comando bind/as para gerar
 *      a iteração (repetição). Pode ser utilizada inclusive para interfacear um
 *      resultset do banco de dados.
 *   Descrição dos métodos:
 *      next    - Retorna o próximo valor e avança o ponteiro;
 *      hasNext - Retorna:
 *         - true  - caso exista um próximo valor; e
 *         - false - caso não exista um próximo valor.
 *       template(s)
 *      close - deve ser chamado ao final da iteração. Libera recursos alocados
 *         caso existam.
 *****************************************************************************/
interface IIterator
{
    function next();
    function hasNext();
    function close();
}


?>