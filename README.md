# Homey Invoice Generator

## Description
The **Homey Invoice Generator** is a custom WordPress plugin that generates invoices for bookings made through the Homey theme. The plugin utilizes **TCPDF** to create professional PDF invoices.

## Features
- Generates invoices for Homey bookings.
- Retrieves booking details, guest information, and pricing.
- Calculates SGST & CGST automatically.
- Adds property name for better identification.
- Uses **TCPDF** for high-quality PDF generation.

## Installation

### 1️ Install TCPDF
The plugin requires **TCPDF** to generate PDFs. Install it by using the method below:

#### Manual Installation
1. Download TCPDF from the official repository: [https://github.com/tecnickcom/TCPDF](https://github.com/tecnickcom/TCPDF)
2. Extract the files and upload them to your WordPress `wp-content/plugins/` directory.
3. Ensure the `tcpdf.php` file is located at:
   ```
   /srv/htdocs/wp-content/plugins/tcpdf/tcpdf.php
   ```

### 2 Install the Homey Invoice Generator Plugin
1. Download or clone this repository into the `wp-content/plugins/` directory:
   ```sh
   git clone https://github.com/yourusername/homey-invoice-generator.git
   ```
2. Activate the plugin in **WordPress Admin** under `Plugins > Installed Plugins`.

## Usage
1. Navigate to your WordPress Admin.
2. Go to `Bookings` and select a booking.
3. Append `?booking_id=BOOKING_ID` to the URL:
   ```
   https://yourwebsite.com/wp-admin/admin-ajax.php?action=generate_invoice&booking_id=123
   ```
4. The invoice will be generated as a downloadable **PDF**.

## Note
1. Works only with Homey Wordpress theme.
2. You can modify the details button in Dashbord->Invoice to https://yourwebsite.com/wp-admin/admin-ajax.php?action=generate_invoice&booking_id=123 and replace 123 with reservation_id.
3. Ensure the Directory has proper permission.
4. Use FTP client to modify code in Wordpress.
5. Use test-tcpdf.php to test if tcpdf is installed properly.

## License
This project is licensed under the **MIT License**.

## Contributing
Feel free to submit pull requests or open issues for improvements.

---
Made by Gagan

