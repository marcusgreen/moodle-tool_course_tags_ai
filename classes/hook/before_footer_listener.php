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
 * Hook listener for before_footer_html_generation hook.
 *
 * @package    tool_course_tag_ai
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_course_tag_ai\hook;

use core\hook\output\before_footer_html_generation;

/**
 * Hook listener to inject JavaScript module on course edit form.
 */
class before_footer_listener {
    /**
     * Handle the before_footer_html_generation hook.
     *
     * Injects the tagsuggest AMD module on course edit pages.
     *
     * @param before_footer_html_generation $hook The hook instance.
     */
    public static function before_footer_html_generation(before_footer_html_generation $hook): void {
        global $PAGE;

        // Get course ID from URL parameter.
        $courseid = $PAGE->url->get_param('id');

        // Only process if we have a course ID (indicates we're on course/edit.php).
        if (empty($courseid)) {
            return;
        }

        // Check if we're on a course-related page.
        if (strpos($PAGE->url->get_path(), '/course/edit.php') === false) {
            return;
        }

        // Check capability.
        $course = \get_course($courseid);
        if (!$course || !has_capability('tool/course_tag_ai:view', \context_course::instance($course->id))) {
            return;
        }

        // Build target URL for AI suggestions page.
        $targeturl = new \moodle_url('/admin/tool/course_tag_ai/index.php', ['courseid' => $courseid]);

        // Load AMD module to inject button.
        $PAGE->requires->js_call_amd('tool_course_tag_ai/tagsuggest', 'init', [$courseid, $targeturl->out(false)]);
    }
}
