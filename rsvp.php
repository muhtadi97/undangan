<?php
header('Content-Type: application/json');

// Set timezone to Indonesia
date_default_timezone_set('Asia/Jakarta');

// Allow CORS if needed
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Get the input data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validate input
if (!$data || empty($data['name']) || empty($data['email'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Data tidak lengkap']);
    exit;
}

// Sanitize data
$name = filter_var($data['name'], FILTER_SANITIZE_STRING);
$email = filter_var($data['email'], FILTER_SANITIZE_STRING);
$jumlah = isset($data['jumlah']) ? filter_var($data['jumlah'], FILTER_SANITIZE_NUMBER_INT) : 1;
$kehadiran = isset($data['kehadiran']) ? filter_var($data['kehadiran'], FILTER_SANITIZE_STRING) : 'hadir';
$ucapan = isset($data['ucapan']) ? filter_var($data['ucapan'], FILTER_SANITIZE_STRING) : '';
$timestamp = date('Y-m-d H:i:s');

// Prepare data for storage
$rsvpData = [
    'name' => $name,
    'email' => $email,
    'jumlah' => $jumlah,
    'kehadiran' => $kehadiran,
    'ucapan' => $ucapan,
    'timestamp' => $timestamp
];

// Save to file (in production, use a database)
$filename = 'rsvp_data.json';

// Read existing data
$existingData = [];
if (file_exists($filename)) {
    $existingData = json_decode(file_get_contents($filename), true) ?: [];
}

// Add new RSVP
$existingData[] = $rsvpData;

// Save back to file
file_put_contents($filename, json_encode($existingData, JSON_PRETTY_PRINT));

// Send WhatsApp notification (optional)
$whatsappNumber = "+6281264265784"; // Replace with your number
$whatsappMessage = "Konfirmasi baru dari:\nNama: $name\nKontak: $email\nHadir: $kehadiran\nJumlah: $jumlah orang\nUcapan: $ucapan";

// Prepare response
$response = [
    'success' => true,
    'message' => 'Terima kasih atas konfirmasinya!',
    'data' => $rsvpData,
    'whatsapp_link' => "https://wa.me/$+6281264265784?text=" . urlencode($whatsappMessage)
];

echo json_encode($response);