<?php

App::uses('Component', 'Controller');

class LoggerComponent extends Component {


    protected $file;

    protected $content;

    protected $writeFlag;

    protected $endRow;

    public function setup($file,$endRow="\n",$writeFlag=FILE_APPEND) {
        $this->file=$file;

        $this->writeFlag=$writeFlag;

        $this->endRow=$endRow;
    }

    public function AddRow($content="",$newLines=1) {
        $newRow = "";
        for ($m=0;$m<$newLines;$m++)
        {
            $newRow .= $this->endRow;
        }
        $this->content .= $content . $newRow;
    }


    public function Commit() {
        $datetime = new DateTime(date('Y-m-d H:i:s'));
        $datetime->setTimezone(new DateTimeZone('America/Los_Angeles'));
        $content =  'Time :'. $datetime->format('Y-m-d H:i:s') ."\n";
        $content .= $this->content;
        return file_put_contents($this->file,$content,$this->writeFlag);
    }

    public function LogError($error,$newLines=1)
    {
        if ($error!=""){
            $this->AddRow($error,$newLines);
            echo $error;
        }
    }
}
