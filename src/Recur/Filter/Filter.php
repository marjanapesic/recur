<?php 
namespace Recur\Filter;

/**
 * Filter interface
 *
 * @author Marjana
 */
interface Filter{
    
    function filter(\DateTime $date);
    
}
?>