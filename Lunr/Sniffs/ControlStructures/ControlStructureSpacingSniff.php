<?php

namespace Lunr\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class ControlStructureSpacingSniff implements Sniff
{

    public function register()
    {
        return [
            T_IF,
            T_FOREACH,
            T_FOR,
            T_SWITCH,
            T_WHILE,
            T_DO,
        ];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Check if the control structure is nested
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
                $error = 'There must be a blank line before control structures';
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'LineBefore');

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
