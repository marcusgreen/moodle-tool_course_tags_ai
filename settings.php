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
 * Admin settings for tool_course_tag_ai.
 *
 * @package    tool_course_tag_ai
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    // Create settings page under 'tools' category.
    $settings = new \admin_settingpage('tool_course_tag_ai', get_string('pluginname', 'tool_course_tag_ai'));

    // Add setting to enable/disable AI suggestions button.
    $settings->add(new \admin_setting_configcheckbox(
        'tool_course_tag_ai/enable_ai_suggestions',
        get_string('enable_ai_suggestions', 'tool_course_tag_ai'),
        get_string('enable_ai_suggestions_description', 'tool_course_tag_ai'),
        0
    ));

    // Add setting for custom prompt template.
    $settings->add(new \admin_setting_configtextarea(
        'tool_course_tag_ai/prompt',
        get_string('prompt_setting', 'tool_course_tag_ai'),
        get_string('prompt_setting_desc', 'tool_course_tag_ai'),
        get_string('default_prompt', 'tool_course_tag_ai'),
        PARAM_TEXT
    ));

    // Add setting for number of suggestions to return.
    $settings->add(new \admin_setting_configtext(
        'tool_course_tag_ai/suggestioncount',
        get_string('suggestioncount_setting', 'tool_course_tag_ai'),
        get_string('suggestioncount_setting_desc', 'tool_course_tag_ai'),
        5,
        PARAM_INT,
        3
    ));

    $ADMIN->add('tools', $settings);
}
