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

namespace smsgateway_modica;

use core\http_client;
use core_sms\manager;
use core_sms\message;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Modica SMS gateway using GET request for WhatsApp.
 *
 * @package    smsgateway_modica
 * @copyright  2025 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gateway extends \core_sms\gateway {
    /**
     * @var string MODICA_DEFAULT_API The default API endpoint for Modica.
     */
    public const MODICA_DEFAULT_API = 'https://whatsapp.aeoral.com/api/send';

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
            'instance_id' => $this->config->modica_application_name,
            'access_token' => $this->config->modica_application_password,
        ];

        $client = \core\di::get(http_client::class);

        try {
            $response = $client->get(
                uri: self::MODICA_DEFAULT_API,
                options: [
                    'query' => $queryparams,
                ]
            );

            $responsebody = $response->getBody()->getContents();
            $statuscode = $response->getStatusCode();

            if ($statuscode === 200) {
                $status = \core_sms\message_status::GATEWAY_SENT;
            } else {
                $status = \core_sms\message_status::GATEWAY_FAILED;
            }

            // Output the API result
            debugging("Modica GET API response status: $statuscode", DEBUG_DEVELOPER);
            debugging("Modica GET API response body: $responsebody", DEBUG_DEVELOPER);

        } catch (GuzzleException $e) {
            $status = \core_sms\message_status::GATEWAY_FAILED;
            debugging("Modica GET API exception: " . $e->getMessage(), DEBUG_DEVELOPER);
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
