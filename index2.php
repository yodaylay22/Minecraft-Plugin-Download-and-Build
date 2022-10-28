
<?php

    // Todos os plugins    

    $plugins = [];

    $plugins['Eco Skills']['link'] = "https://github.com/Auxilor/EcoSkills/archive/refs/heads/master.zip";
    $plugins['Eco Skills']['jars'] = "bin";

    $plugins['Eco Reforges']['link'] = "https://github.com/Auxilor/Reforges/archive/refs/heads/master.zip";
    $plugins['Eco Reforges']['jars'] = "bin";

    $plugins['Eco Talismans']['link'] = "https://github.com/Auxilor/Talismans/archive/refs/heads/master.zip";
    $plugins['Eco Talismans']['jars'] = "bin";

    $plugins['Eco Bosses']['link'] = "https://github.com/Auxilor/EcoBosses/archive/refs/heads/master.zip";
    $plugins['Eco Bosses']['jars'] = "bin";

    $plugins['Eco Weapons']['link'] = "https://github.com/Auxilor/EcoWeapons/archive/refs/heads/master.zip";
    $plugins['Eco Weapons']['jars'] = "bin";

    $plugins['Eco Armor']['link'] = "https://github.com/Auxilor/EcoArmor/archive/refs/heads/master.zip";
    $plugins['Eco Armor']['jars'] = "bin";

    $plugins['Eco Items']['link'] = "https://github.com/Auxilor/EcoItems/archive/refs/heads/master.zip";
    $plugins['Eco Items']['jars'] = "bin";

    $plugins['Eco Enchants']['link'] = "https://github.com/Auxilor/EcoEnchants/archive/refs/heads/master.zip";
    $plugins['Eco Enchants']['jars'] = "bin";

    $plugins['Eco Pets']['link'] = "https://github.com/Auxilor/EcoPets/archive/refs/heads/master.zip";
    $plugins['Eco Pets']['jars'] = "bin";

    $plugins['Eco Crates']['link'] = "https://github.com/Auxilor/EcoCrates/archive/refs/heads/master.zip";
    $plugins['Eco Crates']['jars'] = "bin";

    $plugins['Iris World Generator']['link'] = "https://github.com/VolmitSoftware/Iris/archive/refs/heads/master.zip";
    $plugins['Iris World Generator']['jars'] = "build/libs";

    $plugins['Oraxen']['link'] = "https://github.com/oraxen/oraxen/archive/refs/heads/master.zip";
    $plugins['Oraxen']['jars'] = "build/libs";


    set_time_limit(9600);

    foreach ($plugins as $key => $value) {

        set_error_handler(
            function ($severity, $message, $file, $line) {
                throw new ErrorException($message, $severity, $severity, $file, $line);
            }
        );

        try {
            // echo fix_dirname(__FILE__);
            // exit();
            $loc_jars = $value['jars'];
            $value = $value['link'];
            
            if(is_dir("./engine")){
                delete_dir("./engine");
            }
            mkdir("./engine");

            file_put_contents("./engine/master.zip", 
                file_get_contents($value)
            );
            
            unset($zip);
            unset($res);
            $zip = new ZipArchive;
            $res = $zip->open('./engine/master.zip');
            if ($res === TRUE) {
                $zip->extractTo("./engine");
                $zip->close();

                $scanned_directory = array_diff(scandir("./engine"), array('..', '.', 'master.zip'));
                $folder_name = array_values($scanned_directory)[0];

                chdir("./engine/".$folder_name);
                exec("set JAVA_HOME=C:\Program Files\Java\jdk-18.0.2");
                exec("set PATH=%JAVA_HOME%\bin");
                exec("gradlew shadowJar");
                
                if(count(glob("./engine/".$folder_name."/".$loc_jars."/*.*")) > 1){

                    mkdir("./".$key);
                    $src = "./engine/".$folder_name."/".$loc_jars;
                    $dst = "./".$key;
                    $files = glob($src."/*.*");
                    foreach($files as $file){
                        $file_to_go = str_replace($src,$dst,$file);
                        copy($file, $file_to_go);
                    }
                    echo '<p style="background-color: rgba(52, 211, 153, 1); font-size: 1.25rem; line-height: 1.75rem; font-weight: 700;color: rgba(17, 24, 39, 1); text-align: center;padding: 1.25rem; font-family: monospace;"> '.$key.' build success! </p>';
                    error_log(">> {$key} -> Finalizado com sucesso!");

                }else{

                    $src = "./engine/".$folder_name."/".$loc_jars;
                    $dst = "./";
                    $files = glob($src."/*.*");
                    foreach($files as $file){
                        $file_to_go = str_replace($src,$dst,$file);
                        copy($file, $file_to_go);
                    }
                    echo '<p style="background-color: rgba(52, 211, 153, 1); font-size: 1.25rem; line-height: 1.75rem; font-weight: 700;color: rgba(17, 24, 39, 1); text-align: center;padding: 1.25rem; font-family: monospace;"> '.$key.' build success! </p>';
                    error_log(">> {$key} -> build success!");
                }

            } else {
                echo '<p style="background-color: rgba(248, 113, 113, 1); font-size: 1.25rem; line-height: 1.75rem; font-weight: 700;color: rgba(17, 24, 39, 1); text-align: center;padding: 1.25rem; font-family: monospace;"> '.$key.' build fail! <small>Unzip error</small> </p>';
                error_log(">> {$key} -> build fail! Unzip error.");
            }

        } catch(Exception $e) {
            echo '<p style="background-color: rgba(248, 113, 113, 1); font-size: 1.25rem; line-height: 1.75rem; font-weight: 700;color: rgba(17, 24, 39, 1); text-align: center;padding: 1.25rem; font-family: monospace;"> '.$key.' build fail! <small>'.$e->getMessage().'</small></p>';
            error_log(">> {$key} -> build fail! Error:".$e->getMessage());
        }
    }

    if(is_dir(fix_dirname(__FILE__)."/engine")){
        delete_dir(fix_dirname(__FILE__)."/engine");
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

function fix_dirname($dir){
    $dir = dirname($dir);
    return str_replace("Yuri Eloi", "username", $dir);
    //return str_replace(" ", "\\ ", $dir);
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