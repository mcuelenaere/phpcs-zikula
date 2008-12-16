<?php
/**
 * Parses and verifies the file doc comment.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: FileCommentSniff.php,v 1.17 2007/11/04 22:29:52 squiz Exp $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_CommentParser_ClassCommentParser', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_CommentParser_ClassCommentParser not found');
}

/**
 * Parses and verifies the file doc comment.
 *
 * Verifies that :
 * <ul>
 *  <li>A file doc comment exists.</li>
 *  <li>There is no blank line between the open tag and the file comment.</li>
 *  <li>Short description ends with a full stop.</li>
 *  <li>There is a blank line after the short description.</li>
 *  <li>Each paragraph of the long description ends with a full stop.</li>
 *  <li>There is a blank line between the description and the tags.</li>
 *  <li>Check the order, indentation and content of each tag.</li>
 *  <li>There is exactly one blank line after the file comment.</li>
 * </ul>
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.1.0
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

class Zikula_Sniffs_Files_FileCommentSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * The header comment parser for the current file.
     *
     * @var PHP_CodeSniffer_Comment_Parser_ClassCommentParser
     */
    protected $commentParser = null;

    /**
     * The current PHP_CodeSniffer_File object we are processing.
     *
     * @var PHP_CodeSniffer_File
     */
    protected $currentFile = null;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_OPEN_TAG);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $this->currentFile = $phpcsFile;

        // We are only interested if this is the first open tag.
        if ($stackPtr !== 0) {
            if ($phpcsFile->findPrevious(T_OPEN_TAG, ($stackPtr - 1)) !== false) {
                return;
            }
        }

        $tokens = $phpcsFile->getTokens();

        // Find the next non whitespace token.
        $commentStart = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);

        if ($tokens[$commentStart]['code'] === T_CLOSE_TAG) {
            // We are only interested if this is the first open tag.
            return;
        } else if ($tokens[$commentStart]['code'] === T_COMMENT) {
            $phpcsFile->addError('You must use "/**" style comments for a file comment', ($stackPtr + 1));
            return;
        } else if ($commentStart === false || $tokens[$commentStart]['code'] !== T_DOC_COMMENT) {
            $phpcsFile->addError('Missing file doc comment', ($stackPtr + 1));
            return;
        } else {

            // Extract the header comment docblock.
            $commentEnd = ($phpcsFile->findNext(T_DOC_COMMENT, ($commentStart + 1), null, true) - 1);

            // Check if there is only 1 doc comment between the open tag and class token.
            $nextToken   = array(
                            T_ABSTRACT,
                            T_CLASS,
                            T_DOC_COMMENT,
                           );
            $commentNext = $phpcsFile->findNext($nextToken, ($commentEnd + 1));
            if ($commentNext !== false && $tokens[$commentNext]['code'] !== T_DOC_COMMENT) {
                // Found a class token right after comment doc block.
                $newlineToken = $phpcsFile->findNext(T_WHITESPACE, ($commentEnd + 1), $commentNext, false, $phpcsFile->eolChar);
                if ($newlineToken !== false) {
                    $newlineToken = $phpcsFile->findNext(T_WHITESPACE, ($newlineToken + 1), $commentNext, false, $phpcsFile->eolChar);
                    if ($newlineToken === false) {
                        // No blank line between the class token and the doc block.
                        // The doc block is most likely a class comment.
                        $phpcsFile->addError('Missing file doc comment', ($stackPtr + 1));
                        return;
                    }
                }
            }

          
            // Exactly one blank line after the file comment.
            $nextTokenStart = $phpcsFile->findNext(T_WHITESPACE, ($commentEnd + 1), null, true);

            $comment = $phpcsFile->getTokensAsString($commentStart, ($commentEnd - $commentStart + 1));

            // Parse the header comment docblock.
            try {
                $this->commentParser = new PHP_CodeSniffer_CommentParser_ClassCommentParser($comment, $phpcsFile);
                $this->commentParser->parse();
            } catch (PHP_CodeSniffer_CommentParser_ParserException $e) {
                $line = ($e->getLineWithinComment() + $commentStart);
                $phpcsFile->addError($e->getMessage(), $line);
                return;
            }

            $comment = $this->commentParser->getComment();
            if (is_null($comment) === true) {
                $error = 'File doc comment is empty';
                $phpcsFile->addError($error, $commentStart);
                return;
            }

            // No extra newline before short description.
            $short        = $comment->getShortComment();
            $newlineCount = 0;
            $newlineSpan  = strspn($short, $phpcsFile->eolChar);
            if ($short !== '' && $newlineSpan > 0) {
                $line  = ($newlineSpan > 1) ? 'newlines' : 'newline';
                $error = "Extra $line found before file comment short description";
                $phpcsFile->addError($error, ($commentStart + 1));
            }

            $newlineCount = (substr_count($short, $phpcsFile->eolChar) + 1);

            // Exactly one blank line between short and long description.
            $long = $comment->getLongComment();
            if (empty($long) === false) {
                $between        = $comment->getWhiteSpaceBetween();
                $newlineBetween = substr_count($between, $phpcsFile->eolChar);
          
                $newlineCount += $newlineBetween;

                $testLong = trim($long);
                if (preg_match('|[A-Z]|', $testLong[0]) === 0) {
                    $error = 'File comment long description must start with a capital letter';
                    $phpcsFile->addError($error, ($commentStart + $newlineCount));
                }
            }//end if

         

            // Short description must be single line and end with a full stop.
            $testShort = trim($short);
            $lastChar  = $testShort[(strlen($testShort) - 1)];

            // Check for unknown/deprecated tags.
            $unknownTags = $this->commentParser->getUnknown();
            foreach ($unknownTags as $errorTag) {
                // Unknown tags are not parsed, do not process further.
                $error = "@$errorTag[tag] tag is not allowed in file comment";
                $phpcsFile->addWarning($error, ($commentStart + $errorTag['line']));
            }

            // Check each tag.
            $this->processTags($commentStart, $commentEnd);
        }//end if

    }//end process()


    /**
     * Processes each required or optional tag.
     *
     * @param int $commentStart The position in the stack where the comment started.
     * @param int $commentEnd   The position in the stack where the comment ended.
     *
     * @return void
     */
    protected function processTags($commentStart, $commentEnd)
    {
        // Required tags in correct order.
        $tags = array(
                 'copyright'    => 'precedes @link',
                 // 'link'    => 'follows @copyright',
                 'version' => 'follows @link',
                 'license'     => 'follows @version',
                 'package'  => 'follows @license',
                 'subpackage'    => 'follows @package',
                );

        $foundTags   = $this->commentParser->getTagOrders();
        $errorPos    = 0;
        $orderIndex  = 0;
        $longestTag  = 0;
        $indentation = array();
        foreach ($tags as $tag => $orderText) {

            // Required tag missing.
            if (in_array($tag, $foundTags) === false) {
                $error = "Missing @$tag tag in file comment";
                $this->currentFile->addError($error, $commentEnd);
                continue;
            }

            // Get the line number for current tag.
            $tagName = ucfirst($tag);
            if ($tagName === 'Author' || $tagName === 'Copyright') {
                // These tags are different because they return an array.
                $tagName .= 's';
            }

            // Work out the line number for this tag.
            $getMethod  = 'get'.$tagName;
            $tagElement = $this->commentParser->$getMethod();
            if (is_null($tagElement) === true || empty($tagElement) === true) {
                continue;
            } else if (is_array($tagElement) === true && empty($tagElement) === false) {
                $tagElement = $tagElement[0];
            }

            $errorPos = ($commentStart + $tagElement->getLine());

            // Make sure there is no duplicate tag.
            $foundIndexes = array_keys($foundTags, $tag);
            if (count($foundIndexes) > 1) {
                $error = "Only 1 @$tag tag is allowed in file comment";
                $this->currentFile->addError($error, $errorPos);
            }

            // Check tag order.
            if ($foundIndexes[0] > $orderIndex) {
                $orderIndex = $foundIndexes[0];
            } else {
                $error = "The @$tag tag is in the wrong order; the tag $orderText";;
                $this->currentFile->addError($error, $errorPos);
            }

            // Store the indentation of each tag.
            $len = strlen($tag);
            if ($len > $longestTag) {
                $longestTag = $len;
            }

            $indentation[] = array(
                              'tag'      => $tag,
                              'errorPos' => $errorPos,
                              'space'    => $this->getIndentation($tag, $tagElement),
                             );

            $method = 'process'.$tagName;
            if (method_exists($this, $method) === true) {
                // Process each tag if a method is defined.
                call_user_func(array($this, $method), $errorPos);
            } else {
                $tagElement->process($this->currentFile, $commentStart, 'file');
            }
        }//end foreach

       
    }//end processTags()


    /**
     * Get the indentation information of each tag.
     *
     * @param string                                   $tagName    The name of the doc comment element.
     * @param PHP_CodeSniffer_CommentParser_DocElement $tagElement The doc comment element.
     *
     * @return void
     */
    protected function getIndentation($tagName, $tagElement)
    {
        if ($tagElement instanceof PHP_CodeSniffer_CommentParser_SingleElement) {
            if ($tagElement->getContent() !== '') {
                return (strlen($tagName) + substr_count($tagElement->getWhitespaceBeforeContent(), ' '));
            }
        } else if ($tagElement instanceof PHP_CodeSniffer_CommentParser_PairElement) {
            if ($tagElement->getValue() !== '') {
                return (strlen($tagName) + substr_count($tagElement->getWhitespaceBeforeValue(), ' '));
            }
        }

        return 0;

    }//end getIndentation()


    /**
     * The version tag must have the exact keyword 'release_version'.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processVersion($errorPos)
    {
        $version = $this->commentParser->getVersion();
        if ($version !== null) {
            $content = $version->getContent();
            if (empty($content) === true) {
                $error = 'Content missing for @version tag in file comment';
                $this->currentFile->addError($error, $errorPos);
            }
        }

    }//end processVersion()


    /**
     * The package name must be 'MySource4'.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processPackage($errorPos)
    {
        $package = $this->commentParser->getPackage();
        if ($package !== null) {
            $content = $package->getContent();
            if (empty($content) === true) {
                $error = 'Content missing for @package tag in file comment';
                $this->currentFile->addError($error, $errorPos);
            }
        }

    }//end processPackage()


    /**
     * The subpackage name must be camel-cased.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processSubpackage($errorPos)
    {
        $subpackage = $this->commentParser->getSubpackage();
        if ($subpackage !== null) {
            $content = $subpackage->getContent();
            if (empty($content) === true) {
                $error = 'Content missing for @subpackage tag in file comment';
                $this->currentFile->addError($error, $errorPos);
            }
        }

    }//end processSubpackage()


    /**
     * Author tag must be 'Squiz Pty Ltd <mysource4@squiz.net>'.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processAuthors($errorPos)
    {
    	// no check for author
        $authors = $this->commentParser->getAuthors();
        

    }//end processAuthors()


    /**
     * Copyright tag must be in the form '2006-YYYY Squiz Pty Ltd (ABN 77 084 670 600)'.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processCopyrights($errorPos)
    {
        $copyrights = $this->commentParser->getCopyrights();
        $copyright  = $copyrights[0];
		$wanted = "(c) Zikula Development Team";
        if ($copyright !== null) {
            $content = $copyright->getContent();
            if (empty($content) === true) {
                $error = 'Content missing for @copyright tag in file comment';
                $this->currentFile->addError($error, $errorPos);

            } else if ($content != $wanted) {
                $error = 'Expected ' .$wanted . ' for copyright declaration found "' . $content . '"';
                $this->currentFile->addError($error, $errorPos);
            }
        }

    }//end processCopyrights()


    /**
     * License tag must be 'http://matrix.squiz.net/licence Squiz.Net Open Source Licence'.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processLicense($errorPos)
    {
        $license = $this->commentParser->getLicense();
        if ($license !== null) {
            $url     = $license->getValue();
            $content = $license->getComment();
            if (empty($url) === true && empty($content) === true) {
                $error = 'Content missing for @license tag in file comment';
                $this->currentFile->addError($error, $errorPos);
            } else {
            	/*
				
				
                // Check for license URL.
                if (empty($url) === true) {
                    $error = 'License URL missing for @license tag in file comment';
                    $this->currentFile->addError($error, $errorPos);
                } else if ($url !== 'http://matrix.squiz.net/licence') {
                    $error = 'Expected "http://matrix.squiz.net/licence" for license URL';
                    $this->currentFile->addError($error, $errorPos);
                }
                

                // Check for license name.
                if (empty($content) === true) {
                    $error = 'License name missing for @license tag in file comment';
                    $this->currentFile->addError($error, $errorPos);
                } else if ($content !== 'Squiz.Net Open Source Licence') {
                    $error = 'Expected "Squiz.Net Open Source Licence" for license name';
                    $this->currentFile->addError($error, $errorPos);
                }
                */
            }//end if
        }//end if

    }//end processLicense()


}//end class


?>