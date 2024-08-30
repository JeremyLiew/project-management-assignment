<?php
header("Content-Type: application/json");
require "aboutUsData.php";

if (!empty($_GET['id'])) {
    $id = $_GET['id'];
    $member = get_member($id);

    if (empty($member)) {
        response(200, "Member Not Found", NULL);
    } else {
        response(200, "Member Found", $member);
    }
} else {
    response(400, "Invalid Request", NULL);
}

function response($status, $status_message, $data) {
    header("HTTP/1.1 " . $status);

    $response['status'] = $status;
    $response['status_message'] = $status_message;
    $response['data'] = $data;

    echo json_encode($response);
}
?>
