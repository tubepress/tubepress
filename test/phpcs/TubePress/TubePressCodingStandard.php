<?php
/**
 * TubePress Coding Standard
 */

if (class_exists('PHP_CodeSniffer_Standards_CodingStandard', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_CodingStandard not found');
}

/**
 * TubePress Coding Standard.
 */
class PHP_CodeSniffer_Standards_TubePress_TubePressCodingStandard
extends PHP_CodeSniffer_Standards_CodingStandard
{
    /**
     * Return a list of external sniffs to include with this standard.
     *
     * The standard can include the whole standards or individual Sniffs.
     *
     * @return array
     */
    public function getIncludedSniffs()
    {
        return array('PEAR', 'Zend');

    }//end getIncludedSniffs()

    /**
     * Return a list of external sniffs to exclude from this standard.
     *
     * Including a whole standards above, individual Sniffs can then be removed here.
     *
     * @return array
     */
    public function getExcludedSniffs()
    {
        return array(
		'Generic/Sniffs/Files/LineLengthSniff.php',
		'PEAR/Sniffs/Commenting/ClassCommentSniff.php',
		'PEAR/Sniffs/Commenting/FileCommentSniff.php',
		'PEAR/Sniffs/NamingConventions/ValidClassNameSniff.php',
		'PEAR/Sniffs/Files/LineLengthSniff.php',
		'Zend/Sniffs/Files/LineLengthSniff.php');

    }//end getExcludedSniffs()
}
?>
