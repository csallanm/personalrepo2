<?php
if (isset($_GET['file']) && isset($_GET['folder'])) {
    $file = $_GET['file']; // File name from the request
    $studentFolder = $_GET['folder']; // Student folder from the request

    // Path to the encrypted file inside the student's folder
    $filePath = $studentFolder . '/' . basename($file); // Ensure the folder is included in the path

    if (file_exists($filePath)) {
        // Decrypt the file
        try {
            $decryptedData = decryptFile($filePath);

            // Prepare headers for download
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . strlen($decryptedData));

            // Output the decrypted file content
            echo $decryptedData;
            exit;
        } catch (Exception $e) {
            // Handle decryption error
            echo "Error: " . $e->getMessage();
        }
    } else {
        // File does not exist
        echo "File does not exist.";
    }
} else {
    // Missing parameters
    echo "Invalid request parameters.";
}



function decryptFile($filePath)
{
    // Retrieve the encryption key from an environment variable
    $encryption_key = getenv('ENCRYPTION_KEY');

    if ($encryption_key === false) {
        throw new Exception('Encryption key not found in environment variables.');
    }

    // Read the encrypted file
    $data = trim(file_get_contents($filePath)); // Trim whitespace and newlines

    if ($data === false) {
        throw new Exception('Failed to read the file.');
    }

    // Split the encrypted data and IV using "::" as the separator
    $parts = explode('::', $data);

    // Check if the data is in the expected format
    if (count($parts) !== 2) {
        throw new Exception('Invalid file format. Expected two parts separated by "::".');
    }

    $encryptedData = $parts[0];
    $iv = base64_decode($parts[1]);

    if ($iv === false) {
        throw new Exception('Failed to decode the IV.');
    }

    // Decrypt the data
    $decryptedData = openssl_decrypt($encryptedData, 'aes-256-cbc', $encryption_key, 0, $iv);

    if ($decryptedData === false) {
        throw new Exception('Decryption failed.');
    }

    return $decryptedData;
}
