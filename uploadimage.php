<?php
include_once("connection.php");
session_start();

// Create (POLISHED WITH FILE VALIDATION FOR ALL DOCUMENTS) - prepare statements used
// Student information will be added to the database
// document files are now optional
// However, 2x2 photo is still requrired because since enrollment or admission for 1st year...
// ...the registrar office requires students to have a 2x2 photo

if (isset($_POST['submit'])) {
    // Initialize error tracking and uploaded file names
    $errors = [];
    $uploadedFileNames = []; // Initialize this at the very beginning

    // Gather student data
    $fname = $_POST["fname"];
    $lname = $_POST["lname"];
    $midname = $_POST["midname"];
    $minit = $_POST["minit"];
    $suffix = $_POST["suffix"];
    $course = $_POST["course"];
    $section = $_POST["section"];
    $major = $_POST["major"];
    $ylvl = $_POST["ylvl"];
    $batch = $_POST["batch"];
    $sid = $_POST["sid"];
    $email = $_POST["email"];
    $address = $_POST["address"];
    $phone = $_POST["phone"];
    $age = $_POST["age"];
    $bday = date('Y-m-d', strtotime($_POST['bday']));
    $sex = $_POST["sex"];

    // Check if SID already exists
    $checkSidQuery = "SELECT * FROM tblstudents WHERE sid = ?";
    $stmt = mysqli_prepare($con, $checkSidQuery);
    mysqli_stmt_bind_param($stmt, 's', $sid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $errors[] = 'The student ID already exists. Please enter a unique student ID.';
    }

    // Validate email (if provided)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "$email is not valid.";
    }

    // Validate profile image
    $fileName = $_FILES["image"]["name"];
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedTypes = ["jpg", "jpeg", "png", "gif"];
    $tempName = $_FILES["image"]["tmp_name"];

    if ($fileName) {
        if (!in_array($ext, $allowedTypes) || !is_uploaded_file($tempName)) {
            $errors[] = 'Invalid file type for profile image. Only .jpg, .jpeg, .png, and .gif are allowed.';
        } elseif (in_array($fileName, $uploadedFileNames)) {
            $errors[] = "Duplicate file detected: $fileName is uploaded multiple times.";
        } else {
            $uploadedFileNames[] = $fileName; // Add to the list of uploaded files
        }
    }

    // Validate documents and assign variables
    $documentFiles = [
        'file_psa' => $_FILES["file_psa"] ?? null,
        'file_goodmoral' => $_FILES["file_goodmoral"] ?? null,
        'file_transcre' => $_FILES["file_transcre"] ?? null,
        'file_tor' => $_FILES["file_tor"] ?? null,
        'file_permrec' => $_FILES["file_permrec"] ?? null,
        'file_f137' => $_FILES["file_f137"] ?? null,
        'file_f9' => $_FILES["file_f9"] ?? null,
        'file_marcon' => $_FILES["file_marcon"] ?? null, // Marriage contract
    ];

    $allowedDocumentTypes = ["pdf", "doc", "docx", "png", "jpeg", "jpg", "gif"];
    $studentFolder = "uploads/" . $sid;

    // Prepare variables for the query
    $file_psa = $_FILES['file_psa']['name'] ?? null;
    $file_goodmoral = $_FILES['file_goodmoral']['name'] ?? null;
    $file_transcre = $_FILES['file_transcre']['name'] ?? null;
    $file_tor = $_FILES['file_tor']['name'] ?? null;
    $file_permrec = $_FILES['file_permrec']['name'] ?? null;
    $file_f137 = $_FILES['file_f137']['name'] ?? null;
    $file_f9 = $_FILES['file_f9']['name'] ?? null;
    $marriageContractFileName = $_FILES['file_marcon']['name'] ?? null;

    // Handle optional files
    $optionalFilesToSave = null;
    if (!empty($_FILES['o_file']['name'][0])) {
        foreach ($_FILES['o_file']['name'] as $index => $optFileName) {
            $optTmpName = $_FILES['o_file']['tmp_name'][$index];
            $optExt = strtolower(pathinfo($optFileName, PATHINFO_EXTENSION));

            if (!in_array($optExt, $allowedDocumentTypes)) {
                $errors[] = "Invalid file type for optional file: $optFileName. Only .pdf, .doc, .docx, .png, .jpeg, .jpg, and .gif are allowed.";
            } elseif (in_array($optFileName, $uploadedFileNames)) {
                $errors[] = "Duplicate optional file detected: $optFileName.";
            } else {
                $uploadedFileNames[] = $optFileName; // Add to the list of uploaded files
            }
        }
        $optionalFilesToSave = implode(',', $_FILES['o_file']['name']);
    }

    // Validate each document file
    foreach ($documentFiles as $key => $file) {
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $docFileName = $file["name"];
            $docExt = strtolower(pathinfo($docFileName, PATHINFO_EXTENSION));

            if (!in_array($docExt, $allowedDocumentTypes)) {
                $errors[] = "Invalid file type for $key: $docFileName. Only .pdf, .doc, .docx, .png, .jpeg, .jpg, and .gif are allowed.";
            } elseif (in_array($docFileName, $uploadedFileNames)) {
                $errors[] = "Duplicate file detected: $docFileName.";
            } else {
                $uploadedFileNames[] = $docFileName; // Add to the list of uploaded files
            }
        } elseif ($file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
            $errors[] = "Error uploading file: $key. Please try again.";
        }
    }

    // If any errors, stop processing
    if (!empty($errors)) {
        $_SESSION['status'] = implode('<br>', $errors);
        header('location: students.php');
        exit();
    }

    // Create student folder only after validations pass
    if (!is_dir($studentFolder)) {
        mkdir($studentFolder, 0777, true);
    }

    // Upload profile image
    if ($fileName) {
        $profileImagePath = $studentFolder . "/" . $fileName;
        move_uploaded_file($tempName, $profileImagePath);
    }

    // Upload documents
    foreach ($documentFiles as $key => $file) {
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $docFileName = $file["name"];
            $docTargetPath = $studentFolder . "/" . $docFileName;
            move_uploaded_file($file["tmp_name"], $docTargetPath);
            encryptFile($docTargetPath);
        }
    }

    // Upload optional files directly inside the student folder
    if (!empty($_FILES['o_file']['name'][0])) {
        foreach ($_FILES['o_file']['name'] as $index => $optFileName) {
            $optTmpName = $_FILES['o_file']['tmp_name'][$index];
            $optTargetPath = $studentFolder . "/" . $optFileName;
            move_uploaded_file($optTmpName, $optTargetPath);
            encryptFile($optTargetPath);
        }
    }

    // Insert student into database
    $studentQuery = "INSERT INTO tblstudents (fname, lname, midname, minit, suffix, course, section, major, ylvl, batch, sid, email, address, phone, age, bday, sex, filename) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $studentQuery);
    mysqli_stmt_bind_param($stmt, 'ssssssssssssssssss', $fname, $lname, $midname, $minit, $suffix, $course, $section, $major, $ylvl, $batch, $sid, $email, $address, $phone, $age, $bday, $sex, $fileName);

    if (mysqli_stmt_execute($stmt)) {
        // Insert document data into tbldocuments
        $documentsQuery = "INSERT INTO tbldocuments (file_psa, file_goodmoral, file_transcre, file_tor, file_permrec, file_f137, file_f9, file_marcon, o_file, sid) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $documentsQuery);
        mysqli_stmt_bind_param(
            $stmt,
            'ssssssssss',
            $file_psa,
            $file_goodmoral,
            $file_transcre,
            $file_tor,
            $file_permrec,
            $file_f137,
            $file_f9,
            $marriageContractFileName,
            $optionalFilesToSave,
            $sid
        );

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['status'] = 'Successfully added student ' . $fname . ' ' . $lname . ' from ' . $course . ' - ' . $ylvl . ' and uploaded files.';
            header('Location: students.php?program=' . urlencode($course));
            exit();
        } else {
            $_SESSION['status'] = 'Error inserting document data.';
        }
    } else {
        $_SESSION['status'] = 'Error inserting student data.';
    }

    header('location: students.php');
    exit();
}

