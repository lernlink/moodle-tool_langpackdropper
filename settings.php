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
 * Admin tool "Language pack dropper" - Settings
 *
 * @package    tool_langpackdropper
 * @copyright  2022 Alexander Bias, lern.link GmbH <alexander.bias@lernlink.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $CFG;

if ($hassiteconfig) {
    $settings = new admin_settingpage('tool_langpackdropper', get_string('pluginname', 'tool_langpackdropper', null, true));

    if ($ADMIN->fulltree) {
        // Require the necessary libraries.
        require_once($CFG->dirroot . '/admin/tool/langpackdropper/locallib.php');

        // Create language pack dropper static widget.
        $setting = new admin_setting_heading('tool_langpackdropper/langpackdropperstatic',
                '',
                get_string('setting_langpackdropperstatic_desc', 'tool_langpackdropper', null, true));
        $settings->add($setting);

        // Create language pack urls widget.
        $manageurl = new moodle_url('/admin/tool/langimport/index.php');
        $setting = new admin_setting_configtextarea('tool_langpackdropper/langpackurls',
                get_string('setting_langpackurls', 'tool_langpackdropper', null, true),
                get_string('setting_langpackurls_desc', 'tool_langpackdropper', array('managepage' => $manageurl->out()), true),
                        '', PARAM_RAW);
        $setting->set_updatedcallback('tool_langpackdropper_updatecallback');
        $settings->add($setting);

        // Create language pack timeout widget.
        $setting = new admin_setting_configtext('tool_langpackdropper/downloadtimeout',
                get_string('setting_downloadtimeout', 'tool_langpackdropper', null, true),
                get_string('setting_downloadtimeout_desc', 'tool_langpackdropper', null, true), 10, PARAM_INT);
        $settings->add($setting);
    }

    $ADMIN->add('language', $settings);
}
