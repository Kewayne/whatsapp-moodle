# Moodle WhatsApp Gateway Plugin

**Author:** Kewayne Davidson  
**License:** GNU GPL v3 or later  
**Requires:** Moodle 4.4+

---

## Description

This plugin provides a generic SMS gateway for Moodle to send messages via a WhatsApp API service. It allows Moodle to connect to any WhatsApp provider that supports sending messages through a simple GET request.

This plugin was adapted from the Modica SMS gateway template.

---

## Features

- Integrates with Moodle's messaging and notification system.  
- Configurable API endpoint to work with various WhatsApp API providers.  
- Securely stores your API credentials (Instance ID and Access Token).  
- Simple and clean integration.

---

## Requirements

- Moodle 4.4 or later  
- An account with a WhatsApp API provider that can send messages via a GET request

---

## Installation

### 1. Download the Plugin

Download the plugin files and place them in a folder named `whatsapp`.

### 2. Upload to Moodle

Upload the `whatsapp` folder to the `smsgateway/` directory of your Moodle installation.  
Final path:  
your_moodle_site/smsgateway/whatsapp

yaml
Copy
Edit

### 3. Install the Plugin

- Log in to your Moodle site as an administrator.  
- Go to **Site administration > Notifications**.  
- Moodle will automatically detect the new plugin.  
- Follow the on-screen instructions to install it by clicking the **"Upgrade Moodle database now"** button.

---

## Configuration

After installation, configure the gateway with your WhatsApp API provider's details.

### 1. Navigate to SMS Gateway Settings

Go to:  
**Site administration > Plugins > Message outputs > SMS**

### 2. Add the WhatsApp Gateway

- From the **Add gateway** dropdown menu, select **"WhatsApp Gateway"**  
- Click the **"Add"** button

### 3. Enter API Credentials

You will be redirected to the configuration page. Fill in the following fields:

- **WhatsApp API URL**: The full URL endpoint provided by your API provider for sending messages.  
  *Default: `https://whatsapp.aeoral.com/api/send`*

- **Instance ID**: The instance ID or application name from your API provider.

- **Access Token**: The access token or API key for authentication.

### 4. Save Changes

Click the **"Save changes"** button.

### 5. Enable the Gateway

Ensure the gateway is enabled in the main SMS settings page.

---

## Usage

Once configured and enabled, Moodle will automatically use this gateway to send SMS notifications (e.g., password reset codes, event reminders) to users who have a valid phone number in their profile — provided that the event is set to use SMS as a notification method.

---

## Disclaimer

This plugin acts as a connector. It **does not** provide the WhatsApp API service itself.  
You must have a separate account with a third-party WhatsApp API provider.  
This plugin is not affiliated with or endorsed by WhatsApp Inc.
