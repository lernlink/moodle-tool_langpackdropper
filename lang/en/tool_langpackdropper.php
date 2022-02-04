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
 * Admin tool "Language pack dropper" - Language pack
 *
 * @package    tool_langpackdropper
 * @copyright  2022 Alexander Bias, lern.link GmbH <alexander.bias@lernlink.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$string['eventlangpackinstalled'] = 'Language pack installed';
$string['eventlangpackinstalled_desc'] = 'The language pack \'{$a->name}\' was installed from {$a->url}';
$string['eventlangpackupdated'] = 'Language pack updated';
$string['eventlangpackupdated_desc'] = 'The language pack \'{$a->name}\' was updated from {$a->url}';
$string['pluginname'] = 'Language pack dropper';
$string['privacy:metadata'] = 'The Language pack dropper plugin does not store any personal data.';
$string['setting_downloadtimeout'] = 'Language pack download timeout';
$string['setting_downloadtimeout_desc'] = 'Moodle will try to download the language pack(s) from the given URL(s) until this timeout is hit.';
$string['setting_langpackdropperstatic_desc'] = 'With this plugin, you are able to download language packs which are not located / maintained in AMOS, the central Moodle translation system. This is especially relevant if you are running a large number of language string modifications and want to manage these rather in Git than in the Moodle language customization GUI. This is also relevant if you want to run the same language string modifications on multiple Moodle instances simultaneously.';
$string['setting_langpackurls'] = 'Language pack URLs';
$string['setting_langpackurls_desc'] = 'This setting expects a list of language packs to be used on this Moodle installation.
Each line consists of the language pack identifier and a URL where the language pack can be downloaded.<br/><br/>
For example:<br/>
de_mysublangpack|https://github.com/lernlink/moodle-tool_langpackdropper/raw/master/tests/fixtures/de_droppertest_toplevel.zip
<br/><br/>
Further information to the parameters:
<ul>
<li><b>Identifier:</b> This identifier is used as the folder name below your MOODLEDATA/lang directory where this language pack will be stored.</li>
<li><b>URL:</b> The download URL is expected as full URL where Moodle can download a valid ZIP file. URLs to Github repository downloads work particulary fine.</li>
</ul>
Please note:
<ul>
<li>Configuration lines which appear invalid, e.g. if they contain an invalid URL, will be silently ignored when the settings are processed.</li>
<li>The language pack ZIP file is expected to either contain all language pack files on its top level folder or to contain one single folder where all language pack files are located. In the second case, the folder can have any name. If the ZIP file does not match these requirements, the language pack will not be processed and an error will be logged.</li>
<li>With this tool, Moodle will download PHP code from the given URL and will execute its content as language pack PHP files. <em>It\'s your duty as administrator to only configure download URLs from which you are 100% sure that they do not contain anything else than language pack files or that an attacker can infiltrate malicious code into the ZIP file anytime in the future.</em></li>
<li>If you plan to use this tool to drop a language pack with an identifier which is also managed in AMOS - which is perfectly possible - please evaluate if you have to disable the \\tool_langimport\\task\\update_langpacks_task scheduled task to avoid any interference of both language pack updating mechanisms.</li>
</ul>';
$string['taskupdatelanguagepacks'] = 'Update dropped language packs';
$string['updatedcallbacknotification'] = 'The language pack URLs were updated. An ad-hoc task to download / update the language pack(s) based on the stored settings was scheduled and will be processed shortly.';
