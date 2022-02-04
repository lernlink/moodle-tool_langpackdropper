<?php
namespace Hack4mer\Diffon;
/**
 * Diffon
 * A PHP library to find the differences between two given directories
 *
 */
class Diffon{

    /**
     * The directory to compare
     * @var string
     */
    protected $source;


    /**
     * The directory with which $source is to be compared
     * @var string
     */
    protected $destination;

    /**
     * Content of the source directory, stored to prevent multiple scandir calls
     * @var array
     */
    protected $sourceContent;

    /**
     * Content of the destination directory, stored to prevent multiple scandir calls
     * @var array
     */
    protected $destinationContent;


    /**
     * Whether to check sub-directories recursively , till the end of the tree
     * @var boolean
     */
    protected $recursiveMode;


    function __construct(){
        $this->recursiveMode = false;
    }

    /**
     * Enables/Disables recursive mode; for checking of sub-dirs in the tree
     * @param boolean $enabled whether to enable recursive mode
     */
    public function setRecursiveMode($enabled=true){
        $this->recursiveMode = $enabled;
        return $this;
    }

    /**
     * Set the path for directory to compare
     * @param string $path Path of the directory
     * @return Diffon the instance upon which the method was called
     */
    public function setSource($path){
        $this->source = $path;
        return $this;
    }

    /**
     * Set the path for directory with which source directory is to compared
     * @param string $path Path of the directory
     * @return Diffon the instance upon which the method was called
     */
    public function setDestination($path){
        $this->destination = $path;
        return $this;
    }

    /**
     * Shows the directories being compared
     * @return array An array containing path for the source and destination directories
     */
    public function showDirs(){
        return [
                "source" => 		$this->source,
                "destination" => 	$this->destination
        ];
    }

    /**
     * Returns the result of the comparison
     * @return array array containing the result
     */
    public function diff(){
        $onlyInSource 		= $this->onlyIn("source");
        $onlyInDestination 	= $this->onlyIn("destination");
        $commonContent 		= $this->getCommonContent();
        $commonContentWithDiff = $this->getCommonContentWithDiff();

        $diff = [
                "only_in_source" => $onlyInSource,
                "only_in_destination" => $onlyInDestination,
                "in_both" => $commonContent,
                "not_same" => $commonContentWithDiff
        ];

        return $diff;
    }


    /**
     * Check files/directories that exist in only either in source or in destination directories
     * @param  string $which which directory to show the files for, allowed values: source,destination
     * @return array         Array of the files that exist in only one of the directories
     */
    public function onlyIn($which){

        $sourceContent = $this->getSourceContent();
        $destinationContent = $this->getDestinationContent();


        if($which=="source"){
            return array_diff($sourceContent, $destinationContent);
        }else if($which=="destination"){
            return array_diff($destinationContent, $sourceContent);
        }

        throw new \Exception('Allowed parameter values are "source"  and "destination"');
    }

    /**
     * Returns files/directories that exist in both the directories
     * @return array array containing list of common files and directories
     */
    public function getCommonContent(){
        $sourceContent = $this->getSourceContent();
        $destinationContent = $this->getDestinationContent();

        return array_intersect($sourceContent,$destinationContent);
    }

    /**
     * Returns files that exist in both the directories but contain different data
     * @return array array of "not same" files with same name
     */
    public function getCommonContentWithDiff(){
        $commonContent = $this->getCommonContent();

        //Finding differences
        $not_same = [];
        foreach ($commonContent as $key => $value) {

            $entity1 = $this->source.'/'.$value;
            $entity2 = $this->destination.'/'.$value;

            //Check if the entity is a directory
            if(is_dir($entity1) && is_dir($entity2)){

                if(!$this->recursiveMode){
                    continue;
                }

                //Recursion at rescue
                $diffon = new Diffon();
                $diffon->setSource($entity1)->setDestination($entity2)->setRecursiveMode(true);
                $diff = $diffon->diff();

                if(count($diff['only_in_source']) > 0 || count($diff['only_in_destination']) > 0 || count($diff['not_same']) > 0){
                    $not_same[$key] = $diff;
                }

                continue;
            }

            //Both entities are not directories, lets compare
            $same_content = $this->compare_files($entity1,$entity2);

            if(!$same_content){
                $not_same[$key] = $value;
            }
        }

        return $not_same;
    }


    /**
     * Compares two given files and determines if they contain the same  thing
     * @param  string $file1 path for the file to compare
     * @param  string $file2 path for the file to compare with
     * @return boolean       if the files contain same data
     */
    public function compare_files($file1,$file2){

        if(filesize($file1) !== filesize($file2)){
            return false;
        }

        $f_file1 = fopen($file1, 'rb');
        $f_file2 = fopen($file2, 'rb');

        while (!feof($f_file1)) {

            //Read files to maximum allowed length (8192) in bytes and compare
            if(fread($f_file1, 8192) != fread($f_file2, 8192))
            {
                return false;
            }
        }

        return true;
    }


    /**
     * Gives a list of files and directories in the source
     * Does not perform scandir twice if the list is already available
     * @return array contents of the source directory
     */
    public function getSourceContent(){

        //if(!isset($this->sourceContent)){
        $this->sourceContent = $this->listDir($this->source);
        //}

        return $this->sourceContent;
    }
    /**
     * Gives a list of files and directories in the destination
     * Does not perform scandir twice if the list is already available
     * @return array contents of the destination directory
     */
    public function getDestinationContent(){

        //if(!isset($this->destinationContent)){
        $this->destinationContent = $this->listDir($this->destination);
        //}

        return $this->destinationContent;
    }

    /**
     * Gives a list of files and directories in provided directory
     * @param  string $path path of the directory to list
     * @return array contents of the given directory
     */
    public function listDir($path){
        $contents = scandir($path);
        //Remove "." and ".." from the list and rearrange the indexes
        $contents = array_values(array_diff(scandir($path), array('.', '..')));

        return $contents;
    }
}
?>