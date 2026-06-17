<?php
// Permit cross-origin requests if tested rawly, and declare returning format
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method Not Allowed"]);
    exit;
}

require_once 'db.php';

// Decode JSON format from JavaScript fetch stream
$rawInput = file_get_contents("php://input");
$input = json_decode($rawInput, true);

if (!$input) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid Form Input"]);
    exit;
}

try {
    // Destinations are arrays from JavaScript, map them gracefully to strings
    $destinations = is_array($input['planner_destinations']) ? json_encode($input['planner_destinations']) : '';

    $sql = "INSERT INTO bookings (
        planner_travelertype, planner_groupsize, planner_duration, planner_destinations,
        full_name, dob, gender, nationality, email, phone, passport_number, passport_expiry,
        trip_type, travelers_count, origin, destination, departure_date, return_date, flight_class, seat_preference,
        hotel_name, room_type, check_in, check_out, hotel_guests, meal_preference, special_requests,
        travel_insurance, payment_method, card_number, billing_address
    ) VALUES (
        :planner_travelertype, :planner_groupsize, :planner_duration, :planner_destinations,
        :full_name, :dob, :gender, :nationality, :email, :phone, :passport_number, :passport_expiry,
        :trip_type, :travelers_count, :origin, :destination, :departure_date, :return_date, :flight_class, :seat_preference,
        :hotel_name, :room_type, :check_in, :check_out, :hotel_guests, :meal_preference, :special_requests,
        :travel_insurance, :payment_method, :card_number, :billing_address
    )";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':planner_travelertype' => $input['planner_travelerType'] ?? '',
        ':planner_groupsize'   => intval($input['planner_groupSize'] ?? 1),
        ':planner_duration'    => intval($input['planner_duration'] ?? 3),
        ':planner_destinations'=> $destinations,
        ':full_name'           => $input['full_name'] ?? '',
        ':dob'                 => $input['dob'] ?? '',
        ':gender'              => $input['gender'] ?? '',
        ':nationality'         => $input['nationality'] ?? '',
        ':email'               => $input['email'] ?? '',
        ':phone'               => $input['phone'] ?? '',
        ':passport_number'     => $input['passport_number'] ?? '',
        ':passport_expiry'     => $input['passport_expiry'] ?? '',
        ':trip_type'           => $input['trip_type'] ?? '',
        ':travelers_count'     => intval($input['travelers_count'] ?? 1),
        ':origin'              => $input['origin'] ?? '',
        ':destination'         => $input['destination'] ?? '',
        ':departure_date'      => $input['departure_date'] ?? '',
        ':return_date'         => $input['return_date'] ?? '',
        ':flight_class'        => $input['flight_class'] ?? '',
        ':seat_preference'     => $input['seat_preference'] ?? '',
        ':hotel_name'          => $input['hotel_name'] ?? '',
        ':room_type'           => $input['room_type'] ?? '',
        ':check_in'            => $input['check_in'] ?? '',
        ':check_out'           => $input['check_out'] ?? '',
        ':hotel_guests'        => intval($input['hotel_guests'] ?? 1),
        ':meal_preference'     => $input['meal_preference'] ?? '',
        ':special_requests'    => $input['special_requests'] ?? '',
        ':travel_insurance'    => $input['travel_insurance'] ?? '',
        ':payment_method'      => $input['payment_method'] ?? '',
        ':card_number'         => $input['card_number'] ?? '',
        ':billing_address'     => $input['billing_address'] ?? ''
    ]);

    echo json_encode(["status" => "success", "message" => "Booking complete!"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Execution Error: " . $e->getMessage()]);
}