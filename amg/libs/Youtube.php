<?php
class Youtube
{

    public function checkYoutubeUrl($url){
        preg_match_all("#(?<=v=|v\/|vi=|vi\/|youtu.be\/)[a-zA-Z0-9_-]{11}#",
            $url, $matches);

        if(strlen($matches[0][0]) == 11){
            return $matches[0][0];
        }

        return false;
    }


    public function getYouTubeThumbnail($id){
        return "http://img.youtube.com/vi/".$id."/hqdefault.jpg";
    }

}
