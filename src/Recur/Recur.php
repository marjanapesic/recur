<?php 
namespace Recur;

/**
 * Recur resource is used to set up recurring rule. Recur resource allows recurring
 * rules based on iCalendar (http://www.ietf.org/rfc/rfc5545.txt) with restrictions
 * specified bellow.
 * 
 * For instantiation of recur object configuration array is used. Keys of array are
 * rule recurrence rule parts. Possible rule parts (config array keys) are:
 * 
 * 'FREQ' => freq ; required
 * 'UNTIL' => date-time ; optional
 * 'COUNT' => DIGIT ; optional
 * 'INTERVAL' => DIGIT ; optional
 * 'BYHOUR' => byhrlist ; optional
 * 'BYDAY' => bywdaylist ; optional
 * 
 * Possible values can be:
 * freq = "HOURLY"|"DAILY"|"WEEKLY"
 * byhrlist = hour *("," hour)
 * hour = 1*2DIGIT       ;0 to 23
 * bywdaylist  = weekday *("," weekday)
 * weekday = "SU" / "MO" / "TU" / "WE" / "TH" / "FR" / "SA"
 * 
 * For detailed description of how rules are evaluated please refer to
 * http://www.ietf.org/rfc/rfc5545.txt
 *
 * @author Marjana
 */
class Recur {
    
    /**
     * @var \Recur\Frequency\AbstractFrequency
     */
    protected $freq = null;
    
    /**
     * @var \DateTime until limit
     */
    protected $until = null;
    
    /**
     * @var int count limit
     */
    protected $count = null;
    
    /**
     * @var int frequency interval
     */
    protected $interval = 1;
    
    /**
     * @var String byday rule value
     */
    protected $byday;
    
    /**
     * @var String byhour rule value
     */
    protected $byhour;
    
    /**
     * @var Array day filters
     */
    protected $dayFilters = array();
    
    /**
     * @var Array hour filters
     */
    protected $hourFilters = array();
    

    public function __construct(Array $config)
    {          
        $configLowCase = array_change_key_case($config, CASE_LOWER);

        if(!count($config) || (count($config) != count($configLowCase)) || !isset($configLowCase['freq']))
        {
            throw new Exception\InvalidRuleConfig();
        }
       
        $properties = get_object_vars($this);
        
        foreach ($configLowCase as $conf_key => $conf_value) {

            if(array_key_exists(strtolower($conf_key), $properties)){
                $setter = 'set'.ucfirst($conf_key);
                $this->$setter($conf_value);
                unset($properties[strtolower($conf_key)]);
            }
            else
                throw new Exception\InvalidRuleConfig('Duplicate or invalid key');
        }

        $this->validate();
    }

    /**
     * Set frequency field depending on its config represenation
     * 
     * @param string $freq - Config text of frequency ('hourly', 'daily', 'weekly')
     *                      
     */
    private function setFreq($freq)
    {
        $freq = strtoupper($freq);
        switch ($freq) {
            case "DAILY":
                $this->freq = new \Recur\Frequency\Daily($this->interval);
                break;
            case "WEEKLY":
                $this->freq = new \Recur\Frequency\Weekly($this->interval);
                break;
            case "HOURLY":
                $this->freq = new \Recur\Frequency\Hourly($this->interval);
                break;
            default:
                throw new Exception\InvalidRuleConfig('Invalid frequency.');
        }
    }

    /**
     * Returns frequency object
     *
     * @return \Recur\Frequency\AbstractFrequency
     */
    public function getFreq()
    {
        return $this->freq;
    }
    
    /**
     * Set until field depending on its config represenation
     *
     * @param string $until
     *
     */
    private function setUntil(\DateTime $until)
    {
        if(isset($this->count))
        {
            throw new Exception\InvalidRuleConfig('Count limit and until limit specified.');
        }
        
        $this->until = $until;
    }
    