// Store the encryption key in the environment variable.
function encryptFile($filePath)
{
    // Retrieve the encryption key from an environment variable
    $encryption_key = getenv('ENCRYPTION_KEY');

    if ($encryption_key === false) {
        throw new Exception('Encryption key not found in environment variables.');
    }

    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

    // Read file contents to encrypt
    $data = file_get_contents($filePath);

    // Encrypt the data
    $encryptedData = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);

    if ($encryptedData === false) {
        throw new Exception('Encryption failed.');
    }

    // Combine encrypted data and IV, separating with "::"
    $finalData = $encryptedData . '::' . base64_encode($iv);

    // Write the encrypted data and IV back to the file
    if (file_put_contents($filePath, $finalData) === false) {
        throw new Exception('Failed to write encrypted data to file.');
    }

    return true;
}

// edit part 1 - used prepare statements
// populate information to text and dropdown fields.
if (isset($_POST['click_edit_btn'])) {
    if (!isset($_POST['sid'])) {
        echo json_encode(["error" => "Student ID is not set."]);
        exit;
    }

    $studentId = $_POST['sid'];
    $arrayresult = [];

    // Prepare the SQL query
    $fetch_query = "SELECT * FROM tblstudents 
                    JOIN tblcourses ON tblstudents.course = tblcourses.course 
                    JOIN tbldocuments ON tblstudents.sid = tbldocuments.sid 
                    WHERE tblstudents.sid = ?";

    // Prepare the statement
    $stmt = mysqli_prepare($con, $fetch_query);

    // Bind the parameter (s = string)
    mysqli_stmt_bind_param($stmt, 's', $studentId);

    // Execute the statement
    mysqli_stmt_execute($stmt);

    // Get the result
    $fetch_query_run = mysqli_stmt_get_result($stmt);

    // Set the content type header for JSON
    header('Content-Type: application/json');

    // Check if the query was successful
    if ($fetch_query_run) {
        if (mysqli_num_rows($fetch_query_run) > 0) {
            while ($row = mysqli_fetch_assoc($fetch_query_run)) {
                $arrayresult[] = $row; // Use associative array
            }
            echo json_encode($arrayresult);
        } else {
            // Return JSON object for no records found
            echo json_encode(["error" => "No records found!"]);
        }
    } else {
        // Handle query error
        echo json_encode(["error" => "Query failed: " . mysqli_error($con)]);
    }
} else {
    echo json_encode(["error" => "Click edit button not set."]);
}

