<?php

include_once('db_connect.php');

//Modul za analizo videoposnetkov
require_once('getid3/getid3.php');

//Pridobimo nastavljen projectId
/*
session_start();
$projectId = $_SESSION['projectId'];
*/

$ds     = DIRECTORY_SEPARATOR;
$folder = 'materiali';
$fcount = count($_FILES['file']['name']);

$projectId = $_POST['projectId'];
var_dump($projectId);
var_dump($fcount);

/**
 * Module for saving materials
 * @param {FILES} FILES
 * @param {number} projectId
 */
if (!empty($_FILES))
{
    for ($x = 0; $x < $fcount; $x++)
    {
        var_dump($x);
        $name     = $_FILES['file']['name'][$x];
        $size     = $_FILES['file']['size'][$x];
        $tempFile = $_FILES['file']['tmp_name'][$x]; 
        $diskPath = dirname( __FILE__ ) . $ds. $folder . $ds;
        $type     = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        //preveri če je FILE slika in pridobi dimenzijo
        if ($type == "gif"   || 
            $type == "jpeg"  || 
            $type == "jpg"   ||
            $type == "pjpeg" || 
            $type == "x-png" || 
            $type == "png")
        {
            $imgdata = getimagesize($tempFile);
            $dimenzi = $imgdata[0] . 'x' . $imgdata[1];
        }

        //preveri če je FILE mp4 video in pridobi dimenzijo
        if ($type == "mp4") 
        {
            $getID3   = new getID3();
            $fileinfo = $getID3 -> analyze($tempFile);
            $width    = $fileinfo['video']['resolution_x'];
            $height   = $fileinfo['video']['resolution_y'];
            $dimenzi  = $width . 'x' . $height;
        }

        
        //Ustvarimo ime/referenco datoteke in preverimo če ime že obstaja
        /*
        $unique   = 0;
        $uniCheck = $conn -> prepare("SELECT id FROM materiali 
                                    WHERE referenca = :referenca");      
        do //TEST THIS YOU MUST????????????????????????????????????
        {
            // Za vsak slučaj preverjamo za že uporabljeno referenco
            $randex    = rand(1000, 9999);
            $reference = $diskPath . $randex . time() . '.' . $type;

            $uniCheck -> bindParam(':referenca', $reference, PDO::PARAM_STR);
            $uniCheck -> execute();
            $numCheck = $uniCheck -> fetchAll();
            echo "numCheck:";
            var_dump($numCheck);
            //Če ime že obstaja ga ponovno ustvarimo
            if ($numCheck < 1)
            {
                $unique = 1;
                //break; //ali je vredu / spremenim while pogoj?!?!??
            }
        } while ($unique == 0);
        */
        
        $randex    = rand(1000, 9999);
        $reference = $diskPath . $randex . time() . '.' . $type;
        move_uploaded_file($tempFile,$reference);
        $naziv = pathinfo($name, PATHINFO_FILENAME);
        var_dump($reference);

        $stmt = $conn -> prepare("INSERT INTO materiali (pid, ime, tip, dimenzija, velikost, referenca) 
                                VALUES (:pid, :ime, :tip, :dimenzija, :velikost, :referenca)");

        $stmt -> bindParam(':pid', $projectId, PDO::PARAM_INT);
        $stmt -> bindParam(':ime', $naziv, PDO::PARAM_STR);
        $stmt -> bindParam(':tip', $type, PDO::PARAM_STR);
        $stmt -> bindParam(':dimenzija', $dimenzi, PDO::PARAM_STR);
        $stmt -> bindParam(':velikost', $size, PDO::PARAM_INT);
        $stmt -> bindParam(':referenca', $reference, PDO::PARAM_STR);
        $stmt -> execute();
    }
}

// if ($_SERVER['REQUEST_METHOD'] == 'POST') 

/*multiple files UPLOAD
if(isset($_POST['submit'])) {
  
    // Configure upload directory and allowed file types
    $upload_dir = 'uploads'.DIRECTORY_SEPARATOR;
    $allowed_types = array('jpg', 'png', 'jpeg', 'gif');
      
    // Define maxsize for files i.e 2MB
    $maxsize = 2 * 1024 * 1024; 
  
    // Checks if user sent an empty form 
    if(!empty(array_filter($_FILES['files']['name']))) {
  
        // Loop through each file in files[] array
        foreach ($_FILES['files']['tmp_name'] as $key => $value) {
              
            $file_tmpname = $_FILES['files']['tmp_name'][$key];
            $file_name = $_FILES['files']['name'][$key];
            $file_size = $_FILES['files']['size'][$key];
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
  
            // Set upload file path
            $filepath = $upload_dir.$file_name;
  
            // Check file type is allowed or not
            if(in_array(strtolower($file_ext), $allowed_types)) {
  
                // Verify file size - 2MB max 
                if ($file_size > $maxsize)         
                    echo "Error: File size is larger than the allowed limit."; 
  
                // If file with name already exist then append time in
                // front of name of the file to avoid overwriting of file
                if(file_exists($filepath)) {
                    $filepath = $upload_dir.time().$file_name;
                      
                    if( move_uploaded_file($file_tmpname, $filepath)) {
                        echo "{$file_name} successfully uploaded <br />";
                    } 
                    else {                     
                        echo "Error uploading {$file_name} <br />"; 
                    }
                }
                else {
                  
                    if( move_uploaded_file($file_tmpname, $filepath)) {
                        echo "{$file_name} successfully uploaded <br />";
                    }
                    else {                     
                        echo "Error uploading {$file_name} <br />"; 
                    }
                }
            }
            else {
                  
                // If file extention not valid
                echo "Error uploading {$file_name} "; 
                echo "({$file_ext} file type is not allowed)<br / >";
            } 
        }
    } 
    else {
          
        // If no files selected
        echo "No files selected.";
    }
}
*/

/*TROUBLESHOOT za informacije
        echo "<br/><br/> File name: ";
        var_dump($name);
        echo "<br/> File size bytes: ";
        var_dump($size);
        echo "<br/> File type: ";
        var_dump($type);
        echo "<br/> File dimenzije: ";
        var_dump($dimenzi);
        echo "<br/>File referenca: ";
        var_dump($reference);
*/

/*
if (!empty($_FILES)) ZA MULTIPLE FILES V ARRAYu
{
    for ($x = 0; $x < $fcount; $x++)
    {
        $name     = $_FILES['file']['name'][$x];
        $size     = $_FILES['file']['size'][$x];
        $tempFile = $_FILES['file']['tmp_name'][$x]; 
        $diskPath = dirname( __FILE__ ) . $ds. $folder . $ds;
        $type     = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        //preveri če je FILE slika in pridobi dimenzijo
        if ($type == "gif"   || 
            $type == "jpeg"  || 
            $type == "jpg"   ||
            $type == "pjpeg" || 
            $type == "x-png" || 
            $type == "png")
        {
            $imgdata = getimagesize($tempFile);
            $dimenzi = $imgdata[0] . 'x' . $imgdata[1];
        }

        //preveri če je FILE mp4 video in pridobi dimenzijo
        if ($type == "mp4") 
        {
            $getID3   = new getID3();
            $fileinfo = $getID3 -> analyze($tempFile);
            $width    = $fileinfo['video']['resolution_x'];
            $height   = $fileinfo['video']['resolution_y'];
            $dimenzi  = $width . 'x' . $height;
        }

        //Ustvarimo ime/referenco datoteke in preverimo če ime že obstaja
        $unique   = 0;
        $uniCheck = $conn -> prepare("SELECT id FROM materiali 
                                    WHERE referenca = :referenca");
                                    
        do //TEST THIS YOU MUST????????????????????????????????????????????????????????????????????????
        {
            // Za vsak slučaj preverjamo za že uporabljeno referenco
            $randex    = rand(1000, 9999);
            $reference = $diskPath . $randex . time() . '.' . $type;

            $uniCheck -> bindParam(':referenca', $reference, PDO::PARAM_STR);
            $uniCheck -> execute();
            $numCheck = $uniCheck -> fetchAll();

            //Če ime že obstaja ga ponovno ustvarimo
            if ($numCheck < 1)
            {
                $unique = 1;
                break; //ali je vredu / se sploh splača / spremenim while pogoj?!?!??????????????????????????
            }
        } while ($unique == 0);
        
        move_uploaded_file($tempFile,$reference);
        $naziv = pathinfo($name, PATHINFO_FILENAME);

        $stmt = $conn -> prepare("INSERT INTO materiali (pid, ime, tip, dimenzija, velikost, referenca) 
                                VALUES (:pid, :ime, :tip, :dimenzija, :velikost, :referenca)");

        $stmt -> bindParam(':pid', $projectId, PDO::PARAM_INT);
        $stmt -> bindParam(':ime', $naziv, PDO::PARAM_STR);
        $stmt -> bindParam(':tip', $type, PDO::PARAM_STR);
        $stmt -> bindParam(':dimenzija', $dimenzi, PDO::PARAM_STR);
        $stmt -> bindParam(':velikost', $size, PDO::PARAM_INT);
        $stmt -> bindParam(':referenca', $reference, PDO::PARAM_STR);
        $stmt -> execute();
    }
}
________________________________
    switch ($error) {
        case UPLOAD_ERR_OK:
            $valid = true;
            //validate file extensions
            if ( !in_array($ext, array('jpg','jpeg','png','gif')) ) {
                $valid = false;
                $response = 'Invalid file extension.';
            }
            //validate file size
            if ( $size/1024/1024 > 2 ) {
                $valid = false;
                $response = 'File size is exceeding maximum allowed size.';
            }
            //upload file
            if ($valid) {
                $targetPath =  dirname( __FILE__ ) . DIRECTORY_SEPARATOR. 'uploads' . DIRECTORY_SEPARATOR. $name;
                move_uploaded_file($tmpName,$targetPath);
                header( 'Location: index.php' ) ;
                exit;
            }
            break;
        case UPLOAD_ERR_INI_SIZE:
            $response = 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
            break;
        case UPLOAD_ERR_FORM_SIZE:
            $response = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
            break;
        case UPLOAD_ERR_PARTIAL:
            $response = 'The uploaded file was only partially uploaded.';
            break;
        case UPLOAD_ERR_NO_FILE:
            $response = 'No file was uploaded.';
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            $response = 'Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.';
            break;
        case UPLOAD_ERR_CANT_WRITE:
            $response = 'Failed to write file to disk. Introduced in PHP 5.1.0.';
            break;
        case UPLOAD_ERR_EXTENSION:
            $response = 'File upload stopped by extension. Introduced in PHP 5.2.0.';
            break;
        default:
            $response = 'Unknown error';
        break;
    }
*/


/*
$id=1;
$work = $conn->prepare("SELECT ime, geslo FROM uporabniki WHERE id=:id");
$work->bindParam(':id', $id, PDO::PARAM_INT);
$work->execute();
$row=$work->fetch();
var_dump($row);
                -id materiala -> id (AI)
                -id projekta --> pid
                -ime ----------> ime (text)
                -tip ----------> tip (text)
                -dimenzija ----> dimenzija (text)
                -velikost(MB) -> velikost (text)
                -referenca/lokalna pot datoteke -> referenca (text)
*/

//header("location: materiali.html");

?>