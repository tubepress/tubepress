<?php
class TPAbstractHasDescription extends TPAbstractHasTitle {
	
	private $description;
	
	public function __construct($theName, $theTitle, $theDescription) {
		parent::__construct($name, $title);
		$this->description = $theDescription;
	}
	
	public function getDescription() {
		return $this->description;
	}
}
?>