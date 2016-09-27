<?php
namespace view;

class DateTimeView {


	public function show() {

	    $date = new \DateTime();

        $timeString = $date->format("l") . ", the " . $date->format("jS \of F Y") . ", The time is " . $date->format("H:i:s");

		return '<p>' . $timeString . '</p>';
	}
}