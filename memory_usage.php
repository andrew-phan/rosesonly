<?php
if( !function_exists('memory_get_usage') ){
    include('function.php');
}
echo "At the start we're using (in bytes): ",
memory_get_usage() , "\n\n";
?>