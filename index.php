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
 * AI tag suggestions page for a course.
 *
 * @package    tool_course_tag_ai
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

// Get and validate course ID.
$courseid = required_param('courseid', PARAM_INT);
$course = get_course($courseid);

// Check user is logged in and has access to the course.
require_login($course);

// Check capability.
$context = context_course::instance($course->id);
require_capability('tool/course_tag_ai:view', $context);

// Set up page.
$PAGE->set_pagelayout('incourse');
$PAGE->set_url(new moodle_url('/admin/tool/course_tag_ai/index.php', ['courseid' => $courseid]));
$PAGE->set_title(get_string('heading_suggestions', 'tool_course_tag_ai'));
$PAGE->set_heading($course->fullname);

// Build back button URL.
$backurl = new moodle_url('/course/edit.php', ['id' => $courseid]);

// Output page.
echo $OUTPUT->header();

// Show introduction.
echo $OUTPUT->box(get_string('intro_suggestions', 'tool_course_tag_ai'));

// Show AI suggestions based on course items.
echo $OUTPUT->heading(get_string('heading_suggestions', 'tool_course_tag_ai'), 2);

try {
    $suggestioncount = (int) get_config('tool_course_tag_ai', 'suggestioncount') ?: 5;
    $suggestions = \tool_course_tag_ai\helper::get_ai_suggestions($courseid, $suggestioncount);

    if (!empty($suggestions)) {
        echo \html_writer::start_div('alert alert-success');
        echo \html_writer::tag('strong', get_string('suggested_tags', 'tool_course_tag_ai') . ':');
        echo ' ';
        echo implode(', ', array_map(function($tag) {
            return \html_writer::tag('span', format_string($tag), ['class' => 'badge badge-primary']);
        }, $suggestions));
        echo \html_writer::end_div();
    } else {
        echo \html_writer::start_div('alert alert-warning');
        echo get_string('no_suggestions', 'tool_course_tag_ai');
        echo \html_writer::end_div();
    }
} catch (\Exception $e) {
    echo \html_writer::start_div('alert alert-danger');
    echo get_string('error_ai_service', 'tool_course_tag_ai');
    echo \html_writer::end_div();
    debugging('AI suggestions error: ' . $e->getMessage(), DEBUG_DEVELOPER);
}

// Show back button.
echo $OUTPUT->single_button($backurl, get_string('back', 'core'), 'get');

echo $OUTPUT->footer();
