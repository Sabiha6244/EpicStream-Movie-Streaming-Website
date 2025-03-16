<?php
class CategoryContainers
{

    private $con, $username;
    public function __construct($con, $username)
    {
        $this->con = $con;
        $this->username = $username;
    }

    public function showAllCategories(){

        $query = $this->con->prepare("SELECT * FROM category");
        $query->execute();

        $html = "<div class='previewCategories'>";

        while($row = $query->fetch(PDO::FETCH_ASSOC)){
            
            $html .=$this->getCategoryHtml($row,null,true,true,);

        }

        return $html . "</div>";
    }

    public function showCategory($categoryId, $title = null) {
        // Ensure categoryId is not null
        if($categoryId === null) {
            return "<div class='previewCategories noScroll'>No category available.</div>";
        }
    
        // Prepare and execute query
        $query = $this->con->prepare("SELECT * FROM category WHERE categoryId = :categoryId");
        $query->bindValue(":categoryId", $categoryId);
        $query->execute();
    
        // Start HTML
        $html = "<div class='previewCategories noScroll'>";
    

        if ($title != null) {
            $html .= "<h2>$title</h2>";
        }
        // Fetch results and build category HTML
        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $html .= $this->getCategoryHtml($row, $title, true, true);
        }
    
        // Return completed HTML
        return $html . "</div>";
    }

    private function getVideoHtml($videoData) {
        $videoId = $videoData['id'];
        $thumbnail = $videoData['thumbnail'];
        $name = $videoData['name'];
        
        // Generate the HTML for the video
        return "<div class='videoItem'>
                    <a href='watch.php?id=$videoId'>
                        <img src='Entity/img/$thumbnail' title='$name'>
                        <span>$name</span>
                    </a>
                </div>";
    }
    
    
    private function getCategoryHtml($sqlData,$title,$tvShows,$movies){

            $categoryId = $sqlData["id"];
            $title = $title == null ? $sqlData["name"] :$title;

            if($tvShows && $movies){
                
                $entities = EntityProvider::getEntities($this->con,$categoryId,30);
            }
            else if($tvShows){
                //tv shows
            }
            else{
                //movies
            }

            if(sizeof($entities) == 0){
                return;
            }

            $entitiesHtml = "";

            $PreviewProvider = new PreviewProvider($this->con,$this->username);
            
            foreach($entities as $entity){
                
                $entitiesHtml .= $PreviewProvider->createEntityPreviewSquare($entity);
                
            }

            return "<div class = 'category'>
                   <a href='category.php?id=$categoryId'>
                        <h3>$title</h3>
                   </a>

                   <div class ='entities'>

                   $entitiesHtml
                   </div>
                   </div>";

    }
    
        
    
}
?>