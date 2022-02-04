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
 * Admin tool "Language pack dropper" - Ad-hoc task
 *
 * @package    tool_langpackdropper
 * @copyright  2022 Alexander Bias, lern.link GmbH <alexander.bias@lernlink.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_langpackdropper\task;

/**
 * The tool_langpackdropper drop language packs task class.
 *
 * @package    tool_langpackdropper
 * @copyright  2022 Alexander Bias, lern.link GmbH <alexander.bias@lernlink.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class drop_language_packs extends \core\task\adhoc_task {
    /**
     * Return the name of the component.
     *
     * @return string The name of the component.
     */
    public function get_component() {
        return 'tool_langpackdropper';
    }

    /**
     * Execute the ad-hoc task
     */
    public function execute() {
        global $CFG;
        require_once($CFG->dirroot . '/admin/tool/langpackdropper/locallib.php');

        // Execute the helper function.
        tool_langpackdropper_handle_langpacks();
    }
}
