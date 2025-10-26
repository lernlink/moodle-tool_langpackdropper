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
 * Admin tool "Language pack dropper" - Local library
 *
 * @package    tool_langpackdropper
 * @copyright  2022 Alexander Bias, lern.link GmbH <alexander.bias@lernlink.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Constants for handling the results of the langpack drop.
define('TOOL_LANGPACKDROPPER_LANGPACK_UNEXPECTEDSTRUCTURE', -6);
define('TOOL_LANGPACKDROPPER_LANGPACK_EXTRACTFAILED', -5);
define('TOOL_LANGPACKDROPPER_LANGPACK_NOTWRITABLE', -4);
define('TOOL_LANGPACKDROPPER_LANGPACK_NOZIPFILE', -3);
define('TOOL_LANGPACKDROPPER_LANGPACK_CURLERROR', -2);
define('TOOL_LANGPACKDROPPER_LANGPACK_NOVALIDURL', -1);
define('TOOL_LANGPACKDROPPER_LANGPACK_UPTODATE', 0);
define('TOOL_LANGPACKDROPPER_LANGPACK_INSTALLED', 1);
define('TOOL_LANGPACKDROPPER_LANGPACK_UPDATED', 2);


/**
 * Helper function which handles the dropping and updating of the language packs.
 *
 * @return boolean The fact if the whole process was successful.
 *                 If at least one language pack failed in a way that another try might fix the problem, the value will be false.
 *                 Otherwise, it will be true.
 */
function tool_langpackdropper_handle_langpacks() {
    // Fetch langpack URLs.
    $urls = tool_langpackdropper_parse_langpack_urls();

    // Initialize return value.
    $retvalue = true;

    // If we have at least one language pack.
    if (count($urls) > 0) {
        // Iterate over the language packs.
        foreach ($urls as $name => $url) {
            // Echo status.
            mtrace('Handling the \'' . $name . '\' language pack from ' . $url . ' ...');

            // Handle this language pack.
            $result = tool_langpackdropper_handle_langpack_from_url($name, $url);

            // If we haven't got a proper return value.
            if ($result === null) {
                mtrace('... FAILED: The file downloader function returned null which is unexpected.');
                continue;
            }
            // Output status message and flip return value if any language pack failed.
            switch ($result) {
                case TOOL_LANGPACKDROPPER_LANGPACK_UNEXPECTEDSTRUCTURE:
                    mtrace('... FATAL: The downloaded file has an unexpected directory structure.');
                    break;
                case TOOL_LANGPACKDROPPER_LANGPACK_NOZIPFILE:
                    mtrace('... FATAL: The downloaded file was not recognized as ZIP file.');
                    break;
                case TOOL_LANGPACKDROPPER_LANGPACK_EXTRACTFAILED:
                    mtrace('... FAILED: The downloaded file could not be extracted.');
                    $retvalue = false; // Flip the return value.
                    break;
                case TOOL_LANGPACKDROPPER_LANGPACK_NOTWRITABLE:
                    mtrace('... FAILED: The target path on disk is not writable.');
                    $retvalue = false; // Flip the return value.
                    break;
                case TOOL_LANGPACKDROPPER_LANGPACK_CURLERROR:
                    mtrace('... FAILED: A cURL error happened while downloading the file.');
                    $retvalue = false; // Flip the return value.
                    break;
                case TOOL_LANGPACKDROPPER_LANGPACK_UPTODATE:
                    mtrace('... SUCCESS: The language pack is up to date.');
                    break;
                case TOOL_LANGPACKDROPPER_LANGPACK_INSTALLED:
                    mtrace('... SUCCESS: The language pack was installed.');
                    $logevent = \tool_langpackdropper\event\langpack_installed::create([
                        'context' => context_system::instance(),
                        'other' => [
                            'name' => $name,
                            'url' => $url,
                        ],
                    ]);
                    $logevent->trigger();
                    break;
                case TOOL_LANGPACKDROPPER_LANGPACK_UPDATED:
                    mtrace('... SUCCESS: The language pack was updated.');
                    $logevent = \tool_langpackdropper\event\langpack_updated::create([
                        'context' => context_system::instance(),
                        'other' => [
                            'name' => $name,
                            'url' => $url,
                        ],
                    ]);
                    $logevent->trigger();
                    break;
            }
        }
    }

    // Return.
    return $retvalue;
}


/**
 * Helper function which parses the language pack URLs from the admin settings, validates then and returns
 * an array of valid URLs.
 *
 * @return array of URLs (key: language pack name, value: language pack url).
 */
