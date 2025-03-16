<?php

class Video
{
    private $con, $sqlData, $entity;

    public function __construct($con, $input)
    {
        $this->con = $con;

        if (is_array($input)) {
            $this->sqlData = $input;
        } else {
            $query = $this->con->prepare("SELECT * FROM videos WHERE id = :id");
            $query->bindValue(":id", $input);
            $query->execute();

            $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);

            // Handle the case where no data is found
            if ($this->sqlData === false) {
                throw new Exception("Video not found with ID: $input");
            }
        }

        if (!isset($this->sqlData["entityId"])) {
            throw new Exception("Entity ID not found in video data");
        }

        $this->entity = new Entity($con, $this->sqlData["entityId"]);
    }

    public function getId()
    {
        return $this->sqlData["id"];
    }

    public function getTitle()
    {
        return $this->sqlData["title"];
    }

    public function getDescription()
    {
        return $this->sqlData["description"];
    }

    public function getFilePath()
    {
        return $this->sqlData["filepath"];
    }

    public function getThumbnail()
    {
        return $this->entity->getThumbnail();
    }

    public function getEpisodeNumber()
    {
        return isset($this->sqlData["episode"]) ? $this->sqlData["episode"] : null;
    }

    public function incrementViews()
    {
        $query = $this->con->prepare("UPDATE videos SET views = views + 1 WHERE id = :id");
        $query->bindValue(":id", $this->getId());
        $query->execute();
    }
}
?>
