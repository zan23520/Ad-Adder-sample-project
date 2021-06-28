<?php

include_once("db_connect.php");

include_once("class_users.php");

/**
 * Login user
 * @param {string} ime
 * @param {string} geslo
 * @param {DATABASE} conn
 */
function loginUser($ime, $geslo, $conn) 
{
    //validiraj podatke!!!!!!!!!!!!!!!!!!!!
    //preveri bazo za podatke
    //zabeleži TIMESTAMP OF LAST LOGIN

    //$ime   = 'žan';//$_POST['uname'];//$conn->real_escape_string($_POST['aname']);
    //$geslo = 'password';//$_POST['pass'];//$conn->real_escape_string($_POST['apass']);
    //$hash = hash('md5', $pass);
    
    $log = $conn->prepare("SELECT id 
                           FROM uporabniki 
                           WHERE ime=:ime AND geslo=:geslo");

    $log->bindParam(':ime', $ime, PDO::PARAM_STR);
    $log->bindParam(':geslo', $geslo, PDO::PARAM_STR);


    if ($log -> execute()) 
    {
        //pridobim userId in naredim sejo 
        $loguser = $log->fetch();
        $userId  = $loguser['id'];
        $_SESSION['client'] = $ime;
        $_SESSION['clientid'] = $userId;

        //dodam Last Login TimeStamp uporabniku (ALI PREVERIM TS DODAJANJE???????????????)
        $timestamp = time();
        $lastLogin = $conn -> prepare("UPDATE uporabniki 
                                   SET lastLogin=:time 
                                   WHERE id=:id");

        $lastLogin -> bindParam(':time', $timestamp, PDO::PARAM_INT);
        $lastLogin -> bindParam(':id', $userId, PDO::PARAM_INT);
        $lastLogin -> execute();

        //Za pridobitev datum formata iz TIMESTAMP int: date('d-m-Y H:i:s', $timestamp);
        echo "success";
    } else {
        echo "error";
    }
}

/**
 * Authorize user login
 */
function getSession($conn)
{   
    //preveri za veljavnost seje
    //vrni podatke ime in userId
    if (isset($_SESSION['client']) && isset($_SESSION['clientId']))
    {
        $client   = $_SESSION['client'];
        $clientId = $_SESSION['clientId'];

        $data = new User($clientId, $client);
    } else {
        $data = 'error';
    }
    echo json_encode($data);
}

/**
 * Update user's password
 * @param {number} userId
 * @param {string} username
 * @param {string} password
 * @param {DATABASE} conn
 */
function updatePass($userId, $username, $password, $conn) 
{
    //validiraj podatke
    //preveri bazo za podatke
    //hash gesla - zaenkrat NE !!!
    //apliciraj spremembo gesla
}

/**
 * Register new user
 * @param {string} username
 * @param {string} password
 * @param {DATABASE} conn
 */
function registerUser($username, $password, $conn) 
{
    //validiraj podatke
    //hash gesla
    //preveri da ni že obstoječih duplikatov
    //ustvari novega uporabnika
    $ime   = $username;
    $geslo = $password;
    $datum = time(); 
    //Za pridobitev datum formata iz TIMESTAMP int: date('d-m-Y H:i:s', $timestamp);

    $check = $conn -> prepare("SELECT id 
                               FROM uporabniki 
                               WHERE ime=:ime");

    $check -> bindParam(':ime', $ime, PDO::PARAM_STR);

    if (!($check -> execute())) 
    { 
        //ONLY PDO!!!!!, no checking yet !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        $newUser = $conn -> prepare("INSERT INTO uporabniki (ime, geslo, dateOfReg) 
                                    VALUES (:ime, :geslo, :datum)");
        $newUser -> bindParam(':ime', $ime, PDO::PARAM_STR);
        $newUser -> bindParam(':geslo', $geslo, PDO::PARAM_STR);
        $newUser -> bindParam(':datum', $datum, PDO::PARAM_INT);
        $newUser -> execute();
        echo 'success';
    } else {
        echo 'error';
    }
}


/*
DBTabela->Uporabniki: -primary key ----------> id
					  -uporabniško ime ------> ime
					  -geslo (md5 hash?) ----> geslo
					  -last login(timestamp)-> lastLogin
					  -datum registracije ---> dateOfReg
*/

/** MAIN ___________________________________________________________________PRIMARY
 * Get input and decode it into array
 */


$ajaxCall = json_decode($_POST['data'], true);
//var_dump($ajaxCall);

/**
 * Login user
 */
if (isset($ajaxCall['loginUser'])) 
{
    $username = $ajaxCall['username'];
    $password = $ajaxCall['password'];
    loginUser($username, $password, $conn);
}

/**
 * Get Session
 */
if (isset($ajaxCall['getSession'])) 
{
    getSession($conn);
}

if (isset($ajaxCall['regUser'])) 
{
    $username = $ajaxCall['username'];
    $password = $ajaxCall['password'];
    registerUser($username, $password, $conn);
}

?>