<?php 

namespace Recur\Rule;

use Recur\Recur;

/**
 * Recur type with repetion on mondays, wednesdays and fridays
 *
 * @author Marjana
 */
class MoWeFr extends Recur {

    public function __construct(){
        parent::__construct(array("FREQ" => "DAILY", "BYDAY" => "MO, WE, FR"));
    }
}
?>