function tool_langpackdropper_parse_langpack_urls() {
    // Initialize array to return at the end.
    $urls = [];

    // Get admin setting.
    $config = get_config('tool_langpackdropper', 'langpackurls');

    // If the setting is empty, return.
    if (!isset($config)) {
        return $urls;
    }

    // Split the admin setting line by line.
    $lines = explode("\n", $config);

    // Iterate over the lines.
    foreach ($lines as $line) {
        // Trim the line, continue to next line if it is empty afterwards.
        $line = trim($line);
        if (strlen($line) == 0) {
            continue;
        }

        // Make a new array on delimiter "|".
        $settings = explode('|', $line);

        // If there aren't exactly two settings, continue to next line.
        if (count($settings) != 2) {
            continue;
        }

        // Pick, trim them and clean both settings.
        $langpackname = clean_param(trim($settings[0]), PARAM_ALPHAEXT);
        $langpackurl = clean_param(trim($settings[1]), PARAM_URL);

        // If the url does not contain a valid URL, proceed to next line.
        if (!filter_var($langpackurl, FILTER_VALIDATE_URL)) {
            continue;
        }

        // If the name is empty now, proceed to next line.
        if (strlen($langpackname) < 1) {
            continue;
        }

        // Now we should have a valid language pack, add it to the array.
        $urls[$langpackname] = $langpackurl;
    }

    // Return array.
    return $urls;
}


/**
 * Helper function which downloads the langpack from the given URL, extracts it and places / updates it in Moodledata if necessary.
 *
 * @param string $name The langpack name.
 * @param string $url The langpack URL.
 *
 * @return int The result of the langpack drop.
 */
