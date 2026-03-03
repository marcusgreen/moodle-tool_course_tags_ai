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

namespace tool_course_tag_ai;

/**
 * Helper class for course tag AI suggestions.
 *
 * Provides functionality to generate AI-based tag suggestions for courses
 * based on the names of course items (modules/activities).
 *
 * @package    tool_course_tag_ai
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {
    /**
     * Get AI tag suggestions based on course items.
     *
     * Retrieves all course items (modules/activities) in the given course,
     * extracts their names, and sends them to an external AI/LLM asking for
     * tag suggestions. Returns the most frequently suggested tags.
     *
     * @param int $courseid The course ID
     * @param int $suggestioncount The maximum number of suggestions to return (default: 5)
     * @return array An array of suggested tags, ordered by frequency
     * @throws \moodle_exception If course not found or AI service unavailable
     */
    public static function get_ai_suggestions(int $courseid, int $suggestioncount = 5): array {
        $course = get_course($courseid);
        if (!$course) {
            throw new \moodle_exception('coursenotfound', 'error', '', $courseid);
        }

        $configprompt = get_config('tool_course_tag_ai', 'prompt');
        if (empty($configprompt)) {
            $configprompt = self::get_default_prompt();
        }

        $courseitems = self::get_course_items($courseid);
        $suggestedtags = [];

        foreach ($courseitems as $item) {
            if (empty($item->name)) {
                continue;
            }
            $prompt = $configprompt . "<<" . format_string($item->name) . ">>";
            $suggestedtags[] = self::perform_request($prompt, 'feedback', 'tool_course_tag_ai');
        }

        $tagswithcount = array_count_values($suggestedtags);
        arsort($tagswithcount);
        $returntags = array_slice($tagswithcount, 0, $suggestioncount, true);
        return array_keys($returntags);
    }

    /**
     * Get all course items (modules/activities).
     *
     * Retrieves course modules/activities for the specified course.
     * Only includes visible items unless running as administrator.
     *
     * @param int $courseid The course ID
     * @return array An array of course modules/activities with name and id
     */
    public static function get_course_items(int $courseid): array {
        global $DB;

        // Get course modules using Moodle's course_modinfo which handles all module types
        $course = get_course($courseid);
        $modinfo = get_fast_modinfo($course);

        $items = [];
        foreach ($modinfo->cms as $cm) {
            if ($cm->visible) {
                $item = new \stdClass();
                $item->id = $cm->id;
                $item->name = $cm->name;
                $item->module_type = $cm->modname;
                $items[$cm->id] = $item;
            }
        }

        return $items;
    }

    /**
     * Perform an AI/LLM request.
     *
     * Calls the LLM using either the 4.X core API or backend provided by
     * local_ai_manager (mebis) or tool_aimanager.
     *
     * @param string $prompt The prompt to send to the AI service
     * @param string $purpose The purpose of the request (e.g., 'feedback')
     * @param string $component The component requesting the AI service
     * @return string The LLM response
     * @throws \moodle_exception If AI service is not configured or request fails
     */
    public static function perform_request(string $prompt, string $purpose = 'feedback', string $component = 'tool_course_tag_ai'): string {
        $backend = get_config('qtype_aitext', 'backend');

        if ($backend == 'local_ai_manager') {
            $manager = new \local_ai_manager\manager($purpose);
            $llmresponse = (object) $manager->perform_request($prompt, $component, \context_system::instance()->id);
            if ($llmresponse->get_code() !== 200) {
                throw new \moodle_exception(
                    'err_retrievingfeedback',
                    'qtype_aitext',
                    '',
                    $llmresponse->get_errormessage(),
                    $llmresponse->get_debuginfo()
                );
            }
            return $llmresponse->get_content();
        } else if ($backend == 'core_ai_subsystem') {
            global $USER;
            $action = new \core_ai\aiactions\generate_text(
                contextid: \context_system::instance()->id,
                userid: $USER->id,
                prompttext: $prompt
            );
            $manager = \core\di::get(\core_ai\manager::class);
            $llmresponse = $manager->process_action($action);
            $responsedata = $llmresponse->get_response_data();
            return $responsedata['generatedcontent'];
        } else if ($backend == 'tool_aimanager') {
            $ai = new \tool_aiconnect\ai\ai();
            $llmresponse = $ai->prompt_completion($prompt);
            return $llmresponse['response']['choices'][0]['message']['content'];
        }

        throw new \moodle_exception('err_invalidbackend', 'tool_course_tag_ai');
    }

    /**
     * Get the default AI prompt for tag suggestions.
     *
     * @return string The default prompt template
     */
    public static function get_default_prompt(): string {
        return get_string('default_prompt', 'tool_course_tag_ai', '');
    }
}
