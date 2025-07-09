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

use core\http_client;
use core_sms\manager;
use core_sms\message;
use GuzzleHttp\Exception\GuzzleException;

/**
 * WhatsApp SMS gateway using a GET request.
 *
 * @package    smsgateway_whatsapp
 * @copyright  2025 Kewayne Davidson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gateway extends \core_sms\gateway {
    /**
     * @var string WHATSAPP_DEFAULT_API The default API endpoint for the WhatsApp service.
     */
    public const WHATSAPP_DEFAULT_API = 'https://whatsapp.aeoral.com/api/send';

    #[\Override]
    public function send(message $message): message {
        $recipientnumber = manager::format_number(
            phonenumber: $message->recipientnumber,
            countrycode: $this->config->countrycode ?? null,
        );

        // Strip +, (), spaces, dashes etc. to keep only digits.
        $recipientnumber = preg_replace('/[^\d]/', '', $recipientnumber);

        // Prepare the query parameters.
        $queryparams = [
            'number' => $recipientnumber,
            'type' => 'text',
            'message' => $message->content,
            'instance_id' => $this->config->whatsapp_instance_id,      // Changed from modica_application_name
            'access_token' => $this->config->whatsapp_access_token, // Changed from modica_application_password
        ];

        $client = \core\di::get(http_client::class);

        try {
            $response = $client->get(
                uri: self::WHATSAPP_DEFAULT_API,
                options: [
                    'query' => $queryparams,
                ]
            );

            $responsebody = $response->getBody()->getContents();
            $statuscode = $response->getStatusCode();

            // The API returns 200 for both success and failure, so we check the response body.
            // A success response contains "status":"success".
            $responsedata = json_decode($responsebody);
            if ($statuscode === 200 && isset($responsedata->status) && $responsedata->status === 'success') {
                $status = \core_sms\message_status::GATEWAY_SENT;
            } else {
                $status = \core_sms\message_status::GATEWAY_FAILED;
            }

            // Output the API result for debugging.
            debugging("WhatsApp GET API response status: $statuscode", DEBUG_DEVELOPER);
            debugging("WhatsApp GET API response body: $responsebody", DEBUG_DEVELOPER);

        } catch (GuzzleException $e) {
            $status = \core_sms\message_status::GATEWAY_FAILED;
            debugging("WhatsApp GET API exception: " . $e->getMessage(), DEBUG_DEVELOPER);
        }

        return $message->with(
            status: $status,
        );
    }

    #[\Override]
    public function get_send_priority(message $message): int {
        return 50;
    }
}
