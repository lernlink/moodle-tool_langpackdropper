<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Steps definitions for tool_langpackdropper
 *
 * This script is only called from Behat as part of it's integration in Moodle.
 *
 * @package   tool_langpackdropper
 * @copyright 2022 Alexander Bias, lern.link GmbH <alexander.bias@lernlink.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__.'/../../../../../lib/tests/behat/behat_forms.php');

/**
 * Steps definitions for tool_langpackdropper
 *
 * This script is only called from Behat as part of it's integration in Moodle.
 *
 * @package   tool_langpackdropper
 * @copyright 2022 Alexander Bias, lern.link GmbH <alexander.bias@lernlink.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_tool_langpackdropper extends behat_base {
    // @codingStandardsIgnoreStart
    /**
     * Sets a language pack URL setting.
     *
     * @Given /^I set the fixtures path for the "(?P<identifier_string>(?:[^"]|\\")*)" language pack to "(?P<path_string>(?:[^"]|\\")*)"$/
     * @param string $identifier
     * @param string $path
     * @return void
     */
    public function i_set_the_fixtures_path_for_the_language_pack_to($identifier, $path) {
        // @codingStandardsIgnoreEnd
        global $CFG;

        $this->set_field_value('Language pack URLs', $identifier.'|'.$CFG->wwwroot.$path);
    }
}
