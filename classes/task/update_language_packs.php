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
 * Admin tool "Language pack dropper" - Scheduled task
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
class update_language_packs extends \core\task\scheduled_task {
    /**
     * Return localised task name.
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskupdatelanguagepacks', 'tool_langpackdropper');
    }

    /**
     * Execute scheduled task
     *
     * @return boolean
     */
    public function execute() {
        global $CFG;
        require_once($CFG->dirroot . '/admin/tool/langpackdropper/locallib.php');

        // Execute the helper function.
        $retvalue = tool_langpackdropper_handle_langpacks();

        return $retvalue;
    }
}
