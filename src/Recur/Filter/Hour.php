<?php 
namespace Recur\Filter;

/**
 * Hour filter allows creating recurring type with repetition on selected hours
 *
 * @author Marjana
 */
class Hour implements Filter{
    
    /**
     * @var Array
     */
    protected $hours;
    
    public function __construct(Array $hours)
    {
       $this->hours = $hours;
    }
    
    /**
     * @return boolean
     */
    function filter(\DateTime $date){
        $hour = $date->format('H');            
        return in_array($hour, $this->hours);
    }
    
    /**
     * @return Array
     */
    function getHours(){
        return $this->hours;
    }
}
?>