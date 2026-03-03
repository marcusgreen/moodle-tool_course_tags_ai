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
 * AI tag suggestions form page for a course.
 *
 * @package    tool_course_tag_ai
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

global $CFG, $OUTPUT, $PAGE, $COURSE;

// Parameters.
$courseid = required_param('courseid', PARAM_INT);
$getaisuggestions = optional_param('getaisuggestions', false, PARAM_BOOL);
$cancel = optional_param('cancel', null, PARAM_ALPHA);

// Resolve the course.
$course = get_course($courseid);

// Authentication and authorization.
require_login($course);
$context = context_course::instance($course->id);
require_capability('tool/course_tag_ai:view', $context);

// Return URL (back to course edit form).
$returnurl = new moodle_url('/course/edit.php', ['id' => $courseid]);

// Cancel handling - must happen before any output.
if ($cancel) {
    redirect($returnurl);
}

// Set up page.
$PAGE->set_url(new moodle_url('/admin/tool/course_tag_ai/index.php', ['courseid' => $courseid]));
$PAGE->set_title(get_string('heading_suggestions', 'tool_course_tag_ai'));
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout('standard');

// Params array passed to set_data().
$formparams = [
    'courseid' => $courseid,
    'suggestedtags' => [],
];

// Instantiate the form.
$form = new \tool_course_tag_ai\output\form\course_tags_form(null);

// First set_data() call - populates hidden courseid, empties tags.
$form->set_data($formparams);

// Process submitted form data.
if ($fromform = $form->get_data()) {

    if (isset($fromform->submitbutton)) {
        // Final submit: apply the tags to the course.
        \core_tag_tag::set_item_tags('core', 'course', $course->id, $context, $fromform->formtags);
        redirect($returnurl, get_string('tags_applied', 'tool_course_tag_ai'));
    }

    if (isset($fromform->getaisuggestions)) {
        // AI suggestions button was clicked: fetch suggestions and re-populate.
        try {
            $suggestioncount = (int) get_config('tool_course_tag_ai', 'suggestioncount') ?: 5;
            $suggestedtags = \tool_course_tag_ai\helper::get_ai_suggestions($courseid, $suggestioncount);
            $formparams['suggestedtags'] = $suggestedtags;
            // Second set_data() call with suggestions filled in.
            $form->set_data($formparams);
        } catch (\Exception $e) {
            // Log the error for debugging
            debugging('AI suggestions error: ' . $e->getMessage(), DEBUG_DEVELOPER);
            // Still render the form, but without suggestions
            $form->set_data($formparams);
        }
    }
}

// Output.
echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();
