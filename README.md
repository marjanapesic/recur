
Recurrung library
===================

Recur is a PHP library handling recurrence events. It will provide DateTime
objects representing occurences of repetitive event.

Recur object represent recurring type and is used to set up recurring rule. 
Recur resource allows creating recurring rules based on iCalendar (http://www.ietf.org/rfc/rfc5545.txt)
with restrictions specified bellow.
 
 **Recur instantiation**
For instantiation of recur object configuration array is used. Keys of array are rule recurrence rule parts. Possible rule parts (config array keys) are:
  
 'FREQ' => freq ; required
 'UNTIL' => DateTime ; optional
 'COUNT' => DIGIT ; optional
 'INTERVAL' => DIGIT ; optional
 'BYHOUR' => byhrlist ; optional
 'BYDAY' => bywdaylist ; optional
  
 Possible values can be:
 freq = "HOURLY"|"DAILY"|"WEEKLY"
 byhrlist = hour *("," hour)
 hour = 1*2DIGIT       ;0 to 23
 bywdaylist  = weekday *("," weekday)
 weekday = "SU" / "MO" / "TU" / "WE" / "TH" / "FR" / "SA"
 
 For detailed description of how rules are evaluated please refer to
 http://www.ietf.org/rfc/rfc5545.txt
 
 
 You can use one of predefined recurring types, daily, weekly or monday-wednesday-friday

> - $recurDaily = new \Recur\Rule\Daily();
> - $recurWeekly = new \Recur\Rule\Weekly();
> - $recurMoWeFr = new \Recur\Rule\MoWeFr();

or you can create other type of repetition with use of Recur class and configuration array:

> - $recur = new \Recur\Recur(array('FREQ' => 'HOURLY', 'INTERVAL' => '2', 'COUNT' => 10, 'BYDAY' => 'SA'));

 **Creating recurring event**

> - $event = new \Recur\Event\Event(new \DateTime('now'), $recur);

**Iteration**

> - $iterator = new \Recur\Event\Iterator($event);

**Intersector**
To get dates of event ocurrences in interval use:
> - $interval_start = new \DateTime('now');
> - $interval_end = new \DateTime('2015-04-23 12:00:00');

> - $intersector = new \Recur\Intersector\Interval();
> - $arrayDatesOcurrences = $intersector->intersect($event, $interval_start, $interval_end);

Possible improvements:
\Recur\Recur addOffset calculating offset that is being added instead of itearting by frequency step.