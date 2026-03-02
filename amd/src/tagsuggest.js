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
 * AMD module to inject "AI Suggestions" button on course tags field.
 *
 * @module     tool_course_tag_ai/tagsuggest
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export const init = (courseid, targetUrl) => {
    console.log('tool_course_tag_ai/tagsuggest: init called with courseid=' + courseid + ', targetUrl=' + targetUrl);

    // Find the tags field using the data-fieldtype attribute (set by MoodleQuickForm_tags).
    let tagsField = document.querySelector('[data-fieldtype="tags"]');

    if (tagsField) {
        console.log('tool_course_tag_ai/tagsuggest: Found tags field via [data-fieldtype="tags"]');
    }

    // Fallback to searching for #id_tags if the primary selector fails.
    if (!tagsField) {
        const tagsInput = document.getElementById('id_tags');
        if (tagsInput) {
            tagsField = tagsInput.closest('.fitem');
            if (tagsField) {
                console.log('tool_course_tag_ai/tagsuggest: Found tags field via #id_tags and closest .fitem');
            }
        }
    }

    // Guard against double-injection: check if button already exists.
    if (tagsField && tagsField.querySelector('[data-tool-course-tag-ai-btn]')) {
        console.log('tool_course_tag_ai/tagsuggest: Button already exists, skipping injection');
        return;
    }

    // If tags field not found, exit silently.
    if (!tagsField) {
        console.log('tool_course_tag_ai/tagsuggest: Tags field not found, exiting');
        return;
    }

    // Create the "AI Suggestions" button.
    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'btn btn-secondary btn-sm mt-1';
    button.setAttribute('data-tool-course-tag-ai-btn', '1');

    // Set button text from language string if available, otherwise use fallback.
    button.textContent = (window.M && window.M.str && window.M.str.tool_course_tag_ai && window.M.str.tool_course_tag_ai.aisuggestions) ? window.M.str.tool_course_tag_ai.aisuggestions : 'AI Suggestions';

    // Add click handler to navigate to the AI suggestions page.
    button.addEventListener('click', (event) => {
        event.preventDefault();
        window.location.href = targetUrl;
    });

    // Append button to the tags field container.
    tagsField.appendChild(button);
    console.log('tool_course_tag_ai/tagsuggest: Button successfully added to tags field');
};
