<?php
include_once("connection.php");

if (isset($_POST['id'])) {
    $studentId = $_POST['id'];

    // Query to get the student's data based on the student ID
    $sql = "SELECT * FROM tblstudents 
            JOIN tblcourses ON tblstudents.course = tblcourses.course 
            JOIN tbldocuments ON tblstudents.sid = tbldocuments.sid 
            WHERE tblstudents.sid = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Directory path for the student's files
        $studentFolder = "uploads/" . $studentId . "/";

        // Fetch the student image
        $fileName = $row['filename'];
        $imageUrl = $studentFolder . $fileName;

        // Output student information
        echo "<br>";
        echo "<center><img src='" . htmlspecialchars($imageUrl) . "' alt='Student Image' class='img-fluid' style='width: 150px; height: 150px; border-radius: 50%;'></center>";
        echo "<br>";
        echo "<div class='info-container mb-3'>";
        echo "<div class='info-row'><strong>First Name:</strong> <span>" . htmlspecialchars($row['fname']) . "</span></div>";
        echo "<div class='info-row'><strong>Middle Name:</strong> <span>" . htmlspecialchars($row['midname']) . "</span></div>";
        echo "<div class='info-row'><strong>Last Name:</strong> <span>" . htmlspecialchars($row['lname']) . "</span></div>";
        echo "<div class='info-row'><strong>Suffix:</strong> <span>" . htmlspecialchars($row['suffix']) . "</span></div>";
        echo "<div class='info-row'><strong>Course:</strong> <span>" . htmlspecialchars($row['course']) . "</span></div>";
        echo "<div class='info-row'><strong>Course Description:</strong> <span>" . htmlspecialchars($row['full_course']) . "</span></div>";
        echo "<div class='info-row'><strong>Section:</strong> <span>" . htmlspecialchars($row['section']) . "</span></div>";
        echo "<div class='info-row'><strong>Major:</strong> <span>" . htmlspecialchars($row['major']) . "</span></div>";
        echo "<div class='info-row'><strong>Year Level:</strong> <span>" . htmlspecialchars($row['ylvl']) . "</span></div>";
        echo "<div class='info-row'><strong>Batch Graduated:</strong> <span>";
        echo !empty($row['batch']) && $row['batch'] != 0 ? htmlspecialchars($row['batch']) : '';
        echo "</span></div>";
        echo "<div class='info-row'><strong>Student ID No.:</strong> <span>" . htmlspecialchars($row['sid']) . "</span></div>";
        echo "<div class='info-row'><strong>Email:</strong> <span>" . htmlspecialchars($row['email']) . "</span></div>";
        echo "<div class='info-row'><strong>Phone:</strong> <span>" . htmlspecialchars($row['phone']) . "</span></div>";
        echo "<div class='info-row'><strong>Age:</strong> <span>" . htmlspecialchars($row['age']) . "</span></div>";
        echo "<div class='info-row'><strong>Birthday:</strong> <span>" . htmlspecialchars($row['bday']) . "</span></div>";
        echo "<div class='info-row'><strong>Sex:</strong> <span>" . htmlspecialchars($row['sex']) . "</span></div>";
        echo "<div class='info-row'><strong>Address:</strong> <span>" . htmlspecialchars($row['address']) . "</span></div>";
        echo "</div>";
        echo "<hr class='my-4'>";

        // Prepare document retrieval
        $documents = [
            'PSA' => $row['file_psa'],
            'Good Moral' => $row['file_goodmoral'],
            'Transfer Credentials' => $row['file_transcre'],
            'Transcript of Records' => $row['file_tor'],
            'Permanent Records' => $row['file_permrec'],
            'Form 137' => $row['file_f137'],
            'Form 9' => $row['file_f9'],
            'Marriage Contract' => $row['file_marcon'],
        ];

        // Display document links
        foreach ($documents as $docTitle => $docFile) {
            if (!empty($docFile) && file_exists($studentFolder . $docFile)) {
                echo "<div class='container mt-4'>
                    <div class='row align-items-center'>
                        <div class='col-md-6'>
                            <strong>$docTitle File:</strong>
                        </div>
                        <div class='col-md-6 text-right'>
                            <a href='decrypt.php?file=" . urlencode($docFile) . "&folder=" . urlencode($studentFolder) . "' target='_blank' class='btn btn-dark btn-sm' id='btn-custom-color'>Open $docTitle</a>
                        </div>
                    </div>
                </div>";
            } else {
                echo "<div class='container mt-4'>
                    <div class='row align-items-center'>
                        <div class='col-md-6'>
                            <strong>$docTitle:</strong>
                        </div>
                        <div class='col-md-6'>
                            <span class='text-danger'>No $docTitle uploaded.</span>
                        </div>
                    </div>
                </div>";
            }
        }

        // Handle optional files
        $optionalFiles = $row['o_file'];
        if (!empty($optionalFiles)) {
            $optionalFilesArray = explode(',', $optionalFiles);

            // Only display optional files section if there are valid optional files
            if (count($optionalFilesArray) > 0 && trim($optionalFilesArray[0]) != '') {
                echo "";
                foreach ($optionalFilesArray as $optionalFile) {
                    $optionalFile = trim($optionalFile);
                    $optionalFileUrl = $studentFolder . $optionalFile;

                    // Debug: Log the full path being checked
                    error_log("Checking file: $optionalFileUrl");

                    // Use realpath to validate the file existence
                    if (file_exists($optionalFileUrl) && !empty($optionalFile)) {
                        echo "<div class='container mt-4'>
                            <div class='row align-items-center'>
                                <div class='col-md-6'>
                                    <strong>Optional File:</strong>
                                </div>
                                <div class='col-md-6 text-right'>
                                    <a href='decrypt.php?file=" . urlencode($optionalFile) . "&folder=" . urlencode($studentFolder) . "' target='_blank' class='btn btn-dark btn-sm' id='btn-custom-color'>Open " . htmlspecialchars($optionalFile) . "</a>
                                </div>
                            </div>
                        </div>";
                    } else {
                        echo "<div class='container mt-4'>
                            <div class='row align-items-center'>
                                <div class='col-md-6'>
                                    <strong>Optional File:</strong>
                                </div>
                                <div class='col-md-6 text-right mt-3'>
                                    <p><span class='text-warning'>" . htmlspecialchars($optionalFile) . " not found.</span></p>
                                </div>
                            </div>
                        </div>";
                    }
                }
            } else {
                // No optional files uploaded
                echo "<div class='container mt-4'>
                    <div class='row align-items-center'>
                        <div class='col-md-6'>
                            <strong>Optional File:</strong>
                        </div>
                        <div class='col-md-5 text-right mt-3'>
                            <p><span class='text-danger'>No optional files uploaded.</span></p>
                        </div>
                    </div>
                </div>";
            }
        } else {
            echo "<div class='container mt-4'>
                <div class='row align-items-center'>
                    <div class='col-md-6'>
                        <strong>Optional File:</strong>
                    </div>
                    <div class='col-md-5 text-right mt-3'>
                        <p><span class='text-danger'>No optional files uploaded.</span></p>
                    </div>
                </div>
            </div>";
        }
    } else {
        echo "<p>No data found for this student.</p>";
    }
}
