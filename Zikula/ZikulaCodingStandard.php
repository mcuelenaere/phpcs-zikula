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
            //'Generic/Sniffs/CodeAnalysis/JumbledIncrementerSniff.php',
            //'Generic/Sniffs/CodeAnalysis/UnusedFunctionParameterSniff.php',
            //'Generic/Sniffs/CodeAnalysis/UnconditionalIfStatementSniff.php',
            //'Generic/Sniffs/CodeAnalysis/UselessOverridingMethodSniff.php',
            //'Generic/Sniffs/CodeAnalysis/ForLoopWithTestFunctionCallSniff.php',
            //'Generic/Sniffs/CodeAnalysis/ForLoopShouldBeWhileLoopSniff.php',
            //'Generic/Sniffs/CodeAnalysis/UnnecessaryFinalModifierSniff.php',
            //'Generic/Sniffs/CodeAnalysis/EmptyStatementSniff.php', // seems to make mistakes with if ($var === true) {}
            'Generic/Sniffs/Formatting/NoSpaceAfterCastSniff.php',
            //'Generic/Sniffs/Functions/OpeningFunctionBraceBsdAllmanSniff.php',
            //'Generic/Sniffs/Metrics/NestingLevelSniff.php',
            //'Generic/Sniffs/Metrics/CyclomaticComplexitySniff.php',
            //'Generic/Sniffs/Classes/DuplicateClassNameSniff.php',
            //'Generic/Sniffs/NamingConventions/ConstructorNameSniff.php',
            //'Generic/Sniffs/NamingConventions/UpperCaseConstantNameSniff.php',// this conflicts with PHP 5.3 namespace and use syntax, fixed in SVN not released in 1.2.2
            'Generic/Sniffs/PHP/DisallowShortOpenTagSniff.php',  // no short PHP tags
            'Generic/Sniffs/PHP/LowerCaseConstantSniff.php',  // null, true, false should be lower case
            'Generic/Sniffs/WhiteSpace/DisallowTabIndentSniff.php',  // only spaces for indents
            'Generic/Sniffs/WhiteSpace/ScopeIndentSniff.php',
            'MySource/Sniffs/PHP/EvalObjectFactorySniff.php',  // prohibit eval for object instanciation

            'Squiz/Sniffs/Commenting/EmptyCatchCommentSniff.php',
            'Squiz/Sniffs/Commenting/FunctionCommentThrowTagSniff.php',

            'Squiz/Sniffs/Classes/LowercaseClassKeywordsSniff.php',
            'Squiz/Sniffs/Classes/SelfMemberReferenceSniff.php',
            'Squiz/Sniffs/Classes/DuplicatePropertySniff.php',
            'Squiz/Sniffs/Classes/ClassFileNameSniff.php',
            'Squiz/Sniffs/Classes/ValidClassNameSniff.php',
            'Squiz/Sniffs/ControlStructures/ControlSignatureSniff.php',
            'Squiz/Sniffs/ControlStructures/ElseIfDeclarationSniff.php',
            'Squiz/Sniffs/ControlStructures/ForEachLoopDeclarationSniff.php',
            'Squiz/Sniffs/ControlStructures/ForLoopDeclarationSniff.php',
            //'Squiz/Sniffs/ControlStructures/InlineIfDeclarationSniff.php',// problematic
            'Squiz/Sniffs/ControlStructures/LowercaseDeclarationSniff.php',
            //'Squiz/Sniffs/ControlStructures/SwitchDeclarationSniff.php',  // problematic

            'Squiz/Sniffs/Objects/ObjectInstantiationSniff.php',

            'Squiz/Sniffs/Operators/ValidLogicalOperatorsSniff.php',
            'Squiz/Sniffs/Scope/StaticThisUsageSniff.php',
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