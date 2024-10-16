<?php

// * Copyright 2021-2024 SnehTV, Inc.
// * Licensed under MIT (https://github.com/mitthu786/TS-JioTV/blob/main/LICENSE)
// * Created By : TechieSneh

error_reporting(0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
$DATA_FOLDER = "../assets/data";
include "functions.php";

// Check if the authentication cookie is already set
if (isset($_COOKIE['authToken'])) {
    // Use the existing token from the cookie
    $authToken = $_COOKIE['authToken'];
    // Verify the token if needed (optional step)
} else {
    // If there's no cookie, refresh the token
    $new_auth = refresh_jio_token();

    if ($new_auth["status"] == "error") {
        $msg = $new_auth["message"];
        header("Location: index.php?OtpError&msg=" . urlencode($msg));
        exit();
    } else {
        $old_data = getCRED();
        $old_data = json_decode($old_data, true);
        $old_data["authToken"] = $new_auth["authToken"];
        $new_auth = json_encode($old_data);
        $key_data = file_get_contents($DATA_FOLDER . "/credskey.jtv");
        file_put_contents($DATA_FOLDER . "/creds.jtv", encrypt_data($new_auth, $key_data));

        // Set a cookie to keep the user logged in for a long time (e.g., 30 days)
        setcookie("authToken", $new_auth["authToken"], time() + (30 * 24 * 60 * 60), "/"); // 30 days
        header("Location: index.php?success");
        exit();
    }
}
?>
