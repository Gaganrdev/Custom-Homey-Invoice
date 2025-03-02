<?php
/*
Plugin Name: Homey custom Invoice Generator
Description: Generates invoices for Homey bookings.
Version: 1.0
Author: Gagan R
*/
add_action('wp_ajax_generate_invoice', 'generate_homey_invoice');

function generate_homey_invoice() {
    require_once('/srv/htdocs/wp-content/plugins/tcpdf/tcpdf.php'); // Correct TCPDF path
    if (!isset($_GET['booking_id'])) {
        wp_die('Missing booking ID', 400);
    }
    $booking_id = intval($_GET['booking_id']);

    $booking = get_post($booking_id);
    $booking_meta = get_post_meta($booking_id);
    $user_id = $booking->post_author;
    $user_info = get_userdata($user_id);

    $listing_id = isset($booking_meta['reservation_listing_id'][0]) ? $booking_meta['reservation_listing_id'][0] : null;
    error_log("Listing ID: " . print_r($listing_id, true)); // Debugging
    $property_title = $listing_id ? get_the_title($listing_id) : 'Property Not Found';
    error_log("Property Title: " . print_r($property_title, true)); // Debugging
    $check_in = date('F d, Y', strtotime($booking_meta['reservation_checkin_date'][0] ?? ''));
    $check_out = date('F d, Y', strtotime($booking_meta['reservation_checkout_date'][0] ?? ''));
    $nights = unserialize($booking_meta['reservation_meta'][0] ?? '')['no_of_days'] ?? 0;
    $adults = $booking_meta['reservation_adult_guest'][0] ?? 0;
    $children = $booking_meta['reservation_child_guest'][0] ?? 0;
    $accommodation_total = $booking_meta['reservation_total'][0] ?? 0;
    $taxes = unserialize($booking_meta['reservation_meta'][0] ?? '')['taxes'] ?? 0;

    // Tax
    $sgst = round($taxes / 2, 2);
    $cgst = round($taxes / 2, 2);
    $total_amount = $accommodation_total + $taxes;
    $guest_name = $user_info->display_name ?? 'N/A';
    $guest_email = $user_info->user_email ?? 'N/A';

    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetTitle('Invoice - ' . $booking_id);
    $pdf->SetMargins(10, 10, 10);
    $pdf->AddPage();

    // Add Logo
    $logo_url = 'xyz.png'; // Logo
    $pdf->Image($logo_url, 80, 15, 50, '', 'PNG'); // Center logo
    $pdf->Ln(40);

    // Company Info Box (Better Formatting)
    $pdf->SetFont('dejavusans', 'B', 12);
    $pdf->SetFillColor(230, 230, 230); 
    $pdf->Cell(190, 8, "XYZ", 1, 1, 'C', true);

    $pdf->SetFont('dejavusans', '', 10);
    $pdf->Cell(190, 6, "address", 1, 1, 'C');
    $pdf->Cell(190, 6, "Phone: xxxxxxx  |  Email: xxxxxxx", 1, 1, 'C');
    $pdf->Cell(190, 6, "xxxxxx", 1, 1, 'C');
    $pdf->Ln(10);


    // Invoice Header
    $pdf->SetFont('dejavusans', 'B', 14);
    $pdf->Cell(0, 10, "Invoice", 0, 1, 'C');
    $pdf->SetFont('dejavusans', '', 10);
    $pdf->Cell(0, 6, "Invoice No: xyz$booking_id", 0, 1, 'L');
    $pdf->Cell(0, 6, "Invoice Date: " . date('F d, Y'), 0, 1, 'L');
    $pdf->Ln(5);

    $pdf->SetFillColor(230, 230, 230); 
    $pdf->SetFont('dejavusans', 'B', 10);
    $pdf->Cell(95, 8, "Guest Details", 1, 0, 'C', true);
    $pdf->Cell(95, 8, "Booking Details", 1, 1, 'C', true);
    $pdf->SetFont('dejavusans', '', 10);
    $pdf->Cell(95, 8, "Name: $guest_name", 1, 0, 'L');
    $pdf->Cell(95, 8, "Check-in: $check_in", 1, 1, 'L');
    $pdf->Cell(95, 8, "Email: $guest_email", 1, 0, 'L');
    $pdf->Cell(95, 8, "Check-out: $check_out", 1, 1, 'L');
    $pdf->Cell(95, 8, "Adults: $adults, Children: $children", 1, 0, 'L');
    $pdf->Cell(95, 8, "Nights: $nights", 1, 1, 'L');
    $pdf->Ln(5);

    $pdf->SetFont('dejavusans', 'B', 10);
    $pdf->SetFillColor(230, 230, 230);
    $pdf->Cell(95, 8, "Description", 1, 0, 'C', true);
    $pdf->Cell(95, 8, "Amount (₹)", 1, 1, 'C', true);

    $pdf->SetFont('dejavusans', 'B', 11); 
    $pdf->SetFillColor(245, 27, 65); 
    $pdf->SetTextColor(255, 255, 255); 
    $pdf->Cell(190, 8, strtoupper($property_title), 1, 1, 'C', true); 
    $pdf->SetTextColor(0, 0, 0); 

    // Normal pricing details(Indian Tax System)
    $pdf->SetFont('dejavusans', '', 10);
    $pdf->Cell(95, 8, "Accommodation Total", 1, 0, 'L');
    $pdf->Cell(95, 8, "₹$accommodation_total", 1, 1, 'R');
    $pdf->Cell(95, 8, "SGST (6%)", 1, 0, 'L');
    $pdf->Cell(95, 8, "₹$sgst", 1, 1, 'R');
    $pdf->Cell(95, 8, "CGST (6%)", 1, 0, 'L');
    $pdf->Cell(95, 8, "₹$cgst", 1, 1, 'R');
    $pdf->Cell(95, 8, "Total Tax", 1, 0, 'L');
    $pdf->Cell(95, 8, "₹$taxes", 1, 1, 'R');

    // Grand Total with Bold & Darker Border
    $pdf->SetFont('dejavusans', 'B', 10);
    $pdf->SetFillColor(200, 200, 200);
    $pdf->Cell(95, 8, "Grand Total", 1, 0, 'L', true);
    $pdf->Cell(95, 8, "₹$total_amount", 1, 1, 'R', true);
    $pdf->Ln(30);


    // Thank You Message
    $pdf->SetFont('dejavusans', '', 10);
    $pdf->MultiCell(0, 6, "Thank you for booking with XYZ. We appreciate your business and hope you have a great stay!", 0, 'C');
    $pdf->Ln(5);

    // Output PDF
    $pdf->Output('invoice-' . $booking_id . '.pdf', 'D');
    exit();
}