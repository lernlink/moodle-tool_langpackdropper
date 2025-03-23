moodle-tool_langpackdropper
===========================

[![Moodle Plugin CI](https://github.com/lernlink/moodle-tool_langpackdropper/workflows/Moodle%20Plugin%20CI/badge.svg?branch=MOODLE_401_STABLE)](https://github.com/lernlink/moodle-tool_langpackdropper/actions?query=workflow%3A%22Moodle+Plugin+CI%22+branch%3AMOODLE_401_STABLE)

Moodle admin tool to download and update language packs which are not located / maintained in AMOS.


Requirements
------------

This plugin requires Moodle 4.1+


Motivation for this plugin
--------------------------

With this plugin, you are able to download language packs which are not part of the official Moodle language pack (which means that they are not maintained in AMOS, the central Moodle translation system).

This is especially relevant if you are running a large number of language string modifications and want to manage these on your own code management server rather than in the language customization interface of your particular Moodle instance.
This is also relevant if you want to run the same language string modifications on multiple Moodle instances simultaneously.


Installation
------------

Install the plugin like any other plugin to folder
/admin/tool/langpackdropper

See http://docs.moodle.org/en/Installing_plugins for details on installing Moodle plugins


Usage & Settings
----------------

After installing the plugin, it does not do anything to Moodle yet.

To configure the plugin and its behaviour, please visit:
Site administration -> Language -> Language pack dropper

There, you find two settings:

### 1. Language pack URLs

This setting expects a list of language packs to be used on this Moodle installation. Each line consists of the language pack identifier and a URL where the language pack can be downloaded.

### 2. Language pack download timeout

Moodle will try to download the language pack(s) from the given URL(s) until this timeout is hit.


Capabilities
------------

This plugin does not add any additional capabilities.


Scheduled Tasks
---------------

This plugin also introduces these additional scheduled tasks:

### \tool_langpackdropper\task\update_language_packs

This task is responsible for downloading and dropping the language packs, i.e. it is the main purpose of the plugin.\
By default, the task is enabled and runs daily at 1am.


Security implications
---------------------

With this tool, Moodle will download PHP code from the given URL and will execute its content as language pack PHP files. The Moodle administrator has to make sure to only configure download URLs which do not contain anything else than language pack files. Additionally, the Moodle administrator has to make sure that an attacker cannot infiltrate malicious code into the ZIP file anytime in the future.


Theme support
-------------

This plugin acts behind the scenes, therefore it should work with all Moodle themes.
This plugin is developed and tested on Moodle Core's Boost theme.
It should also work with Boost child themes, including Moodle Core's Classic theme. However, we can't support any other theme than Boost.


Plugin repositories
-------------------

This plugin is published and regularly updated in the Moodle plugins repository:
http://moodle.org/plugins/view/tool_langpackdropper

The latest development version can be found on Github:
https://github.com/lernlink/moodle-tool_langpackdropper


Bug and problem reports
-----------------------

This plugin is carefully developed and thoroughly tested, but bugs and problems can always appear.

Please report bugs and problems on Github:
https://github.com/lernlink/moodle-tool_langpackdropper/issues


Community feature proposals
---------------------------

The functionality of this plugin is primarily implemented for the needs of our clients and published as-is to the community. We are aware that members of the community will have other needs and would love to see them solved by this plugin.

Please issue feature proposals on Github:
https://github.com/lernlink/moodle-tool_langpackdropper/issues

Please create pull requests on Github:
https://github.com/lernlink/moodle-tool_langpackdropper/pulls


Paid support
------------

We are always interested to read about your issues and feature proposals or even get a pull request from you on Github. However, please note that our time for working on community Github issues is limited.

As certified Moodle Partner, we also offer paid support for this plugin. If you are interested, please have a look at our services on https://lern.link or get in touch with us directly via team@lernlink.de.


Moodle release support
----------------------

This plugin is only maintained for the most recent major release of Moodle as well as the most recent LTS release of Moodle. Bugfixes are backported to the LTS release. However, new features and improvements are not necessarily backported to the LTS release.

Apart from these maintained releases, previous versions of this plugin which work in legacy major releases of Moodle are still available as-is without any further updates in the Moodle Plugins repository.

There may be several weeks after a new major release of Moodle has been published until we can do a compatibility check and fix problems if necessary. If you encounter problems with a new major release of Moodle - or can confirm that this plugin still works with a new major release - please let us know on Github.

If you are running a legacy version of Moodle, but want or need to run the latest version of this plugin, you can get the latest version of the plugin, remove the line starting with $plugin->requires from version.php and use this latest plugin version then on your legacy Moodle. However, please note that you will run this setup completely at your own risk. We can't support this approach in any way and there is an undeniable risk for erratic behavior.


Translating this plugin
-----------------------

This Moodle plugin is shipped with an english language pack only. All translations into other languages must be managed through AMOS (https://lang.moodle.org) by what they will become part of Moodle's official language pack.

As the plugin creator, we manage the translation into german for our own local needs on AMOS. Please contribute your translation into all other languages in AMOS where they will be reviewed by the official language pack maintainers for Moodle.


Right-to-left support
---------------------

This plugin has not been tested with Moodle's support for right-to-left (RTL) languages.
If you want to use this plugin with a RTL language and it doesn't work as-is, you are free to send us a pull request on Github with modifications.


Maintainers
-----------

lern.link GmbH\
Alexander Bias


Copyright
---------

lern.link GmbH\
Alexander Bias


Credits
-------
For comparing a downloaded language pack with an already installed language pack, this plugin uses the Diffon library by Anand Singh (see https://github.com/sudoanand/diffon).