    /**
     * @return \DateTime
     */
    public function getUntil()
    {
        return $this->until;
    }
    
    
    /**
     * Set count field depending on its config represenation
     *
     * @param int $until
     *
     */
    private function setCount($count)
    {
        if(isset($this->until) || (int)$count <= 0)
        {
            throw new Exception\InvalidRuleConfig('Invalid config rule or count and until rule specified.');
        }
        
        $this->count = (int)$count;
    }
    
    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }
    
    
    /**
     * Set interval field depending on its config represenation
     *
     * @param int $interval
     *
     */
    private function setInterval($interval)
    {
        if((int)$interval <= 0)
        {
            throw new Exception\InvalidRuleConfig('Invalid interval.');
        }
        
        if(isset($this->freq)){
            $this->freq->setInterval((int)$interval);
        }
        
        $this->interval = (int)$interval;
    }
    
    /**
     * @return int
     */
    public function getInterval()
    {
        return $this->interval;
    }
    
    /**
     * Set byday field depending on its config represenation
     * If byday filter is present adds coresponding \Recur\Filter\AbstractFilter
     * to dayFilter array
     *
     * @param Array $byday
     *
     */
    private function setByday($byday)
    {
        if(!is_string($byday))
            throw new Exception\InvalidRuleConfig('By day rule must be string.');
        
        $byday = array_map('trim', array_filter(explode(',', strtoupper($byday))));
        
        $byday = array_unique($byday);
        
        foreach($byday as $one){
            if(!preg_match('/^(SU|MO|TU|WE|TH|FR|SA)$/',$one))
                throw new Exception\InvalidRuleConfig('Invalid by day rule specification.');
        }

        if($byday){
            $this->byday = $byday;
            $this->dayFilters[] = new \Recur\Filter\Day(in_array('MO', $byday), in_array('TU', $byday), in_array('WE', $byday), in_array('TH', $byday), in_array('FR', $byday), in_array('SA', $byday), in_array('SU', $byday));
        }
    }
    
    /**
     * @return Array
     */
    public function getByday()
    {
        return $this->byday;
    }
    
    
    /**
     * Set byhour field depending on its config represenation
     * If byhour filter is present adds coresponding \Recur\Filter\AbstractFilter
     * to hourFilter array
     *
     * @param Array $byhour
     *
     */
    private function setByhour($byhour)
    {
        if(!is_string($byhour))
            throw new Exception\InvalidRuleConfig('By hour rule must be string.');
    
        $byhour = array_map('intval', array_filter(explode(',', $byhour), 'is_numeric'));
    
        sort($byhour, SORT_NUMERIC);
        $byhour = array_unique($byhour);
    
        if((($first = current($byhour)) && (int)$first < 0) || (($last = end($byhour)) && (int)$last > 23))
            throw new Exception\InvalidRuleConfig('Invalid hour rule specification.');
    
        if($byhour){
            $this->byhour = $byhour;
            $this->hourFilters[] = new \Recur\Filter\Hour($this->byhour);
        }
    }
    
    /**
     * @return Array
     */
    public function getByhour()
    {
        return $this->byhour;
    }
    
    /**
     * @return Array of day filters
     */
    public function getDayFilters(){
        return $this->dayFilters;
    }
    
    /**
     * @return boolean indicates existance of day filters
     */
    public function hasDayFilters(){
        return !empty($this->dayFilters);
    }

    /**
     * @return Array of hour filters
     */
    public function getHourFilters(){
        return $this->hourFilters;
    }
    

    /**
     * @return boolean indicates existance of hour filters
     */
    public function hasHourFilters(){
        return !empty($this->hourFilters);
    }
    
    /**
     * @return Array of upper filters depending on frequency
     */
    public function getUpperFilters(){
        
        if($this->getFreq() instanceof \Recur\Frequency\Hourly){
            return $this->getDayFilters();
        }
        
        return array();
    }
    
    /**
     * @return Array of filters on same level as frequency
     */
    public function getLevelFilters(){

        if($this->getFreq() instanceof \Recur\Frequency\Daily){
            return $this->getDayFilters();
        }
        if($this->getFreq() instanceof \Recur\Frequency\Hourly){
            return $this->getHourFilters();
        }
        
        return array();
    }
    
    /**
     * @return Array of lower filters depending on frequency
     */
    public function getLowerFilters(){
        
        if($this->getFreq() instanceof \Recur\Frequency\Weekly){
            return array_merge($this->getDayFilters(), $this->getHourFilters());
        }
        
        if($this->getFreq() instanceof \Recur\Frequency\Daily){
            return $this->getHourFilters();
        }
        
        return array();
    }
    
    /**
     * @return Recur\Frequency\AbstractFrequency that corespodents to lowest filter
     */
    public function getFrequencyByLowerFilter(){
        
        if($this->getFreq() instanceof \Recur\Frequency\Weekly){

            if($this->hasHourFilters()){
                return new \Recur\Frequency\Hourly();
            }
            
            if($this->hasDayFilters()){
                return new \Recur\Frequency\Daily();
            }
        }
        
        if($this->getFreq() instanceof \Recur\Frequency\Daily){
            return new \Recur\Frequency\Hourly();
        }
    }
    
    
    public function addOffset($eventDate)
    {
              
        if(($upperFilters = $this->getUpperFilters()))
        {
            $status = false;
            while(!$status)
            {    
                $eventDate->add($this->getFreq()->getOffset());
                $status = true;
                
                foreach($upperFilters as $filter)
                {
                    $status &= $filter->filter($eventDate);
                    if(!$status) break;
                }
            }
           
            return;
        }
        
        $eventDate->add($this->getFreq()->getOffset());
    }
    
    
    /**
     * @return Array which represents config array for this object
     */
    public function getConfigArray(){
       
        $config = array();
        
        $config['FREQ'] = $this->freq->getConfigName();
        
        if($this->interval && $this->interval != 1)
            $config['INTERVAL'] = $this->interval;
        
        if($this->until)
            $config['UNTIL'] = $this->until;
        
        if($this->count)
            $config['COUNT'] = $this->count;
        
        if($this->byday){
            $config['BYDAY'] = implode(',', $this->byday);
        }
        
        if($this->byhour){
            $config['BYHOUR'] = implode(',', $this->byhour);
        }
            
        return $config;
    }
    
    public function validate(){
        
    }

}
?>