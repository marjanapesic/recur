<?php 
namespace Recur\Filter;

/**
 * Day filter allows creating recurring type with repetition on selected days in week
 *
 * @author Marjana
 */
class Day implements Filter{
    
    /**
     * @var boolean
     */
    protected $mo;
    
    /**
     * @var boolean
     */
    protected $tu;
    
    /**
     * @var boolean
     */
    protected $we;
    
    /**
     * @var boolean
     */
    protected $th;
    
    /**
     * @var boolean
     */
    protected $fr;
    
    /**
     * @var boolean
     */
    protected $sa;
    
    /**
     * @var boolean
     */
    protected $su;
    
    
    public function __construct($mo, $tu, $we, $th, $fr, $sa, $su)
    {
       $this->mo = (bool)$mo;
       $this->tu = (bool)$tu;
       $this->we = (bool)$we;
       $this->th = (bool)$th;
       $this->fr = (bool)$fr;
       $this->sa = (bool)$sa;
       $this->su = (bool)$su;
    }
    
    /**
     * @return boolean
     */
    function filter(\DateTime $date){
        switch($date->format('N')){
            case 1:
                return $this->mo;
            case 2:
                return $this->tu;
            case 3:
                return $this->we;
            case 4:
                return $this->th;
            case 5:
                return $this->fr;
            case 6:
                return $this->sa;
            case 7:
                return $this->su;
        }
    }
    
    /**
     * @return boolean
     */
    public function isMo(){
        return $this->mo;
    }
    
    /**
     * @return boolean
     */
    public function isTu(){
        return $this->tu;
    }
    
    /**
     * @return boolean
     */
    public function isWe(){
        return $this->we;
    }
    
    /**
     * @return boolean
     */
    public function isTh(){
        return $this->th;
    }
    
    /**
     * @return boolean
     */
    public function isFr(){
        return $this->fr;
    }
    
    /**
     * @return boolean
     */
    public function isSa(){
        return $this->sa;
    }

    /**
     * @return boolean
     */
    public function isSu(){
        return $this->su;
    }
}
?>