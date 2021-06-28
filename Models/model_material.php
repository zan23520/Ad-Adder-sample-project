<?php

//vključi class za povezavo do baze
include_once("class_database.php");

//vključi class za povezavo do baze
include_once("class_material.php");

class Materials
{
    //Creates database connection
    private function connect()
    {
        $db = new DB();
        return $db->init();
    }

    /**
     * Get project's materials
     * @param {number} projectId
     * @param {number} userId -notYet
     */
    public function getMaterials($projectId)
    {
        $conn  = $this->connect();
        $files = array();

        //dobimo ime projekta
        $project = $conn -> prepare("SELECT ime 
                                    FROM projekti 
                                    WHERE id = :id"); // AND  uid = :uid"); še za userId
                                    
        $project -> bindParam(':id', $projectId, PDO::PARAM_INT);

        if ($project -> execute()) 
        {
            while($row = $project->fetch())
            {
                $name = $row['ime'];
            }
        }

        $materials = $conn->prepare("SELECT id, ime, tip, dimenzija, velikost, referenca 
                                       FROM materiali 
                                       WHERE pid=:pid");

        $materials->bindParam(':pid', $projectId, PDO::PARAM_INT);

        if ($materials -> execute()) 
        {
            while ($row = $materials -> fetch()) 
            {
                $id        = intval($row['id']);
                $ime       = $row['ime'];
                $tip       = $row['tip'];
                $dimenzija = $row['dimenzija'];
                $velikost  = intval($row['velikost']);
                $referenca = $row['referenca'];

                $files[] = new Material($id, $ime, $tip, $dimenzija, $velikost, $referenca);
            }
        }
        //poleg podatkov materialov pošljemo še ime projekta
        $data  = array("name" => $name, "files" => $files);
        return $data;
    }

    /**
     * Update material's name and comment
     * @param {number} matId
     * @param {string} newName
     * @param {string} newName
     */
    public function updateMaterial($matId, $newName, $comment)
    {
        //pridobi novo ime in komentar
        //

        $conn = $this->connect();

        $update = $conn -> prepare("UPDATE  materiali 
                                    SET  ime = :ime, komentar =:comment
                                    WHERE id = :id"); 

        $update->bindParam(':ime', $newName, PDO::PARAM_STR);
        $update->bindParam(':komentar', $comment, PDO::PARAM_STR);
        $update->bindParam(':id', $matId, PDO::PARAM_INT);
        $update->execute();

        if ($update -> rowCount() > 0) 
        {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Delete project's material
     * @param {number} projectId
     * @param {number} adId
     */
    public function deleteMaterial($matId, $projectId)
    {
        //pridobi referenco materiala in ga izbriši
        //izbriši DB row materiala
        $conn = $this->connect();

        $getpath = $conn -> prepare("SELECT referenca
                                     FROM materiali
                                     WHERE id = :id
                                     AND  pid = :pid");

        $delete->bindParam(':pid', $projectId, PDO::PARAM_INT);
        $getpath->bindParam(':id', $matId, PDO::PARAM_INT);
        $getpath->execute();

        $frow = $project->fetch();
        $file = $frow['referenca']; //FILE PATH

        $delete = $conn->prepare("DELETE FROM materiali
                                  WHERE id = :id
                                  AND  pid = :pid");

        $delete->bindParam(':pid', $projectId, PDO::PARAM_INT);
        $delete->bindParam(':id', $matId, PDO::PARAM_INT);
        $delete->execute();


        if (file_exists($file)) {
            unlink($file);
        } else {
            // File not found.
        }

        if ($delete -> rowCount() > 0) 
        {
            return true;
        } else {
            return false;
        }
    }

}