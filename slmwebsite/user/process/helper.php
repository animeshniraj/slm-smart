<?php
    
    function dateInput($parameter,$prop,$value,$disabled = '')
    {

        $prop = preg_replace('/\s+/', '_', $prop);
        
        echo "<div class=\"form-group row\">\n<label class=\"col-sm-2 col-form-label\">".$parameter."</label>\n<div class=\"col-sm-10\">\n\n<input type=\"hidden\" name=\"allparams[]\" value=\"".$parameter."\"><input type=\"text\" name=\"paramsvalue[]\" id=\"".$prop."\" ".$disabled." class=\"form-control\" value=\"".$value."\" placeholder=\"\">\n<script>\n$(function() {\n$('#".$prop."').daterangepicker({\nsingleDatePicker: true,\nshowDropdowns: true,\nminYear: 1901,\nmaxYear: parseInt(moment().format('YYYY'),10),\nlocale: {\n    format: 'DD-MM-YYYY'\n}\n\n    \n}, function(start, end, label) {\n\n});\n});\n</script>\n</div>\n</div>";

        if($value=="")
            {
                echo "<script>$(document).ready(function() {document.getElementById(\"".$prop."\").value=\"\"});</script>";
            }

    }




    function datetimeInput($parameter,$prop,$value,$disabled = '',$dmin='',$dmax='')
    {
        $prop = preg_replace('/\s+/', '_', $prop);
        if($disabled=="readonly")
        {
            echo "<div class=\"form-group row\">\n<label class=\"col-sm-2 col-form-label\">".$parameter."</label>\n<div class=\"col-sm-10\">\n<input type=\"hidden\" name=\"allparams[]\" value=\"".$parameter."\"><input type=\"text\" name=\"paramsvalue[]\" value=\"".$value."\" id=\"".$prop."\" ".$disabled." class=\"form-control\">\n</div>\n</div>";
        }
        else if($disabled=="readonly required" || $disabled=="required")
        {
            
                echo "<div class=\"form-group row\">\n<label class=\"col-sm-2 col-form-label\">".$parameter."</label>\n<div class=\"col-sm-10\">\n<input type=\"hidden\" name=\"allparams[]\" value=\"".$parameter."\"><input type=\"text\" name=\"paramsvalue[]\" value=\"".$value."\" id=\"".$prop."\" ".$disabled." class=\"form-control\">\n<script>\n$(function() {\n$('#".$prop."').daterangepicker({\nsingleDatePicker: true,timePicker: true,\nshowDropdowns: true,\nminYear: 1901,\nmaxYear: parseInt(moment().format('YYYY'),10),\nlocale: {\n    format: 'YYYY-MM-DD hh:mm A'\n}\n\n    \n}, function(start, end, label) {\n\n});\n});\n $('#".$prop."').val('".$value."');</script>\n</div>\n</div>";
            
            
        }
        
        else
        {

            echo "<div class=\"form-group row\">\n<label class=\"col-sm-2 col-form-label\">".$parameter."</label>\n<div class=\"col-sm-10\">\n\n<input type=\"hidden\" name=\"allparams[]\" value=\"".$parameter."\"><input type=\"text\" name=\"paramsvalue[]\" id=\"".$prop."\" ".$disabled." class=\"form-control\" placeholder=\"\">\n<script>\n$(function() {\n$('#".$prop."').daterangepicker({\nsingleDatePicker: true,timePicker: true,\nshowDropdowns: true,\nminYear: 1901,\nmaxYear: parseInt(moment().format('YYYY'),10),minDate: '".$dmin."',\nlocale: {\n    format: 'DD-MM-YYYY hh:mm A'\n}\n\n    \n}, function(start, end, label) {\n\n});\n});\n $('#".$prop."').val('".$value."');</script>\n</div>\n</div>";

            if($value=="")
            {
                echo "<script>$(document).ready(function() {document.getElementById(\"".$prop."\").value=\"\"});</script>";
            }

            


        }
        



    }


    function timeInput($parameter,$prop,$value,$disabled = '')
    {
        $prop = preg_replace('/\s+/', '_', $prop);
        echo "<div class=\"form-group row\">\n<label class=\"col-sm-2 col-form-label\">".$parameter."</label>\n<div class=\"col-sm-10\">\n<input type=\"hidden\" name=\"allparams[]\" value=\"".$parameter."\"><input type=\"time\" value=\"".$value."\" name=\"paramsvalue[]\" id=\"".$prop."\" ".$disabled." class=\"form-control\">\n</div>\n</div>";
    }


    function stringInput($parameter,$prop,$value,$disabled = '')
    {
        $prop = preg_replace('/\s+/', '_', $prop);


            echo "<div class=\"form-group row\">\n<label class=\"col-sm-2 col-form-label\">".$parameter."</label>\n<div class=\"col-sm-10\">\n<input type=\"hidden\" name=\"allparams[]\" value=\"".$parameter."\"><input type=\"text\" name=\"paramsvalue[]\" value=\"".$value."\" id=\"".$prop."\" ".$disabled." class=\"form-control\">\n</div>\n</div>";
        
        
    }

    function stringTestInput($parameter,$prop,$value,$disabled = '')
    {
        $prop = preg_replace('/\s+/', '_', $prop);

        echo "<div class=\"form-group row\">\n<div class=\"col-sm-12\">\n<input type=\"hidden\" name=\"allparams[]\" value=\"".$parameter."\"><input type=\"text\" name=\"paramsvalue[]\" value=\"".$value."\" id=\"".$prop."\" ".$disabled." class=\"form-control\">\n</div>\n</div>";
    }


    function integerInput($parameter,$prop,$value,$disabled = '')
    {
        $prop = preg_replace('/\s+/', '_', $prop);
        echo "<div class=\"form-group row\">\n<label class=\"col-sm-2 col-form-label\">".$parameter."</label>\n<div class=\"col-sm-10\">\n<input type=\"hidden\" name=\"allparams[]\" value=\"".$parameter."\"><input type=\"number\" step=1 value=\"".$value."\" name=\"paramsvalue[]\" id=\"".$prop."\" ".$disabled." class=\"form-control\">\n</div>\n</div>";
    }

    function integerTestInput($parameter,$prop,$value,$disabled = '',$min='',$max='',$quarantine)
    {
        $prop = preg_replace('/\s+/', '_', $prop);

        if(!$quarantine)
        {
            $min = -100000;
            $max = 100000;
            $quarantine =">100000";
        }
        echo "<div class=\"form-group row\"><div class=\"col-sm-12\">\n<input type=\"hidden\" name=\"allparams[]\" value=\"".$parameter."\"><input type=\"hidden\" name=\"quarantine[]\" value=\"".$quarantine."\"><input type=\"number\" step=1 value=\"".$value."\" name=\"paramsvalue[]\" id=\"".$prop."\" ".$disabled." class=\"form-control\">\n</div>\n</div>";
    }

 

    function decimalInput($parameter,$prop,$value,$disabled = '')
    {
        $prop = preg_replace('/\s+/', '_', $prop);
        echo "<div class=\"form-group row\">\n<label class=\"col-sm-2 col-form-label\">".$parameter."</label>\n<div class=\"col-sm-10\">\n<input type=\"hidden\" name=\"allparams[]\" value=\"".$parameter."\"><input type=\"number\" value=\"".$value."\" step=0.01 name=\"paramsvalue[]\" id=\"".$prop."\" ".$disabled." class=\"form-control\">\n</div>\n</div>";
    }


    function decimalTestInput($parameter,$prop,$value,$disabled = '',$min='',$max='',$quarantine)
    {
        $prop = preg_replace('/\s+/', '_', $prop);
        if(!$quarantine)
        {
            $min = -100000;
            $max = 100000;
            $quarantine =">100000";
        }
        echo "<div class=\"form-group row\">\n<div class=\"col-sm-12\">\n<input type=\"hidden\" name=\"allparams[]\" value=\"".$parameter."\"><input type=\"hidden\" name=\"quarantine[]\" value=\"".$quarantine."\"><input type=\"number\" value=\"".$value."\" step=0.001 name=\"paramsvalue[]\" id=\"".$prop."\" ".$disabled." class=\"form-control\">\n</div>\n</div>";
    }


    function optionInput($parameter,$prop,$currvalue,$options,$disabled = '')
    {
        global $GRADE_TITLE;
        $prop = preg_replace('/\s+/', '_', $prop);
        $dumval =  "<div class=\"form-group row\">\n<label class=\"col-sm-2 col-form-label\">".$parameter."</label>\n<div class=\"col-sm-10\">\n<input type=\"hidden\" name=\"allparams[]\" value=\"".$parameter."\"><select name=\"paramsvalue[]\" id=\"".$prop."\" ".$disabled." class=\"form-control\">";

        $options = explode(",",$options);

        foreach($options as $value)
        {
            if($value && $parameter==$GRADE_TITLE)
            {
                $dumval = $dumval ."<option value=\"".$value."\">".explode('#',$value)[0]."</option>";
            }
            elseif ($value ) {
                $dumval = $dumval ."<option value=\"".$value."\">".$value."</option>";
            }
            
        }

        $dumval = $dumval ."</select><script>changeSelect(document.getElementById('".$prop."'),'".$currvalue."')</script>\n</div>\n</div>";

        echo $dumval;
    }

	

    




?>