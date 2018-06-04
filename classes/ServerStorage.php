<?php

class ServerStorage
{
    public $name;
    public $file;
    public $data;

    public function __construct()
    {
        $this->name = get_class($this);

        $this->file = __DIR__ .'/cache/'. $this->name;

        if (!file_exists($this->file)) {
            touch($this->file);
            $this->generate();
        }
        
        $this->data = require($this->file);
    }

    final public function generate($module='')
    {
        $reader = fopen($this->file, 'r');
        $cacheData = fread($reader, 1);
        fclose($reader);

        $content = "<?php\n\n";
        $content .= "\$cache = [];\n\n/* BEGIN CACHE FOR ". $this->name ." */\n\n/* END CACHE FOR ". $this->name ." */\n";
        $content .= "\nreturn \$cache;\n";

        $writter = fopen($this->file, 'w');
        fwrite($writter, $content);
        fclose($writter);
    }

    final public function get()
    {
        $args = func_get_args();

        if (count($args)) {
            $variable = '';

            foreach ($args as $n => $arg) {
                $variable .= '[$args[' . $n . ']]';
            }

            $code = '$result = isset($this->data'. $variable .') ? $this->data'. $variable ." : '';";
            eval($code);

            return $result;
        }

        return '';
    }

    final public function set()
    {
        $args = func_get_args();

        if (count($args)) {
            $result = $this->data;
            $variable = '';

            for ($i=0; $i<count($args)-1; $i++) {
                if ($args[$i] === null) {
                    return false;
                }

                $variable .= '[$args[' . $i . ']]';
            }

            if (is_array($args[count($args) - 1])) {
                $code = '$result'. $variable .' = $args[count($args) - 1];';  
            } else {
                $code = '$result'. $variable .' = "'. addslashes($args[count($args) - 1]) .'";';
            }

            eval($code);
            $this->data = $result;

            return true;
        }

        return null;
    }

    final public function drop()
    {
        $args = func_get_args();

        if (count($args)) {
            $result = $this->data;
            $variable = '';

            for ($i=0; $i<count($args); $i++) {
                if ($args[$i] === null) {
                    return false;
                }

                $variable .= '[$args[' . $i . ']]';
            }

            $code = 'if (isset($result'. $variable .')) unset($result'. $variable .');';
            eval($code);
            $this->data = $result;
        } else {
            $this->data = [];
        }

        return true;
    }

    final public function store()
    {
        if (file_exists($this->file)) {
            unlink($this->file);
            touch($this->file);
            $this->generate();
        }

        if (file_exists($this->file)) {
            $reader = fopen($this->file, 'r');
            $dataContent = fread($reader, filesize($this->file));
            fclose($reader);

            foreach ($this->data as $dataKey => $dataValue) {
                $dataString = '// BEGIN '. $this->name .' ['. $dataKey ."]\n";
                $tmpDataValue = $dataValue;
                
                if (is_array($tmpDataValue)) {
                    ksort($tmpDataValue);
                }
                
                $dataString .= $this->dataToString($tmpDataValue, "\$cache['{$dataKey}']");
                $dataString .= "// END ". $this->name .' ['. $dataKey .']';

                $dataContent = str_replace("/* END CACHE FOR {$this->name} */", "$dataString\n\n/* END CACHE FOR $this->name */", $dataContent);
            }

            $writter = fopen($this->file, 'w');
            fwrite($writter, $dataContent);
            fclose($writter);
        }
    }

    final public function dataToString($data=[], $prefix='')
    {
        if (is_array($data)) {
            $dataString = '';
            foreach ($data as $dataKey => $dataValue) {
                $tmpDataValue = $dataValue;
                
                if (is_array($tmpDataValue)) {
                    ksort($tmpDataValue);
                }
                
                $dataString .= $this->dataToString($tmpDataValue, $prefix .'['.(is_numeric($dataKey) ? $dataKey : "'{$dataKey}'").']');
            }
            return $dataString;
        } else {
            if (is_numeric($data)) {
                return $prefix .' = '. $data .';'."\n";
            } else {
                return $prefix .' = "'. addslashes($data) .'";'."\n";
            }
        }
    }
}
