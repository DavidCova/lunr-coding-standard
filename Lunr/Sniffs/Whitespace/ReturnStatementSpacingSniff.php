<?php

namespace Lunr\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class ReturnStatementSpacingSniff implements Sniff
{

    public function register()
    {

        return [ T_RETURN ];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Check if the return statement is nested
        $isNested  = FALSE;
        $parentPtr = $phpcsFile->findPrevious(Tokens::$scopeOpeners, $stackPtr - 1, NULL, FALSE, NULL, TRUE);

        if ($parentPtr !== FALSE)
        {
            $isNested = TRUE;
        }

        if (!$isNested)
        {
            // Check line before
            $prevToken = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), NULL, TRUE);

            if ($tokens[$stackPtr]['line'] - $tokens[$prevToken]['line'] < 2)
            {
                $error = 'There must be a blank line before return statements';
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'LineBeforeReturn');

                if ($fix === TRUE)
                {
                    $phpcsFile->fixer->beginChangeset();
                    $phpcsFile->fixer->addContentBefore($stackPtr, "\n");
                    $phpcsFile->fixer->endChangeset();
                }
            }
        }
    }

}
