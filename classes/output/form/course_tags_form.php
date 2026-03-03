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

namespace tool_course_tag_ai\output\form;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/lib/formslib.php');

/**
 * Form for applying AI-suggested tags to a course.
 *
 * @package     tool_course_tag_ai
 * @copyright   2025
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_tags_form extends \moodleform {

    /**
     * Definition of the form to manage course tags.
     *
     * @return void
     */
    protected function definition() {
        $mform = $this->_form;

        // Add hidden form field for course ID.
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        // Tags autocomplete element - itemtype 'course' and component 'core'
        // tells Moodle to use the core course tag collection.
        $tags = $mform->createElement(
            'tags',
            'formtags',
            get_string('tags'),
            [
                'itemtype' => 'course',
                'component' => 'core',
            ]
        );
        $mform->addElement($tags);

        // Add AI tag suggestions button.
        $mform->addElement('submit', 'getaisuggestions', get_string('getaisuggestions_button', 'tool_course_tag_ai'));

        // Add action buttons (Save/Cancel).
        $this->add_action_buttons(true, get_string('applytags', 'tool_course_tag_ai'));

        // Disable the form change checker so the AI suggestions button does not trigger
        // an "unsaved changes" warning.
        $this->_form->disable_form_change_checker();
    }

    /**
     * Sets the data for the form.
     *
     * @param array|\stdClass $data The data to set, containing 'courseid' and 'suggestedtags'.
     *
     * @return void
     */
    public function set_data($data) {
        $mform = $this->_form;
        $data = (object) $data;
        $mform->getElement('courseid')->setValue($data->courseid);
        $mform->getElement('formtags')->setValue($data->suggestedtags);
    }

    /**
     * Validates the form data.
     *
     * @param array $data The form data
     * @param array $files The uploaded files
     * @return array An array of validation errors
     */
    public function validation($data, $files) {
        // Allow the AI suggestions button to submit without validation.
        if (!empty($data['getaisuggestions'])) {
            return [];
        }
        // Require at least one tag when using the final submit button.
        if (empty($data['formtags'])) {
            return ['formtags' => get_string('error_no_tags_selected', 'tool_course_tag_ai')];
        }
        return [];
    }
}