// edit part 2 - UPDATE THE DATA - used prepare statements
// Update the student information.
if (isset($_POST['update_data'])) {
    // Fetching data from the form
    $fname = $_POST["fname"];
    $lname = $_POST["lname"];
    $midname = $_POST["midname"];
    $minit = $_POST["minit"];
    $suffix = $_POST["suffix"];
    $course = $_POST["course"];
    $section = $_POST["section"];
    $major = $_POST["major"];
    $ylvl = $_POST["ylvl"];
    $batch = $_POST["batch"];
    $sid = $_POST["sid"];
    $email = $_POST["email"];
    $address = $_POST["address"];
    $phone = $_POST["phone"];
    $age = $_POST["age"];
    $bday = date('y-m-d', strtotime($_POST['bday']));
    $sex = $_POST["sex"];

    // Fetch the current course before any update operation
    $existingCourseQuery = "SELECT course FROM tblstudents WHERE sid = ?";
    $stmt = mysqli_prepare($con, $existingCourseQuery);
    mysqli_stmt_bind_param($stmt, "s", $sid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $existingData = mysqli_fetch_assoc($result);
    $originalCourse = $existingData['course'];
    mysqli_stmt_close($stmt);

    // Define upload directory structure
    $studentFolder = "uploads/$sid/";
    if (!is_dir($studentFolder)) {
        mkdir($studentFolder, 0755, true);
    }

    // Fetch existing files and documents for the student
    $existingFilesQuery = "SELECT filename FROM tblstudents WHERE sid = ?";
    $stmt = mysqli_prepare($con, $existingFilesQuery);
    mysqli_stmt_bind_param($stmt, "s", $sid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $existingFileData = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    $existingDocsQuery = "SELECT * FROM tbldocuments WHERE sid = ?";
    $stmt = mysqli_prepare($con, $existingDocsQuery);
    mysqli_stmt_bind_param($stmt, "s", $sid);
    mysqli_stmt_execute($stmt);
    $docsResult = mysqli_stmt_get_result($stmt);
    $existingDocData = mysqli_fetch_assoc($docsResult);
    mysqli_stmt_close($stmt);

    // Allowed file types
    $allowedImageTypes = ["png", "jpeg", "jpg", "gif"];
    $allowedDocTypes = ["pdf", "doc", "docx", "png", "jpeg", "jpg", "gif"];

    // Initialize error messages array
    $errors = [];

    // Gather all file names to check for duplicates
    $allFileNames = [];
    if (!empty($_FILES['image']['name'])) $allFileNames[] = $_FILES['image']['name'];
    if (!empty($_FILES['file_psa']['name'])) $allFileNames[] = $_FILES['file_psa']['name'];
    if (!empty($_FILES['file_goodmoral']['name'])) $allFileNames[] = $_FILES['file_goodmoral']['name'];
    if (!empty($_FILES['file_transcre']['name'])) $allFileNames[] = $_FILES['file_transcre']['name'];
    if (!empty($_FILES['file_tor']['name'])) $allFileNames[] = $_FILES['file_tor']['name'];
    if (!empty($_FILES['file_permrec']['name'])) $allFileNames[] = $_FILES['file_permrec']['name'];
    if (!empty($_FILES['file_f137']['name'])) $allFileNames[] = $_FILES['file_f137']['name'];
    if (!empty($_FILES['file_f9']['name'])) $allFileNames[] = $_FILES['file_f9']['name'];
    if (!empty($_FILES['file_marcon']['name'])) $allFileNames[] = $_FILES['file_marcon']['name'];
    if (!empty($_FILES['o_file']['name'][0])) {
        foreach ($_FILES['o_file']['name'] as $optFileName) {
            $allFileNames[] = $optFileName;
        }
    }

    // Detect duplicates
    $duplicateFiles = array_filter(array_count_values($allFileNames), function ($count) {
        return $count > 1;
    });

    if (!empty($duplicateFiles)) {
        $errors[] = 'Duplicate file names detected: ' . implode(", ", array_keys($duplicateFiles)) . '. Please rename the files before uploading.';
    }

    // Function to process uploads
    function processUpload($fileInput, $existingFile, $targetDirectory, $allowedTypes)
    {
        global $errors;
        $newFileName = null;

        if (!empty($_FILES[$fileInput]["name"])) {
            $fileName = basename($_FILES[$fileInput]["name"]);
            $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
            $targetPath = $targetDirectory . $fileName;

            if (in_array($fileType, $allowedTypes)) {
                if (!empty($existingFile) && file_exists($targetDirectory . $existingFile)) {
                    unlink($targetDirectory . $existingFile);
                }

                if (move_uploaded_file($_FILES[$fileInput]["tmp_name"], $targetPath)) {
                    if ($fileInput !== 'image') {
                        encryptFile($targetPath);
                    }
                    $newFileName = $fileName;
                } else {
                    $errors[] = "Error uploading $fileInput.";
                }
            } else {
                $errors[] = "Invalid file type for $fileInput. Allowed types: " . implode(", ", $allowedTypes);
            }
        } else {
            $newFileName = $existingFile;
        }

        return $newFileName;
    }

    // Validate email (if provided)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "$email is not valid.";
    }

    // Only proceed if there are no duplicates or other errors
    if (empty($errors)) {
        $fileName = processUpload("image", $existingFileData['filename'], $studentFolder, $allowedImageTypes);
        $psaFile = processUpload("file_psa", $existingDocData['file_psa'], $studentFolder, $allowedDocTypes);
        $goodMoralFile = processUpload("file_goodmoral", $existingDocData['file_goodmoral'], $studentFolder, $allowedDocTypes);
        $transcreFile = processUpload("file_transcre", $existingDocData['file_transcre'], $studentFolder, $allowedDocTypes);
        $torFile = processUpload("file_tor", $existingDocData['file_tor'], $studentFolder, $allowedDocTypes);
        $permrecFile = processUpload("file_permrec", $existingDocData['file_permrec'], $studentFolder, $allowedDocTypes);
        $form137File = processUpload("file_f137", $existingDocData['file_f137'], $studentFolder, $allowedDocTypes);
        $form9File = processUpload("file_f9", $existingDocData['file_f9'], $studentFolder, $allowedDocTypes);
        $marriageContractFile = processUpload("file_marcon", $existingDocData['file_marcon'], $studentFolder, $allowedDocTypes);

        $optionalFilesToSave = $existingDocData['o_file'];
        if (!empty($_FILES['o_file']['name'][0])) {
            $optionalFiles = $_FILES['o_file'];
            $uploadedOptionalFiles = [];
            $validFiles = true;

            for ($i = 0; $i < count($optionalFiles['name']); $i++) {
                $optFileName = basename($optionalFiles['name'][$i]);
                $optFileType = pathinfo($optFileName, PATHINFO_EXTENSION);

                if (!in_array($optFileType, $allowedDocTypes)) {
                    $errors[] = "Invalid file type for $optFileName. Allowed types: " . implode(", ", $allowedDocTypes);
                    $validFiles = false;
                }
            }

            if ($validFiles) {
                for ($i = 0; $i < count($optionalFiles['name']); $i++) {
                    $optFileName = basename($optionalFiles['name'][$i]);
                    $optTargetPath = $studentFolder . $optFileName;

                    if (move_uploaded_file($optionalFiles['tmp_name'][$i], $optTargetPath)) {
                        encryptFile($optTargetPath);
                        $uploadedOptionalFiles[] = $optFileName;
                    } else {
                        $errors[] = "Error uploading optional file: $optFileName.";
                    }
                }

                if (!empty($uploadedOptionalFiles)) {
                    $optionalFilesToSave = implode(",", $uploadedOptionalFiles);
                }
            }
        }

        if (empty($errors)) {
            $studentQuery = "UPDATE tblstudents SET fname = ?, lname = ?, midname = ?, minit = ?, suffix = ?, course = ?, section = ?, major = ?, ylvl = ?, batch = ?, email = ?, address = ?, phone = ?, age = ?, bday = ?, sex = ?, filename = ? WHERE sid = ?";
            $stmt = mysqli_prepare($con, $studentQuery);
            mysqli_stmt_bind_param($stmt, 'ssssssssssssssssss', $fname, $lname, $midname, $minit, $suffix, $course, $section, $major, $ylvl, $batch, $email, $address, $phone, $age, $bday, $sex, $fileName, $sid);

            if (mysqli_stmt_execute($stmt)) {
                $documentsQuery = "UPDATE tbldocuments SET file_psa = ?, file_goodmoral = ?, file_transcre = ?, file_tor = ?, file_permrec = ?, file_f137 = ?, file_f9 = ?, file_marcon = ?, o_file = ? WHERE sid = ?";
                $stmt = mysqli_prepare($con, $documentsQuery);
                mysqli_stmt_bind_param($stmt, 'ssssssssss', $psaFile, $goodMoralFile, $transcreFile, $torFile, $permrecFile, $form137File, $form9File, $marriageContractFile, $optionalFilesToSave, $sid);

                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['status'] = 'Successfully updated student ' . $fname . ' ' . $lname . ' from ' . $course . ' - ' . $ylvl . ' and updated files (if any).';
                } else {
                    $_SESSION['status'] = "Error updating documents.";
                }

                mysqli_stmt_close($stmt);
            } else {
                $_SESSION['status'] = "Error updating student data.";
            }
        }
    }

    if (!empty($errors)) {
        $_SESSION['status'] = "Errors encountered: " . implode(", ", $errors);
    }

    header('Location: students.php?program=' . urlencode(empty($errors) ? $course : $originalCourse));
    exit();
}
