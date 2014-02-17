### PHP Uploader

Uploader is simple file(s) uploader class, that supports <br />
single or multiple files at once . <br />
You can configure it easily and it's full options . <br />

***
                
### Example 1: (single file upload)
                
<pre>

<form method="post" enctype="multipart/form-data">
    <input name="file" type="file" />
    <input type="submit" name="upload" value="upload" />
</form>
<?php
require 'Uploader.php';
if(isset($_POST['upload']))
{
    $config = array
    (
        'form_key'          =>  'file' // the name of input file form 
        ,'upload_dir'       =>  session_save_path() // path to save the file in , default "php tmp"
        ,'allowed_ext'      =>  array('png', 'jpg', 'jpeg', 'gif') // allowed extensions
        ,'excluded_ext'     =>  array('htaccess', 'php', 'pl', 'py')   // disallowed extensions
        ,'max_filesize'     =>  5000 // max file size default "5 mb or 5000 kb"
        ,'override'         =>  false // override existing ?
    );
    
    $u = new Uploader($config);
    if($u == true) echo 'done';
    else var_dump($u->result());
}
?>
</pre>


***

         
### Example 2: (multiple files upload)
                
<pre>
    <form method="post" enctype="multipart/form-data">
        <input name="file[]" type="file" />
        <input type="submit" name="upload" value="upload" />
    </form>
    
<?php
require 'Uploader.php';
if(isset($_POST['upload']))
{
    $config = array
    (
        'form_key'          =>  'file' // the name of input file form 
        ,'upload_dir'       =>  session_save_path() // path to save the file in , default "php tmp"
        ,'allowed_ext'      =>  array('png', 'jpg', 'jpeg', 'gif') // allowed extensions
        ,'excluded_ext'     =>  array('htaccess', 'php', 'pl', 'py')   // disallowed extensions
        ,'max_filesize'     =>  5000 // max file size default "5 mb"
        ,'override'         =>  false // override existing ?
    );
    
    $u = new Uploader($config);
    if($u == true) echo 'done';
    else var_dump($u->result());
}

?>
</pre>
