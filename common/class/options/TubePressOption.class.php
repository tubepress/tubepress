<?php
class TubePressOption implements TubePressHasValue,
    TubePressHasDescription, TubePressHasName, TubePressHasTitle {
	
	const storageIdentifier = "tubepress";

    private $name;
    private $title;
    private $description;
    private $value;
	
	public function __construct($theName, $theTitle, $theDescription, $theDefault) {
		$this->name = $theName;
		$this->title = $theTitle;
		$this->description = $theDescription;
		$this->value = $theDefault;
	}

	public final function getName() { return $this->name; }
	public final function getTitle() { return $this->title; }
	public final function getDescription() { return $this->description; }
	public final function &getValue() { return $this->value; }
}
?>