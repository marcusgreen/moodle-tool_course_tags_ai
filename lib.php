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
 * Hook implementations for tool_course_tag_ai.
 *
 * @package    tool_course_tag_ai
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Extend course navigation to add AI tag suggestions link.
 *
 * @param navigation_node $navigation The navigation node to extend.
 * @param stdClass $course The course object.
 * @param context $context The course context.
 */
function tool_course_tag_ai_extend_navigation_course(
    \navigation_node $navigation,
    \stdClass $course,
    \context $context
): void {
    global $SITE;

    // Skip the site course.
    if ($course->id == $SITE->id) {
        return;
    }

    // Check capability.
    if (!has_capability('tool/course_tag_ai:view', $context)) {
        return;
    }

    // Build URL to the AI suggestions page.
    $url = new \moodle_url('/admin/tool/course_tag_ai/index.php', ['courseid' => $course->id]);

    // Add navigation link.
    $navigation->add(
        get_string('aisuggestions_nav', 'tool_course_tag_ai'),
        $url,
        \navigation_node::TYPE_SETTING,
        null,
        'course_tag_ai',
        new \pix_icon('i/settings', '')
    );
}

