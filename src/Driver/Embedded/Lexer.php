<?php
/*
 * Yurii's Gantt Plugin
 *
 * Copyright (C) 2020 Yurii K.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses
 */

namespace dokuwiki\plugin\yuriigantt\src\Driver\Embedded;

//
// WORKAROUND: for stable version
//
if (!class_exists(\dokuwiki\Parsing\Lexer\Lexer::class)) {
    class_alias(\Doku_Lexer::class, \dokuwiki\Parsing\Lexer\Lexer::class);
}

class Lexer extends \dokuwiki\Parsing\Lexer\Lexer
{

}
