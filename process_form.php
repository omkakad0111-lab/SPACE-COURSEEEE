<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $child_name = htmlspecialchars($_POST['child-name']);
    $age = htmlspecialchars($_POST['age']);
    $parent_name = htmlspecialchars($_POST['parent-name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    
    // File upload handling
    $upload_dir = "uploads/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_name = $_FILES['payment-proof']['name'];
    $file_tmp = $_FILES['payment-proof']['tmp_name'];
    $file_path = $upload_dir . basename($file_name);
    
    // Move uploaded file
    if (move_uploaded_file($file_tmp, $file_path)) {
        // Email details
        $to = "omkakad0111@gmail.com";
        $subject = "New Registration: Space Explorers Course";
        
        // Email content
        $message = "
        <html>
        <head>
            <title>New Course Registration</title>
        </head>
        <body>
            <h2>New Registration for Space Explorers Course</h2>
            <p><strong>Child's Name:</strong> $child_name</p>
            <p><strong>Age:</strong> $age</p>
            <p><strong>Parent's Name:</strong> $parent_name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Phone:</strong> $phone</p>
            <p><strong>Payment Proof:</strong> $file_name (attached)</p>
        </body>
        </html>
        ";
        
        // Headers
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: $email" . "\r\n";
        
        // Boundary for attachment
        $boundary = md5(time());
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
        
        // Message with attachment
        $body = "--$boundary\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode($message));
        
        // Attachment
        $file_size = filesize($file_path);
        $handle = fopen($file_path, "r");
        $content = fread($handle, $file_size);
        fclose($handle);
        $encoded_content = chunk_split(base64_encode($content));
        
        $body .= "--$boundary\r\n";
        $body .= "Content-Type: application/octet-stream; name=\"$file_name\"\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n";
        $body .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n\r\n";
        $body .= $encoded_content . "\r\n";
        $body .= "--$boundary--";
        
        // Send email
        if (mail($to, $subject, $body, $headers)) {
            echo "<script>alert('Registration successful! We will contact you soon.'); window.location.href = 'index.html';</script>";
        } else {
            echo "<script>alert('Sorry, there was an error processing your registration. Please try again later.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Error uploading file. Please try again.'); window.history.back();</script>";
    }
} else {
    header("Location: index.html");
}
?>
