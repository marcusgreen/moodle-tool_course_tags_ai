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

namespace tool_course_tags_ai;

/**
 * AI Bridge class for handling LLM requests through different backends.
 *
 * @package    tool_course_tags_ai
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class AiBridge {
    /** @var int The context ID for AI requests */
    private $contextid;

    /**
     * Constructor for the AI bridge class.
     *
     * @param int $contextid The context ID for AI requests
     */
    public function __construct(int $contextid) {
        $this->contextid = $contextid;
    }

    /**
     * Perform an AI/LLM request using the configured backend.
     *
     * Calls the LLM using either the 4.5 core api or the backend provided by
     * local_ai_manager (mebis) or tool_aimanager.
     *
     * @param string $prompt The prompt to send to the AI service
     * @param string $purpose The purpose of the request (e.g., 'feedback')
     * @return string The AI response content
     * @throws \moodle_exception If there's an error retrieving feedback or invalid backend
     */
    public function perform_request(string $prompt, string $purpose = 'feedback'): string {
        $backend = get_config('tool_course_tags_ai', 'backend');

        if ($backend == 'local_ai_manager') {
            return $this->handle_local_ai_manager($prompt, $purpose);
        } else if ($backend == 'core_ai_subsystem') {
            return $this->handle_core_ai_subsystem($prompt);
        } else if ($backend == 'tool_aimanager') {
            return $this->handle_tool_aimanager($prompt);
        }

        throw new \moodle_exception('err_invalidbackend', 'tool_course_tags_ai');
    }

    /**
     * Handle request using local_ai_manager backend.
     *
     * @param string $prompt The prompt to send
     * @param string $purpose The purpose of the request
     * @return string The AI response
     * @throws \moodle_exception
     */
    private function handle_local_ai_manager(string $prompt, string $purpose): string {
        $manager = new \local_ai_manager\manager($purpose);
        $llmresponse = $manager->perform_request($prompt, 'tool_course_tags_ai', $this->contextid);

        // Validate that response is an object with required methods
        if (!is_object($llmresponse)) {
            throw new \moodle_exception('err_retrievingfeedback_checkconfig', 'tool_course_tags_ai');
        }

        // Check response status code
        if (!method_exists($llmresponse, 'get_code')) {
            throw new \moodle_exception('err_retrievingfeedback_checkconfig', 'tool_course_tags_ai');
        }

        if ($llmresponse->get_code() !== 200) {
            $errormessage = method_exists($llmresponse, 'get_errormessage') ?
                $llmresponse->get_errormessage() : 'Unknown error';
            $debuginfo = method_exists($llmresponse, 'get_debuginfo') ?
                $llmresponse->get_debuginfo() : '';
            throw new \moodle_exception(
                'err_retrievingfeedback',
                'tool_course_tags_ai',
                '',
                $errormessage,
                $debuginfo
            );
        }

        // Validate get_content method exists
        if (!method_exists($llmresponse, 'get_content')) {
            throw new \moodle_exception('err_retrievingfeedback_checkconfig', 'tool_course_tags_ai');
        }

        return $llmresponse->get_content();
    }

    /**
     * Handle request using core_ai_subsystem backend.
     *
     * @param string $prompt The prompt to send
     * @return string The AI response
     * @throws \moodle_exception
     */
    private function handle_core_ai_subsystem(string $prompt): string {
        global $USER;

        $action = new \core_ai\aiactions\generate_text(
            contextid: $this->contextid,
            userid: $USER->id,
            prompttext: $prompt
        );
        $manager = \core\di::get(\core_ai\manager::class);
        $llmresponse = $manager->process_action($action);
        $responsedata = $llmresponse->get_response_data();

        // Validate response data contains the required 'generatedcontent' key
        if (!is_array($responsedata) || empty($responsedata['generatedcontent'])) {
            throw new \moodle_exception('err_retrievingfeedback_checkconfig', 'tool_course_tags_ai');
        }

        return $responsedata['generatedcontent'];
    }

    /**
     * Handle request using tool_aimanager backend.
     *
     * @param string $prompt The prompt to send
     * @return string The AI response
     * @throws \moodle_exception
     */
    private function handle_tool_aimanager(string $prompt): string {
        if (class_exists('\tool_aiconnect\ai\ai')) {
            $ai = new \tool_aiconnect\ai\ai();
            $llmresponse = $ai->prompt_completion($prompt);
            return $llmresponse['response']['choices'][0]['message']['content'];
        } else {
            throw new \moodle_exception('err_retrievingfeedback_checkconfig', 'tool_course_tags_ai', '');
        }
    }
}
