<?php
set_time_limit(9600);
date_default_timezone_set('America/Sao_Paulo');
$plugins = [];

// $plugins['Eco Skills']['link'] = "https://github.com/Auxilor/EcoSkills/archive/refs/heads/master.zip";
// $plugins['Eco Skills']['jars'] = "bin";

// $plugins['Eco Reforges']['link'] = "https://github.com/Auxilor/Reforges/archive/refs/heads/master.zip";
// $plugins['Eco Reforges']['jars'] = "bin";

// $plugins['Eco Talismans']['link'] = "https://github.com/Auxilor/Talismans/archive/refs/heads/master.zip";
// $plugins['Eco Talismans']['jars'] = "bin";

// $plugins['Eco Bosses']['link'] = "https://github.com/Auxilor/EcoBosses/archive/refs/heads/master.zip";
// $plugins['Eco Bosses']['jars'] = "bin";

// $plugins['Eco Weapons']['link'] = "https://github.com/Auxilor/EcoWeapons/archive/refs/heads/master.zip";
// $plugins['Eco Weapons']['jars'] = "bin";

// $plugins['Eco Armor']['link'] = "https://github.com/Auxilor/EcoArmor/archive/refs/heads/master.zip";
// $plugins['Eco Armor']['jars'] = "bin";

// $plugins['Eco Items']['link'] = "https://github.com/Auxilor/EcoItems/archive/refs/heads/master.zip";
// $plugins['Eco Items']['jars'] = "bin";

// $plugins['Eco Enchants']['link'] = "https://github.com/Auxilor/EcoEnchants/archive/refs/heads/master.zip";
// $plugins['Eco Enchants']['jars'] = "bin";

// $plugins['Eco Pets']['link'] = "https://github.com/Auxilor/EcoPets/archive/refs/heads/master.zip";
// $plugins['Eco Pets']['jars'] = "bin";

// $plugins['Eco Crates']['link'] = "https://github.com/Auxilor/EcoCrates/archive/refs/heads/master.zip";
// $plugins['Eco Crates']['jars'] = "bin";

// $plugins['Iris World Generator']['link'] = "https://github.com/VolmitSoftware/Iris/archive/refs/heads/master.zip";
// $plugins['Iris World Generator']['jars'] = "build/libs";

$plugins['Iris World Generator']['link'] = "https://github.com/mcMMO-Dev/mcMMO/archive/refs/heads/master.zip";
$plugins['Iris World Generator']['jars'] = "bin";


$folderName = date("d-m-Y H.i.s");
if(!is_dir("./".$folderName)) mkdir("./".$folderName);

foreach ($plugins as $key => $value) {
    if(is_dir("./engine")) delete_dir("./engine");
    mkdir("./engine");

    file_put_contents("./engine/master.zip", file_get_contents($value['link']));
    if(!is_writable("./engine/master.zip")){ echo "Arquivo de {$key} não encontrado!"; continue;}

    unzip_file("./engine/master.zip", "./engine");

    $return = makeBuild(getFolders("./engine"));
    exit();
    if($return){
        $finalFolder = "./".$folderName."/".$key;
        mkdir($finalFolder);
        $jarFolder = "./engine/".getFolders("./engine")."/".$value['jars'];
        moveAll($jarFolder, $finalFolder);

        feedback($key, true);
        error_log("\n\n>> {$key} -> build success!");
    }else{
        feedback($key, false);
        error_log("\n\n>> {$key} -> build error!");
    }

    if(is_dir("./engine")) delete_dir("./engine");
}


function unzip_file($file, $destination){
    // create object
    $zip = new ZipArchive() ;
    // open archive
    if ($zip->open($file) !== TRUE) {
        return false;
    }
    // extract contents to destination directory
    $zip->extractTo($destination);
    // close archive
    $zip->close();
        return true;
}

function getFolders($dir){
    return array_values(array_diff(scandir($dir), array('..', '.', 'master.zip')))[0];
}

function delete_dir($src) { 
    $dir = opendir($src);
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                delete_dir($src . '/' . $file); 
            } 
            else { 
                unlink($src . '/' . $file); 
            } 
        } 
    } 
    closedir($dir); 
    rmdir($src);
}

function makeBuild($folder_name){
    chdir("./engine/".$folder_name);
    exec("(((set JAVA_HOME=C:\Program Files\Java\jdk-18.0.2) & set PATH=C:\Program Files\Java\jdk-18.0.2\bin) & gradlew shadowJar) & exit", $output, $code);
    chdir("../../");
    foreach ($output as $value) {
        if(str_contains($value, "BUILD SUCCESSFUL")) return true;
    }
    echo "<pre>";
    print_r($output);
    echo "</pre>";
    return false;
}

function moveAll($from, $to){
    // Get array of all source files
    $files = scandir($from);
    // Identify directories
    $source = "{$from}/";
    $destination = "{$to}/";
    // Cycle through all source files
    foreach ($files as $file) {
    if (in_array($file, array(".",".."))) continue;
    // If we copied this successfully, mark it for deletion
    if (copy($source.$file, $destination.$file)) {
        $delete[] = $source.$file;
    }
    }
    // Delete all successfully-copied files
    foreach ($delete as $file) {
    unlink($file);
    }
}

function feedback($name, $type){
    if($type){
        echo '<p style="background-color: rgba(52, 211, 153, 1); font-size: 1.25rem; line-height: 1.75rem; font-weight: 700;color: rgba(17, 24, 39, 1); text-align: center;padding: 1.25rem; font-family: monospace;"> '.$name.' build success! </p>';
    }else{
        echo '<p style="background-color: rgba(248, 113, 113, 1); font-size: 1.25rem; line-height: 1.75rem; font-weight: 700;color: rgba(17, 24, 39, 1); text-align: center;padding: 1.25rem; font-family: monospace;"> '.$name.' build fail! <small>Erro na build</small></p>';
    }
}