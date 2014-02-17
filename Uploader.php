<?php

/**
 * Simple Upload Class
 * 
 * Simple miltiple/sinlge file(s) uploader class
 * 
 * @package     Upload
 * @author      <fb.com/alash3al> or <alash3al@facebook.com>
 * @copyright   2014 Mohammed Alashaal
 * @version     1.0
 * @access      public
 * @license     MIT license
 * @filesource
 */
class Uploader
{
    /** @ignore */
    protected $errors       =   array
    (
        1   =>  'file_exceeds_limit',
        2   =>  'file_exceeds_form_limit',
        3   =>  'file_partial',
        4   =>  'no_file_selected',
        5   =>  'ext_not_allowed',
        6   =>  'no_tmp_dir',
        7   =>  'cannot_write_file',
        8   =>  'php_ext_stopped_upload',
        9   =>  'upload_dir_error',
        10  =>  'file_already_exists'
    );
    /** @ignore */
    protected $configs      =   array();
    /** @ignore */
    protected $file         =   null;
    /** @ignore */
    protected $result       =   null;
    
    /**
     * Constructor
     * 
     * @param array $config
     * @return object
     */
    public function __construct(array $config = array())
    {
        $configs = array
        (
            'form_key'          =>  'file'
            ,'upload_dir'       =>  session_save_path()
            ,'allowed_ext'      =>  array('png', 'jpg', 'jpeg', 'gif')
            ,'excluded_ext'     =>  array('htaccess', 'php', 'pl', 'py')
            ,'max_filesize'     =>  5000
            ,'override'         =>  false
        );
        
        $this->configs = array_merge($configs, $config);
    }
    
    /**
     * start uploading
     * 
     * @return bool
     */
    function do_upload()
    {
        $key = $this->configs['form_key'];
        
        if(is_array($_FILES[$key]['name']) and (empty($_FILES[$key]['name']) or empty($_FILES[$key]['name'][0]))) {
            $this->result = $this->errors[4];
            return false;
        }
        
        if(!isset($_FILES[$key])) {
            $this->result = $this->errors[4];
            return false;
        }
        
        if
        (
            !isset($_FILES[$key]['name'])       or 
            !isset($_FILES[$key]['error'])      or
            !isset($_FILES[$key]['size'])       or
            !isset($_FILES[$key]['type'])       or
            !isset($_FILES[$key]['tmp_name'])
        )
        {
            $this->result = $this->errors[4];
            return false;
        }
        
        if(!is_array($_FILES[$key]['name'])) 
        {
            $file = array($_FILES[$key]);
        } 
        elseif(is_array($_FILES[$key]['name'])) 
        {
            $c = count($_FILES[$key]['name']);
            $file = array();
            
            for($i=0; $i<$c; ++$i) 
            {
                $file[] = array
                (
                    'name'      =>  $_FILES[$key]['name'][$i],
                    'type'      =>  $_FILES[$key]['type'][$i],
                    'size'      =>  $_FILES[$key]['size'][$i],
                    'error'     =>  $_FILES[$key]['error'][$i],
                    'tmp_name'  =>  $_FILES[$key]['tmp_name'][$i]
                );
            }
        } 
        else 
        {
            $this->result = $this->errors[4];
            return false;
        }
        
        $r = 0;
        
        
        foreach($file as &$f)
        {
            $this->file = $f['name'];
            
            if($f['error'] > 0) 
            {
                $this->result[$f['name']] = $this->errors[(int)$f['error']];
                --$r;    
            }
            else
            {
                if(((int) $f['size']/1024) > $this->configs['max_filesize'])
                {
                    $this->result[$f['name']] = $this->errors[1];
                    --$r;
                }
                elseif(in_array($this->get_file_ext(), $this->configs['excluded_ext']))
                {
                    $this->result[$f['name']] = $this->errors[5];
                    --$r;
                }
                elseif(!in_array($this->get_file_ext(), $this->configs['allowed_ext']))
                {
                    $this->result[$f['name']] = $this->errors[5];
                    --$r;
                }
                elseif
                (
                    (
                        file_exists($this->new_filepath()) and
                        $this->configs['override'] == true 
                    ) or
                    !file_exists($this->new_filepath())
                )
                {
                    if(@move_uploaded_file($f['tmp_name'], $this->new_filepath()) == true)
                    {
                        $this->result[$f['name']] = true;
                        ++$r;
                    }
                    else
                    {
                        $this->result[$f['name']] = $this->errors[9];
                        --$r;
                    }
                }
                elseif(file_exists($this->new_filepath()) and $this->configs['override'] !== true)
                {
                    $this->result[$f['name']] = $this->errors[10];
                    --$r;
                } 
                else
                {
                    $this->result[$f['name']] = 'unknown_error';
                    --$r;
                }
            }
        }
        
        if($r > 0 and count($_FILES[$key]['name']) == $r) return true;
        elseif($r <= 0) return false;
    }
    
    /** @ignore */
    function result()
    {
        return (array) $this->result;
    }
    
    /** @ignore */
    protected function get_file_ext()
    {
        return pathinfo($this->file, PATHINFO_EXTENSION);
    }
    
    /** @ignore */
    protected function new_filepath()
    {
        $p = realpath($this->configs['upload_dir']);
        
        if(empty($p) or !$p) return false;
        
        return $p . DIRECTORY_SEPARATOR .  $this->filter_filename();
    }
}
