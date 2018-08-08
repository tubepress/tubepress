<?php
/*
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

$contribFixers = array(

    'align_double_arrow',                   // Align double arrow symbols in consecutive lines.

    'align_equals',                         // Align equals symbols in consecutive lines.

    'concat_with_spaces',                   // Concatenation should be used with at least one whitespace around.

    'long_array_syntax',                    // Arrays should use the long syntax.

    'multiline_spaces_before_semicolon',    // Multi-line whitespace before closing semicolon are prohibited.

    'newline_after_open_tag',               // Ensure there is no code on the same line as the PHP open tag.

    'ordered_use',                          // Ordering use statements.

    '-phpdoc_no_empty_return',              // @return void and @return null annotations should be omitted from phpdocs.

    '-braces',                              // The body of each structure MUST be enclosed by braces. Braces should be
                                            // properly placed. Body of braces should be properly indented. TubePress
                                            // excludes this because we like to keep a blank line after control structure
                                            // opening braces.

    'php4_constructor',                     // Convert PHP4-style constructors to __construct. Warning! This could
                                            // change code behavior.

    'php_unit_construct',                   // PHPUnit assertion method calls like "->assertSame(true, $foo)" should be
                                            // written with dedicated method like "->assertTrue($foo)". Warning! This
                                            // could change code behavior.

    'short_echo_tag',                       // Replace short-echo <?= with long format <?php echo syntax.

    '-empty_return'                         // A return statement wishing to return nothing should be simply "return".
);

return Symfony\CS\Config\Config::create()
    ->level(\Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers($contribFixers);