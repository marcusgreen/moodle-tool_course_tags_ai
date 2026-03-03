# moodle-tool_course_tag_ai

## Overview

**Course Tag AI** is a Moodle admin tool that generates AI-powered tag suggestions for courses based on their content. It automatically analyzes the names and types of course items/activities and recommends relevant tags using artificial intelligence.

## Features

- **Automatic Tag Suggestions**: Generates intelligent tag recommendations for courses
- **Course Content Analysis**: Analyzes all visible course modules and activities (forums, assignments, quizzes, etc.)
- **Multiple AI Backends**: Supports various AI services:
  - `local_ai_manager` (Mebis)
  - `core_ai_subsystem` (Moodle 4.x)
  - `tool_aimanager`
- **Configurable**: Customize the number of suggestions and the AI prompt template
- **Frequency-based Ranking**: Suggests tags ordered by relevance based on frequency

## Requirements

- Moodle 4.0+
- An AI service configured (local_ai_manager, core_ai_subsystem, or tool_aimanager)

## Installation

1. Place the plugin in `/admin/tool/course_tag_ai/`
2. Navigate to Site Administration > Notifications to complete installation
3. Configure AI settings in Site Administration > Tools > Course Tag AI

## Configuration

### Settings

- **AI Prompt Template**: Customize the prompt sent to the AI service for generating suggestions
- **Suggestion Count**: Set the number of tag suggestions to return (default: 5)

### Language

The plugin supports language customization through language pack strings.

## How It Works

1. The tool queries all visible course modules in the selected course
2. Module names are extracted and sent to the configured AI service
3. The AI generates tag suggestions based on the course content
4. Suggestions are returned ranked by frequency of relevance
5. Tags can be applied to the course for better organization and discovery

## Related Plugins

This plugin is complementary to **qbank_bulktags**, which provides similar AI-powered tagging functionality for question banks.

## License

This plugin follows Moodle's standard license terms.
