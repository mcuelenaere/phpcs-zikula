<?php
if (class_exists('PHP_CodeSniffer_Standards_CodingStandard', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_CodingStandard not found');
}

/**
 * Zikula Coding Standard.
 */
class PHP_CodeSniffer_Standards_Zikula_ZikulaCodingStandard extends PHP_CodeSniffer_Standards_CodingStandard
{
    /**
     * Return a list of external sniffs to include with this standard.
     * The standard can include the whole standards or individual Sniffs.
     *
     * @return array
     */
    public function getIncludedSniffs()
    {
        return array(
                'Zend/Sniffs/Files/ClosingTagSniff.php', // no ending PHP_EOF tags
                'Generic/Sniffs/PHP/DisallowShortOpenTagSniff.php', // no short PHP tags
                'Generic/Sniffs/PHP/LowerCaseConstantSniff.php', // null, true, false
                'Generic/Sniffs/WhiteSpace/DisallowTabIndentSniff.php', // only spaces for tags
                'Squiz/Sniffs/Classes/LowercaseClassKeywordsSniff.php', // all class keywords must be lowercase
                //'Squiz/Sniffs/Classes/ValidClassNameSniff.php', // all class names must be camel case
                'Squiz/Sniffs/Classes/SelfMemberReferenceSniff.php', // checks use of self in classes
                //'Squiz/Sniffs/Commenting/BlockCommentSniff.php', //
                //'Squiz/Sniffs/Commenting/ClassCommentSniff.php', //
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