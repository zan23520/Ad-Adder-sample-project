<?php

//vključi class za povezavo do baze
include_once("class_database.php");

//vključi razred Projekt
include_once("class_project.php");

class Projects
{
    //Creates database connection
    private function connect()
    {
        $db = new DB();
        return $db->init();
    }

    /**
     * Returns user's projects
     * @param {number} userId
     */
    public function getProjects ($userId)
    {
        $data = array();
        $conn = $this->connect();

        $projects = $conn -> prepare("SELECT * 
                                      FROM projekti 
                                      WHERE uid=:uid");

        $projects -> bindParam(':uid', $userId, PDO::PARAM_INT);

        if ($projects->execute()) 
        {
            while ($row = $projects -> fetch()) 
            {   
            $id       = intval($row['id']);
            $uid      = intval($row['uid']);
            $ime      = $row['ime'];
            $oglasvlc = $row['oglasevalec'];
            $termin   = $row['termin'];
            $format   = $row['format'];
            $material = intval($row['material']);

            $data[] = new Project($id, $uid, $ime, $oglasvlc, 
                                $termin, $format, $material);
            }
        }
        return $data;
    }

    /**
     * Creates new project for user
     * @param {number} userId
     * @param {string} name
     */
    public function newProject($userId, $name)
    {
        $conn = $this->connect();

        $newRow = $conn -> prepare("INSERT INTO projekti 
        (uid, ime, oglasevalec, termin, format, material) 
        VALUES (:uid, :ime, 0, 0, 0, 0)");

        $newRow -> bindParam(':ime', $name, PDO::PARAM_STR);
        $newRow -> bindParam(':uid', $userId, PDO::PARAM_INT);
        $newRow -> execute();

        if ($newRow -> rowCount() > 0) 
        {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Deletes project and all its materials
     * @param {number} userId
     * @param {number} projectId
     */
    public function deleteProject($projectId, $userId)
    {
        $conn = $this->connect();

        $delete = $conn -> prepare("DELETE FROM projekti
                                    WHERE id = :pid
                                    AND  uid = :uid");

        $delete -> bindParam(':pid', $projectId, PDO::PARAM_INT);
        $delete -> bindParam(':uid', $userId, PDO::PARAM_INT);
        //$delete -> execute();

        //briši tudi materiale tega projekta!!!
        $deleteMat = $conn -> prepare("DELETE FROM materiali
                                       WHERE pid = :pid");

        $deleteMat -> bindParam(':pid', $projectId, PDO::PARAM_INT);
        //$deleteMat -> execute();

        $getpath = $conn -> prepare("SELECT referenca
                                     FROM materiali
                                     WHERE pid = :pid");

        $delete->bindParam(':pid', $projectId, PDO::PARAM_INT);

        if ($getpath -> execute()) 
        {
            while($row = $getpath->fetch())
            {
                $file = $row['referenca'];
                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }
        $delete -> execute();
        $deleteMat -> execute();

        if ($delete -> rowCount() > 0) 
        {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Update project's name
     * @param {number} projectId
     * @param {number} userId --NOT YET!!!
     * @param {string} name
     */
    public function updateProject($projectId, $newName)
    {
        $conn = $this->connect();

        $update = $conn -> prepare("UPDATE  projekti 
                                    SET  ime = :ime
                                    WHERE id = :pid"); //AND   uid = :uid"); 

        $update->bindParam(':ime', $newName, PDO::PARAM_STR);
        $update->bindParam(':pid', $projectId, PDO::PARAM_INT);
        //$update->bindParam(':uid', $userId, PDO::PARAM_INT);
        $update->execute();

        if ($update -> rowCount() > 0) 
        {
            return true;
        } else {
            return false;
        }
    }
}

//EOF