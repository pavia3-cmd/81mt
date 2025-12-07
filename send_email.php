<?php

// Check if the request is a POST request from the form
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. --- CONFIGURATION ---
    $to_email = $_POST['to_email'] ?? 'info@81.mt'; 
    $subject = $_POST['subject'] ?? 'New Form Submission from Website (www.81.mt)';
    
    $redirect_url_success = $_POST['redirect'] ?? '/'; 
    $redirect_url_failure = 'error.html'; 

    
    // 2. --- COLLECT AND SANITIZE FORM DATA ---
    
    // Attempt to gather data from the multi-field 'Request A Quote' form
    $first_name   = filter_var($_POST['first_name'] ?? '', FILTER_SANITIZE_STRING);
    $last_name    = filter_var($_POST['last_name'] ?? '', FILTER_SANITIZE_STRING);
    $service      = filter_var($_POST['service'] ?? 'N/A', FILTER_SANITIZE_STRING); 
    
    // Attempt to gather data from the single-field 'Inquire Now' form
    $name         = filter_var($_POST['name'] ?? '', FILTER_SANITIZE_STRING);
    
    // Determine the full name: prefer first/last name, fallback to 'name' field
    $full_name = (!empty($first_name) || !empty($last_name)) ? trim($first_name . " " . $last_name) : $name;
    
    // General fields present in both forms
    $client_email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $phone        = filter_var($_POST['phone'] ?? 'N/A', FILTER_SANITIZE_STRING);
    $message      = filter_var($_POST['message'] ?? 'No message provided.', FILTER_SANITIZE_STRING);
    
    // Basic validation
    if (empty($full_name) || empty($client_email) || empty($message)) {
        header("Location: " . $redirect_url_failure);
        exit;
    }

    
    // 3. --- CONSTRUCT EMAIL BODY ---
    $email_body = "You have received a new form submission from the website.\n\n";
    $email_body .= "--- SUBMISSION DETAILS ---\n";
    $email_body .= "Client Name: " . $full_name . "\n";
    $email_body .= "Client Email: " . $client_email . "\n";
    
    // Conditional fields
    if ($phone !== 'N/A') {
        $email_body .= "Client Phone: " . $phone . "\n";
    }
    if ($service !== 'N/A') {
        $email_body .= "Service Requested: " . $service . "\n";
    }
    
    $email_body .= "Message:\n" . $message . "\n";
    $email_body .= "--------------------------\n";
    $email_body .= "Source: www.81.mt\n";

    
    // 4. --- SET EMAIL HEADERS ---
    $headers = "From: Website Contact <noreply@81.mt>" . "\r\n";
    $headers .= "Reply-To: " . $client_email . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    
    // 5. --- SEND THE EMAIL ---
    $success = mail($to_email, $subject, $email_body, $headers);

    
    // 6. --- REDIRECTION ---
    if ($success) {
        header("Location: " . $redirect_url_success);
    } else {
        header("Location: " . $redirect_url_failure);
    }
    exit;

} else {
    // If someone accesses send_email.php directly, redirect them.
    header("Location: /");
    exit;
}

?>