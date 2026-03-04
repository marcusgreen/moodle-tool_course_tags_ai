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
 * Language strings for tool_course_tags_ai.
 *
 * @package    tool_course_tags_ai
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Course Tags AI Suggestions';
$string['aisuggestions'] = 'AI Suggestions';
$string['aisuggestions_nav'] = 'AI Tag Suggestions';
$string['heading_suggestions'] = 'AI Tag Suggestions';
$string['intro_suggestions'] = 'Get AI-powered suggestions for course tags based on your course content.';
$string['suggested_tags'] = 'Suggested tags';
$string['no_suggestions'] = 'No tag suggestions could be generated. Please check that your course has activities and that the AI service is properly configured.';
$string['error_ai_service'] = 'Error retrieving AI suggestions. Please check your AI service configuration.';
$string['default_prompt'] = 'Suggest a relevant tag for a course activity. Respond with only one tag, nothing else. Activity name: ';
$string['prompt_setting'] = 'AI Prompt Template';
$string['prompt_setting_desc'] = 'Template for the prompt sent to the AI service. Use <<activity_name>> as a placeholder for the course activity name.';
$string['suggestioncount_setting'] = 'Number of Suggestions';
$string['suggestioncount_setting_desc'] = 'Maximum number of tag suggestions to return.';
$string['privacy:metadata'] = 'The Course Tags AI Suggestions tool does not store any personal data.';
$string['tool/course_tags_ai:view'] = 'View AI tag suggestions';
$string['getaisuggestions_button'] = 'Get AI Suggestions';
$string['applytags'] = 'Apply Tags';
$string['tags_applied'] = 'Tags successfully applied to the course.';
$string['error_no_tags_selected'] = 'Please select at least one tag before applying.';
$string['enable_ai_suggestions'] = 'Enable AI suggestions';
$string['enable_ai_suggestions_description'] = 'Show a button on the course tag form that fetches AI-suggested tags based on the course activities.';
$string['backend_setting'] = 'LLM Backend';
$string['backend_setting_desc'] = 'Select which LLM backend service to use for AI-powered tag suggestions.';
$string['backend_local_ai_manager'] = 'Local AI Manager (Mebis)';
$string['backend_core_ai_subsystem'] = 'Moodle Core AI Subsystem (4.5+)';
$string['backend_tool_aimanager'] = 'Tool AI Manager';
$string['err_invalidbackend'] = 'Invalid LLM backend configured. Please check the plugin settings.';
$string['err_retrievingfeedback'] = 'Error retrieving AI suggestions: {$a}';
$string['err_retrievingfeedback_checkconfig'] = 'Error retrieving AI suggestions. Please check your AI service configuration.';