function tool_langpackdropper_handle_langpack_from_url($name, $url) {
    global $CFG;
    require_once($CFG->libdir . '/filelib.php');
    require_once($CFG->dirroot . '/admin/tool/langpackdropper/lib/diffon/Diffon.php');

    // If the given URL is not valid, return.
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return TOOL_LANGPACKDROPPER_LANGPACK_NOVALIDURL;
    }

    // Clean the given params. Better be (double) safe than sorry as we will download and write stuff with these parameters.
    $name = clean_param(trim($name), PARAM_ALPHAEXT);
    $url = clean_param(trim($url), PARAM_URL);

    // Init curl.
    $curl = new curl();

    // Compose download parameters.
    $dldirectory = make_request_directory('tool_langpackdropper');
    $dlpath = $dldirectory . '/' . $name . '.zip';
    $timeout = get_config('downloadtimeout', 'tool_langpackdropper');

    // Download langpack.
    $curlresult = $curl->download_one($url, null, ['filepath' => $dlpath, 'followlocation' => true, 'timeout' => $timeout]);

    // If the download was not successful.
    if ($curlresult !== true) {
        // Return.
        return TOOL_LANGPACKDROPPER_LANGPACK_CURLERROR;
    }

    // Init ZIP packer.
    $zippacker = new zip_packer();

    // Get files within ZIP file.
    $zipfiles = $zippacker->list_files($dlpath);

    // If the downloaded file isn't a ZIP file.
    if ($zipfiles === false) {
        // Return.
        return TOOL_LANGPACKDROPPER_LANGPACK_NOZIPFILE;
    }

    // Check if the langconfig.php file is located directly in the top directory.
    $langconfigattop = false;
    // Iterate over all files in the ZIP.
    foreach ($zipfiles as $zf) {
        // If we have found the langconfig.php file without a subdirectory prefix in the pathname.
        if ($zf->pathname == 'langconfig.php' && $zf->is_directory == false) {
            $langconfigattop = true;
            break;
        }
    }

    // If langconfig.php was not located in the top directory.
    if ($langconfigattop == false) {
        // Try to get the name of the subdirectory.
        $subdirname = '';
        $subdircount = 0;
        // Iterate over all files in the ZIP.
        foreach ($zipfiles as $zf) {
            // If we have found a subdirectory.
            if ($zf->is_directory == true) {
                // Remember the name of the subdirectory.
                $subdirname = $zf->pathname;
                // Increment the directory counter.
                $subdircount++;

                // If we have more than one subdirectory.
                if ($subdircount > 1) {
                    // Return.
                    return TOOL_LANGPACKDROPPER_LANGPACK_UNEXPECTEDSTRUCTURE;
                }
            }
        }

        // If we haven't found exactly one subdirectory.
        if ($subdirname == '') {
            // Return.
            return TOOL_LANGPACKDROPPER_LANGPACK_UNEXPECTEDSTRUCTURE;
        }

        // Check if the langconfig.php file is located in the second level directory.
        $langconfigatsecondlevel = false;
        // Iterate over all files in the ZIP.
        foreach ($zipfiles as $zf) {
            // If we have found the langconfig.php file at the second level.
            if ($zf->pathname == $subdirname . 'langconfig.php' && $zf->is_directory == false) {
                $langconfigatsecondlevel = true;
                break;
            }
        }

        // If we haven't found langconfig.php in the second level directory.
        if ($langconfigatsecondlevel == false) {
            // Return.
            return TOOL_LANGPACKDROPPER_LANGPACK_UNEXPECTEDSTRUCTURE;
        }
    }

    // If the lang directory does not exist yet in Moodledata yet
    // (this can happen if there wasn't any language pack besides English installed before).
    if (!is_dir($CFG->langotherroot)) {
        // Create the directory (This step is copied from install_init_dataroot()).
        if (!mkdir($CFG->langotherroot, $CFG->directorypermissions, true)) {
            // Return.
            return TOOL_LANGPACKDROPPER_LANGPACK_NOTWRITABLE;
        }
    }

    // Compose target path.
    $targetpath = $CFG->langotherroot . '/' . $name;

    // Detect if a language pack is already installed at the target path.
    if (is_dir($targetpath)) {
        $langpackinstalled = true;
    } else {
        $langpackinstalled = false;
    }

    // If a language pack is already installed at the target path, but the path is not writable for us.
    if ($langpackinstalled && !is_writable($targetpath)) {
        // Return.
        return TOOL_LANGPACKDROPPER_LANGPACK_NOTWRITABLE;
    }

    // Compose extract parameters.
    $tempextractdirectory = make_request_directory('tool_langpackdropper');
    $tempextractpath = $tempextractdirectory . '/' . $name;

    // Extract ZIP file directly to the temp extract location.
    $tempextractresult = $zippacker->extract_to_pathname($dlpath, $tempextractpath);

    // If the extraction went wrong for some reason.
    if ($tempextractresult === false) {
        // Return.
        return TOOL_LANGPACKDROPPER_LANGPACK_EXTRACTFAILED;
    }

    // If the language pack files are in the top directory.
    if ($langconfigattop == true) {
        $extractedlangpackpath = $tempextractpath;

        // Otherwise, if the language pack files are in a subdirectory.
    } else {
        $extractedlangpackpath = $tempextractpath . '/' . $subdirname;
    }

    // If the language pack is not yet installed.
    if ($langpackinstalled == false) {
        // Move the language pack files to the target location.
        // The permissions are already fine as the directory was created properly by make_request_directory() before.
        rename($extractedlangpackpath, $targetpath);

        // Purge language cache.
        purge_caches(['lang' => true]);

        // Return successfully.
        return TOOL_LANGPACKDROPPER_LANGPACK_INSTALLED;

        // Otherwise, if the language pack is already installed.
    } else {
        // Compare the downloaded with the installed language pack.
        $diffon = new Hack4mer\Diffon\Diffon();
        $diffon->setSource($extractedlangpackpath)->setDestination($targetpath);
        $diff = $diffon->diff();
        if (count($diff['only_in_source']) > 0 || count($diff['only_in_destination']) > 0 || count($diff['not_same']) > 0) {
            $langpackoutdated = true;
        } else {
            $langpackoutdated = false;
        }

        // If the language pack is up to date.
        if ($langpackoutdated == false) {
            // Return successfully.
            return TOOL_LANGPACKDROPPER_LANGPACK_UPTODATE;
        }

        // Delete the existing language pack at the target location.
        fulldelete($targetpath);

        // Move the language pack files to the target location.
        // The permissions are already fine as the directory was created properly by make_request_directory() before.
        rename($extractedlangpackpath, $targetpath);

        // Purge language cache.
        purge_caches(['lang' => true]);

        // Return successfully.
        return TOOL_LANGPACKDROPPER_LANGPACK_UPDATED;
    }
}

/**
 * Helper function which acts as update callback after the admin setting is stored.
 */
function tool_langpackdropper_updatecallback() {
    // If a language pack drop is configured.
    if (!empty(get_config('tool_langpackdropper', 'langpackurls'))) {
        // Trigger the ad-hoc task.
        $task = new \tool_langpackdropper\task\drop_language_packs();
        $result = \core\task\manager::queue_adhoc_task($task);

        // Trigger a notification on the settings page.
        \core\notification::success(get_string('updatedcallbacknotification', 'tool_langpackdropper'));
    }
}
