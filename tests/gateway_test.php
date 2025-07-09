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

namespace smsgateway_whatsapp; // Changed

use core_sms\message;
use core_sms\message_status;
use GuzzleHttp\Psr7\Response;

/**
 * WhatsApp SMS gateway tests.
 *
 * @package    smsgateway_whatsapp
 * @category   test
 * @copyright  2025 Kewayne Davidson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \smsgateway_whatsapp\gateway
 */
final class gateway_test extends \advanced_testcase {
    public function test_send_success(): void {
        $this->resetAfterTest();

        $config = (object) [
            'whatsapp_url' => gateway::WHATSAPP_DEFAULT_API,
            'whatsapp_instance_id' => 'test_instance',
            'whatsapp_access_token' => 'test_token',
        ];

        $manager = \core\di::get(\core_sms\manager::class);
        $gw = $manager->create_gateway_instance(
            classname: gateway::class,
            name: 'whatsapp', // Changed
            enabled: true,
            config: $config,
        );

        // Mock the http client to return a successful response.
        ['mock' => $mock] = $this->get_mocked_http_client();
        $mock->append(new Response(
            status: 200,
            body: json_encode(['status' => 'success', 'message' => 'Message sent successfully.'])
        ));

        $message = $manager->send(
            recipientnumber: '+18761234567',
            content: 'Hello from Moodle!',
            component: 'core',
            messagetype: 'test',
            recipientuserid: null,
            async: false,
        );

        $this->assertInstanceOf(message::class, $message);
        $this->assertEquals(message_status::GATEWAY_SENT, $message->status);
        $this->assertEquals($gw->id, $message->gatewayid);
    }

    public function test_send_failure(): void {
        $this->resetAfterTest();

        $config = (object) [
            'whatsapp_url' => gateway::WHATSAPP_DEFAULT_API,
            'whatsapp_instance_id' => 'test_instance',
            'whatsapp_access_token' => 'test_token',
        ];

        $manager = \core\di::get(\core_sms\manager::class);
        $gw = $manager->create_gateway_instance(
            classname: gateway::class,
            name: 'whatsapp', // Changed
            enabled: true,
            config: $config,
        );

        // Mock the http client to return a failed response.
        ['mock' => $mock] = $this->get_mocked_http_client();
        $mock->append(new Response(
            status: 200,
            body: json_encode(['status' => 'error', 'message' => 'Invalid token.'])
        ));

        $message = $manager->send(
            recipientnumber: '+18761234567',
            content: 'This will fail.',
            component: 'core',
            messagetype: 'test',
            recipientuserid: null,
            async: false,
        );

        $this->assertEquals(message_status::GATEWAY_FAILED, $message->status);
        $this->assertEquals($gw->id, $message->gatewayid);
    }
}
