<?php
/**
 * @desc Iterator over Arrays
 * @author Diego Chavão - @Chavao <fale@chavao.net> 
 * @author Lucas Souza - @LucasZeta <lucas@silostecnologia.com.br>
 */
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

    /**
     * @desc Get the current value of the array and forwards the pointer.
     * @return object Object with the current value of the array of objects.
     */
    function next()
    {
        $objReturn = $this->p_obj;

        if(is_array($this->p_arrObj)) $this->p_obj=next($this->p_arrObj);
        return $objReturn;
    }

    /**
     * @desc Returns the pointer to the first element of the array.
     */
    function rewind()
    {
        if(is_array($this->p_arrObj)) $this->p_obj=reset($this->p_arrObj);
    }

    /**
     * @desc Verifies if the iterator has a next element in the array.
     * @return boolean
     */
    function hasNext()
    {
        return $this->p_obj!=false;
    }

    /**
     * @desc Should to call close() at the end of iterator. Flushes resources.
     */
    function close()
    {
        unset($this->p_arrObj);
    }

}?>