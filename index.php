
<?php

    // Todos os plugins    

    $plugins = array();

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
            
            $loc_jars = $value['jars'];
            $value = $value['link'];
            
            if(is_dir(dirname(__FILE__)."/engine")){
                delete_dir(dirname(__FILE__)."/engine");
            }
            mkdir(dirname(__FILE__)."/engine");

            file_put_contents(dirname(__FILE__)."/engine/master.zip", 
                file_get_contents($value)
            );
            
            unset($zip);
            unset($res);
            $zip = new ZipArchive;
            $res = $zip->open(dirname(__FILE__).'/engine/master.zip');
            if ($res === TRUE) {
                $zip->extractTo(dirname(__FILE__)."/engine");
                $zip->close();

                $scanned_directory = array_diff(scandir(dirname(__FILE__)."/engine"), array('..', '.', 'master.zip'));
                $folder_name = array_values($scanned_directory)[0];

                chdir(dirname(__FILE__)."/engine/".$folder_name);
                exec(".\gradlew shadowJar");
                
                if(count(glob(dirname(__FILE__)."/engine/".$folder_name."/".$loc_jars."/*.*")) > 1){

                    mkdir(dirname(__FILE__)."/".$key);
                    $src = dirname(__FILE__)."/engine/".$folder_name."/".$loc_jars;
                    $dst = dirname(__FILE__)."/".$key;
                    $files = glob($src."/*.*");
                    foreach($files as $file){
                        $file_to_go = str_replace($src,$dst,$file);
                        copy($file, $file_to_go);
                    }
                    echo '<p style="background-color: rgba(52, 211, 153, 1); font-size: 1.25rem; line-height: 1.75rem; font-weight: 700;color: rgba(17, 24, 39, 1); text-align: center;padding: 1.25rem; font-family: monospace;"> '.$key.' build success! </p>';

                }else{

                    $src = dirname(__FILE__)."/engine/".$folder_name."/".$loc_jars;
                    $dst = dirname(__FILE__);
                    $files = glob($src."/*.*");
                    foreach($files as $file){
                        $file_to_go = str_replace($src,$dst,$file);
                        copy($file, $file_to_go);
                    }
                    echo '<p style="background-color: rgba(52, 211, 153, 1); font-size: 1.25rem; line-height: 1.75rem; font-weight: 700;color: rgba(17, 24, 39, 1); text-align: center;padding: 1.25rem; font-family: monospace;"> '.$key.' build success! </p>';
                }

            } else {
                echo '<p style="background-color: rgba(248, 113, 113, 1); font-size: 1.25rem; line-height: 1.75rem; font-weight: 700;color: rgba(17, 24, 39, 1); text-align: center;padding: 1.25rem; font-family: monospace;"> '.$key.' build fail! <small>Unzip error</small> </p>';
            }

        } catch(Exception $e) {
            echo '<p style="background-color: rgba(248, 113, 113, 1); font-size: 1.25rem; line-height: 1.75rem; font-weight: 700;color: rgba(17, 24, 39, 1); text-align: center;padding: 1.25rem; font-family: monospace;"> '.$key.' build fail! <small>'.$e->getMessage().'</small></p>';
        }
    }

    if(is_dir(dirname(__FILE__)."/engine")){
        delete_dir(dirname(__FILE__)."/engine");
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