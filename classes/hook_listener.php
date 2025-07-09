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

namespace smsgateway_whatsapp; // Changed from smsgateway_modica

use core_sms\hook\after_sms_gateway_form_hook;

/**
 * Hook listener for WhatsApp sms gateway.
 *
 * @package    smsgateway_whatsapp
 * @copyright  2025 Kewayne Davidson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_listener {
    /**
     * Hook listener for the sms gateway setup form.
     *
     * @param after_sms_gateway_form_hook $hook The hook to add to sms gateway setup.
     */
    public static function set_form_definition_for_whatsapp_sms_gateway(after_sms_gateway_form_hook $hook): void {
        // Only add these settings for our plugin.
        if ($hook->plugin !== 'smsgateway_whatsapp') { // Changed from smsgateway_modica
            return;
        }

        $mform = $hook->mform;

        $mform->addElement('static', 'information', get_string('whatsapp_information', 'smsgateway_whatsapp'));

        // API URL setting.
        $mform->addElement(
            'text',
            'whatsapp_url', // Changed
            get_string('whatsapp_url', 'smsgateway_whatsapp'),
            'maxlength="255" size="50"',
        );
        $mform->setType('whatsapp_url', PARAM_URL);
        $mform->addRule('whatsapp_url', get_string('maximumchars', '', 255), 'maxlength', 255);
        $mform->addRule('whatsapp_url', null, 'required');
        $mform->setDefault(
            elementName: 'whatsapp_url',
            defaultValue: gateway::WHATSAPP_DEFAULT_API,
        );

        // Instance ID setting.
        $mform->addElement(
            'text',
            'whatsapp_instance_id', // Changed
            get_string('whatsapp_instance_id', 'smsgateway_whatsapp'),
            'maxlength="255" size="50"',
        );
        $mform->setType('whatsapp_instance_id', PARAM_TEXT);
        $mform->addRule('whatsapp_instance_id', get_string('maximumchars', '', 255), 'maxlength', 255);
        $mform->addRule('whatsapp_instance_id', null, 'required');

        // Access Token setting.
        $mform->addElement(
            'passwordunmask',
            'whatsapp_access_token', // Changed
            get_string('whatsapp_access_token', 'smsgateway_whatsapp'),
            'maxlength="255" size="50"',
        );
        $mform->setType('whatsapp_access_token', PARAM_TEXT);
        $mform->addRule('whatsapp_access_token', get_string('maximumchars', '', 255), 'maxlength', 255);
        $mform->addRule('whatsapp_access_token', null, 'required');
    }
}
