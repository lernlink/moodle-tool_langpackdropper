@tool @tool_langpackdropper
Feature: The language pack dropper tool allows admins to use non-AMOS language packs easily
  In order to customize my Moodle installation
  As an admin
  I need to download non-AMOS language packs and keep them updated

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    When I log in as "admin"
    And I navigate to "Language > Language packs" in site administration
    And I set the field "Available language packs" to "de"
    And I press "Install selected language pack(s)"

  Scenario: Configure a new (top level structured) language pack in the plugin settings and have it downloaded with the ad-hoc task
    And I navigate to "Language > Language pack dropper" in site administration
    And I set the field "Language pack URLs" to "de_droppertest|https://github.com/lernlink/moodle-tool_langpackdropper/raw/refs/heads/main/tests/fixtures/de_droppertest_toplevel.zip"
    And I press "Save changes"
    And I should see "The language pack URLs were updated"
    And I run all adhoc tasks
    And I navigate to "Language > Language packs" in site administration
    And the "Installed language packs" select box should contain "de_droppertest"
    And I navigate to "Language > Language settings" in site administration
    And I set the field "Languages on language menu" to "en,de_droppertest"
    And I press "Save changes"
    And I log out
    And I log in as "teacher1"
    And I follow "Preferences" in the user menu
    And I click on "Preferred language" "link"
    And I set the field "Preferred language" to "Deutsch (droppertest) ‎(de_droppertest)‎"
    And I press "Save changes"
    And I am on "Course 1" course homepage
    Then I should see "Mitglieder" in the ".secondary-navigation [data-key='participants']" "css_element"

  Scenario: Configure a new (top level structured) language pack in the plugin settings and have it downloaded with the scheduled task
    And I navigate to "Language > Language pack dropper" in site administration
    And I set the field "Language pack URLs" to "de_droppertest|https://github.com/lernlink/moodle-tool_langpackdropper/raw/refs/heads/main/tests/fixtures/de_droppertest_toplevel.zip"
    And I press "Save changes"
    And I should see "The language pack URLs were updated"
    # I do not run ad-hoc tasks by purpose now so that the langpack is not installed by the ad-hoc task
    And I run the scheduled task "tool_langpackdropper\task\update_language_packs"
    And I navigate to "Language > Language packs" in site administration
    And the "Installed language packs" select box should contain "de_droppertest"
    And I navigate to "Language > Language settings" in site administration
    And I set the field "Languages on language menu" to "en,de_droppertest"
    And I press "Save changes"
    And I log out
    And I log in as "teacher1"
    And I follow "Preferences" in the user menu
    And I click on "Preferred language" "link"
    And I set the field "Preferred language" to "Deutsch (droppertest) ‎(de_droppertest)‎"
    And I press "Save changes"
    And I am on "Course 1" course homepage
    Then I should see "Mitglieder" in the ".secondary-navigation [data-key='participants']" "css_element"

  Scenario: Configure a new (subdirectory / Github structured) language pack in the plugin settings and have it downloaded with the ad-hoc task
    And I navigate to "Language > Language pack dropper" in site administration
    And I set the field "Language pack URLs" to "de_droppertest|https://github.com/lernlink/moodle-tool_langpackdropper/raw/refs/heads/main/tests/fixtures/de_droppertest_toplevel.zip"
    And I press "Save changes"
    And I should see "The language pack URLs were updated"
    And I run all adhoc tasks
    And I navigate to "Language > Language packs" in site administration
    And the "Installed language packs" select box should contain "de_droppertest"
    And I navigate to "Language > Language settings" in site administration
    And I set the field "Languages on language menu" to "en,de_droppertest"
    And I press "Save changes"
    And I log out
    And I log in as "teacher1"
    And I follow "Preferences" in the user menu
    And I click on "Preferred language" "link"
    And I set the field "Preferred language" to "Deutsch (droppertest) ‎(de_droppertest)‎"
    And I press "Save changes"
    And I am on "Course 1" course homepage
    Then I should see "Mitglieder" in the ".secondary-navigation [data-key='participants']" "css_element"

  Scenario: Configure a new (top level structured) language pack in the plugin settings, have it downloaded with the ad-hoc task and have it updated later with the scheduled task
    And I navigate to "Language > Language pack dropper" in site administration
    And I set the field "Language pack URLs" to "de_droppertest|https://github.com/lernlink/moodle-tool_langpackdropper/raw/refs/heads/main/tests/fixtures/de_droppertest_toplevel.zip"
    And I press "Save changes"
    And I should see "The language pack URLs were updated"
    And I run all adhoc tasks
    And I navigate to "Language > Language packs" in site administration
    And the "Installed language packs" select box should contain "de_droppertest"
    # Now, we change the langpack URL in the background to simulate a regular upstream update of the language pack which is then detected by the scheduled task.
    And the following config values are set as admin:
      | langpackurls | de_droppertest\|https://github.com/lernlink/moodle-tool_langpackdropper/raw/refs/heads/main/tests/fixtures/de_droppertest_toplevel_updated.zip | tool_langpackdropper |
    And I run the scheduled task "tool_langpackdropper\task\update_language_packs"
    And I navigate to "Language > Language packs" in site administration
    And the "Installed language packs" select box should contain "de_droppertest"
    And I navigate to "Language > Language settings" in site administration
    And I set the field "Languages on language menu" to "en,de_droppertest"
    And I press "Save changes"
    And I log out
    And I log in as "teacher1"
    And I follow "Preferences" in the user menu
    And I click on "Preferred language" "link"
    And I set the field "Preferred language" to "Deutsch (droppertest) ‎(de_droppertest)‎"
    And I press "Save changes"
    And I am on "Course 1" course homepage
    Then I should see "Personen" in the ".secondary-navigation [data-key='participants']" "css_element"
