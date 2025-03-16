<?php
class Entity
{
    private $con, $sqlData;

    public function __construct($con, $input)
    {
        $this->con = $con;

        if (is_array($input)) {
            $this->sqlData = $input;
        } else {
            $query = $this->con->prepare("SELECT * FROM entities WHERE id = :id");
            $query->bindValue(":id", $input, PDO::PARAM_INT);
            $query->execute();

            $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);

             //Handle the case where no data is found
           /* if ($this->sqlData === false) {
                throw new Exception("Entity not found with ID: $input");
            }*/
        }
    }


    public function getId()
    {
        return $this->sqlData["id"];
    }

    public function getName()
    {
        return $this->sqlData["name"];
    }

    public function getThumbnail()
    {
        return $this->sqlData["thumbnail"];
    }

    public function getPreview()
    {
        return $this->sqlData["preview"];
    }

    /*public function getCategoryId()
    {
        return $this->sqlData["categoryId"];
    }*/

   public function getCategoryId()
    {
        return isset($this->sqlData["categoryId"]) ? $this->sqlData["categoryId"] : null;
    }


    public function getSeasons()
    {
        $query = $this->con->prepare("SELECT * FROM videos WHERE entityId=:id AND isMovie=0 ORDER BY season, episode ASC");
        $query->bindValue(":id", $this->getId()); // Use $this->getId() to refer to the current entity's ID
        $query->execute();

        $seasons = array();
        $videos = array();

        $currentSeason = null;
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {


            if ($currentSeason != null && $currentSeason != $row["season"]) {
                $seasons[] = new Season($currentSeason, $videos);
                $videos = array();
            }

            $currentSeason = $row["season"];
            $videos[] = new Video($this->con, $row);
        }

        if (sizeof($videos) != 0) {
            $seasons[] = new Season($currentSeason, $videos);
        }

        return $seasons;
    }



}