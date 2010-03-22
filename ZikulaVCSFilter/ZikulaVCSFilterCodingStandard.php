<?php
if (class_exists('PHP_CodeSniffer_Standards_CodingStandard', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_CodingStandard not found');
}

/**
 * Zikula Coding Standard.
 */
class PHP_CodeSniffer_Standards_Zikula_ZikulaVCSFilterCodingStandard extends PHP_CodeSniffer_Standards_CodingStandard
{
    /**
     * Return a list of external sniffs to include with this standard.
     * The standard can include the whole standards or individual Sniffs.
     *
     * If any of these conditions trigger we will not allow the commit to process to version control
     *
     * @return array
     */
    public function getIncludedSniffs()
    {
        return array(
            'Generic/Sniffs/Functions/OpeningFunctionBraceBsdAllmanSniff.php',
            'Generic/Sniffs/Classes/DuplicateClassNameSniff.php',
            'Generic/Sniffs/PHP/DisallowShortOpenTagSniff.php',  // no short PHP tags
            'Generic/Sniffs/WhiteSpace/DisallowTabIndentSniff.php',  // only spaces for indents
            'Squiz/Sniffs/Classes/ClassFileNameSniff.php',
            'Squiz/Sniffs/Classes/ValidClassNameSniff.php',
            'Zend/Sniffs/Files/ClosingTagSniff.php',// no ending PHP_EOF tags
        );


    }

    /**
     * Return a list of external sniffs to exclude from this standard.
     * Including a whole standards above, individual Sniffs can then be removed here.
     *
     * @return array
     */
    public function getExcludedSniffs()
    {
        return array();
    }
}