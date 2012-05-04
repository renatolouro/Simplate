<?php
/**
 * @desc Interface that specifies the methods of an iterator class. 
 * @author Renato da Silva Louro - @rslouro <renato@silostecnologia.com.br>
 */
interface IIterator
{
    function next();
    function hasNext();
    function close();
}


?>