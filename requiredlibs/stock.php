<?php

    require_once("stockModel.php");



    # Give Basic Data back
    function initDataHandshake($data)
    {
        global $GRADE_TITLE;
        
        $payload_received = json_decode($data["payload"],true);

        //print_r($payload_received);

        $payload = [];
        $payload["response"] = true;
        $payload['data'] = [];
        $payload['model_id'] = $payload_received["model_id"];

        $stockObj = new stockModel($payload_received["process"]);

        $basic_properties = $stockObj->processObj->getBasicProperties(); 
        $additional_properties = $stockObj->processObj->getAdditionalProperties(); 
        $additional_filters = $stockObj->processObj->getAdditionalFilter();

        $columnDefs = [];
        $uid_map = [];


        $dumProp = [];
        $dumProp["field"] = "process_id";
        $dumProp["headerName"] = "Process Id";
        $dumProp["filter"] = 'agTextColumnFilter';
        $dumProp["floatingFilter"] = true;
        $dumProp["pinned"] = "left";

        array_push($columnDefs,$dumProp);

        array_push($uid_map,["process_id","Process Id"]);
        array_push($uid_map,["date","Date"]);
        array_push($uid_map,["grade",$GRADE_TITLE]);
        
        $dumProp = [];
        $dumProp["field"] = "date";
        $dumProp["headerName"] = "Date";
        $dumProp["filter"] = 'agDateColumnFilter';
        $dumProp["floatingFilter"] = true;

        array_push($columnDefs,$dumProp);

        ## EXCEPTION FOR ANNEALING

        if($payload_received["process"] == "Annealing")
        {
            $dumProp = [];
            $dumProp["field"] = "raw_blend_id";
            $dumProp["headerName"] = "Raw Blend Id";
            $dumProp["filter"] = 'agTextColumnFilter';
            $dumProp["floatingFilter"] = true;
            $dumProp["sortable"] = true;

            array_push($columnDefs,$dumProp);
            array_push($uid_map,["raw_blend_id",'Raw Blend Id']);
        }

        ## END


        foreach ($additional_properties as  $prop) {

            $dumProp = [];
            $dumProp["field"] = $prop["uid"];
            $dumProp["headerName"] = $prop["Name"];
            if($prop["Filter"])
            {
                $dumProp["filter"] = $prop["FilterType"];
                $dumProp["floatingFilter"] = true;
            }

            array_push($uid_map,[$prop["uid"],$prop["Name"]]);

            


            array_push($columnDefs,$dumProp);
        }

        $count = 0;

        foreach ($payload_received["filter"]['test'] as  $prop) {
                $dumProp = [];

                $dumProp["field"] = "TEST_PROPERTY_ID_".$count++;
                $dumProp["headerName"] = $prop["property"];
                $dumProp["filter"] = 'agNumberColumnFilter';
                $dumProp["floatingFilter"] = true;

                 array_push($uid_map,[$dumProp["field"],$prop["property"]]);

                array_push($columnDefs,$dumProp);
        }

        $dumProp = [];
        $dumProp["field"] = "prod_qty";
        $dumProp["headerName"] = "Production Quantity (kg)";
        $dumProp["filter"] = 'agNumberColumnFilter';
        $dumProp["floatingFilter"] = true;
        array_push($uid_map,["prod_qty","Production Quantity (kg)"]);
        array_push($columnDefs,$dumProp);

        $dumProp = [];
        $dumProp["field"] = "bal_qty";
        $dumProp["headerName"] = "Balance Quantity (kg)";
        $dumProp["filter"] = 'agNumberColumnFilter';
        $dumProp["floatingFilter"] = true;

        array_push($uid_map,["prod_qty","Balance Quantity (kg)"]);
        array_push($columnDefs,$dumProp);


        $payload["uid_map"] = $uid_map;
        $payload["columnDef"] = $columnDefs;



        $result = getAllProcessIds($payload_received["process"],
                                                $payload_received["grades"],
                                                $payload_received["start_date"],
                                                $payload_received["end_date"],
                                                $basic_properties,$additional_properties,
                                                $payload_received["filter"],$uid_map,
                                                $payload_received["show_only_balance"]);


        $payload["rowData"] = $result[0];
        $payload["fetch_data_list"] = $result[1];
        $payload["test_data_list"] = $result[2];
        $payload["total_production_qty"] = $result[3];
        $payload["total_balance_qty"] = $result[4];
        $payload["process"] = $payload_received["process"];

        echo json_encode($payload);

    }


    function getAllProcessIds($process,$grades,$start_date,$end_date,$basic_properties,$additional_properties,
                              $filter,$uid_map,$show_only_balance)
    {
        global $GRADE_TITLE;
        $rowData = [];

        $fetch_data_list = [];
        $test_data_list = [];
        $total_production_qty = 0;
        $total_balance_qty = 0;

        $startdate = Date("Y-m-d 00:00:00",strtotime($start_date));
        $enddate = Date("Y-m-d 23:59:59",strtotime($end_date));

        if($basic_properties["filter_date_from_entry"])
        {

            
            $result = runQuery("SELECT processid,entrytime as date FROM processentry WHERE processname='$process' AND entrytime>='$startdate' AND entrytime <='$enddate' ORDER BY processentry.processid");


        }
        else
        {
            $dumparam = $basic_properties["filter_date_from_property"];
            
            $result = runQuery("SELECT processentry.processid,processentryparams.value as date FROM processentry 
                                LEFT JOIN processentryparams ON processentryparams.processid=processentry.processid 
                                WHERE processentry.processname='$process' AND processentryparams.param='$dumparam' 
                                AND processentryparams.value>='$startdate' AND processentryparams.value <='$enddate' 
                                ORDER BY processentry.processid");
        }


        $ProcessList = [];

        while($row = $result->fetch_assoc())
        {
            $dumData = [];
            $dumData["process_id"] = $row["processid"];
            $dumData["date"] = Date('d-M-Y',strtotime($row["date"]));

            
            $dum_prop_fetch1 = [];
            foreach ($additional_properties as  $prop) {
                $dumData[$prop["uid"]] = '▮▮▮▯▯';

                array_push($dum_prop_fetch1,[$prop["uid"],$prop["Name"]]);

            }

           

            $dum_prop_fetch2 = [];
            foreach ($filter["test"] as  $prop) {
                $dum = $prop["property"];
                $key1 = map_search($uid_map,$dum);
                    
                $dumData[$key1] = '▮▮▮▯▯';

                array_push($dum_prop_fetch2,[$key1,$dum]);
            }

            
            

            $dumId = $row["processid"];
            $graderesult = runQuery("SELECT * FROM processentryparams WHERE processid='$dumId' AND param='$GRADE_TITLE'");
            $dumData["grade"] = "";
            
            if($graderesult->num_rows!=0)
            {

                 $dumData["grade"]= $graderesult->fetch_assoc()["value"];


            }

            # Grade Filter

            if(count($grades))
            {
                if($process!='Melting')
                {
                    if(!in_array($dumData["grade"],$grades))
                    {
                        continue;
                    }
                }
            }


            # Filters
            foreach ($filter["additional"] as  $prop) {
                $dumProp = $prop[0];
                $dumSelected = $prop[1];

                $filterResult = runQuery("SELECT * FROM processentryparams WHERE processid='$dumId' AND param='$dumProp'");

                if($filterResult->num_rows!=0)
                {

                    $filterResult = $filterResult->fetch_assoc()['value'];

                    
                    if($dumSelected)
                    {
                        if(!in_array($filterResult,$dumSelected))
                        {
                            continue 2;
                        }
                    }
                }

            }

            

            array_push($ProcessList,$dumId);

            array_push($rowData,$dumData);


        }

        $prod_qty = [];
        $bal_qty = [];
        $raw_blend_id = []; ## EXCEPTION FOR ANNEALING


        global $MASS_TITLE;

        $qtyresult = runQuery("SELECT processid,param,value FROM processentryparams WHERE param='$MASS_TITLE' AND processid IN ('".implode("','",$ProcessList)."')");



        while ($row=$qtyresult->fetch_assoc()) {
        

            $prod_qty[$row["processid"]] =  $row["value"];
            $total_production_qty += $row["value"]; 

        }

        ## EXCEPTION FOR ANNEALING
        if($process=="Annealing")
        {
             $qtyresult = runQuery("SELECT processid,param,value FROM processentryparams WHERE step='Parent' AND processid IN ('".implode("','",$ProcessList)."')");
        


            
            while ($row=$qtyresult->fetch_assoc()) {
            

                $raw_blend_id[$row["processid"]] =  $row["param"];

            }
        }

        ## END


        $qtyresult = runQuery("SELECT  param, SUM(value) as val FROM processentryparams WHERE step='PARENT' AND param IN ('".implode("','",$ProcessList)."') GROUP BY param");




        while ($row=$qtyresult->fetch_assoc()) {
        

            $bal_qty[$row["param"]] =  $row["val"];

        }


            

        $newRowData = [];


        foreach ($rowData as $row) {

            if(isset($prod_qty[$row["process_id"]]))
            {
                $row["prod_qty"] = round($prod_qty[$row["process_id"]],2);
            }
            else
            {
                $row["prod_qty"] = "N/A";
            }


            if(isset($bal_qty[$row["process_id"]]) && isset($prod_qty[$row["process_id"]]))
            {
                $row["bal_qty"] = round($row["prod_qty"]-$bal_qty[$row["process_id"]],2);
                
                
            }
            else
            {
                $row["bal_qty"] = $row["prod_qty"];
                
            }



            ## EXCEPTION FOR ANNEALING
            if($process=="Annealing")
            {
                if(isset($raw_blend_id[$row["process_id"]]))
                {
                    $row["raw_blend_id"] = $raw_blend_id[$row["process_id"]];
                }
                else
                {
                    $row["raw_blend_id"] = "N/A";
                }

            }

            ## END
            
            

            if(is_numeric($row["bal_qty"]))
            {
                $total_balance_qty +=$row["bal_qty"];
            }
          

            if($show_only_balance)
            {
                if($row["bal_qty"]>0)
                {
                    array_push($newRowData,$row);
                    array_push($fetch_data_list,[$row["process_id"],$dum_prop_fetch1]);
                    if($dum_prop_fetch2)
                    {
                        array_push($test_data_list,[$row["process_id"],$dum_prop_fetch2]);
                    }
                    
                }
            }
            else
            {
                array_push($newRowData,$row);
                array_push($fetch_data_list,[$row["process_id"],$dum_prop_fetch1]);
                if($dum_prop_fetch2)
                {
                    array_push($test_data_list,[$row["process_id"],$dum_prop_fetch2]);
                }
            }
            

            
            
        }

        

        return [$newRowData,$fetch_data_list,$test_data_list,round($total_production_qty,2),round($total_balance_qty,2)];
    }


    function fetch_process_data($post_data)
    {

        $payload_received = json_decode($post_data["payload"],true);

        $payload = [];
        $payload["response"] = true;
        $payload['data'] = [];
        $payload['model_id'] = $payload_received["model_id"];
        $uid_map = $payload_received["uid_map"];

         $stockObj = new stockModel($payload_received["process"]);
         $additional_properties = $stockObj->processObj->getAdditionalProperties();


        $job = $payload_received["job"];


        $data = [];
        foreach ($job as  $value) {
           

           $dumId = $value[0];
           $params = $value[1];

           $data[$dumId] = [];

           $param_data = [];

           foreach ($params as $param) {
               
            array_push($param_data,$param[1]);
           }


            $dataResult = runQuery("SELECT * FROM processentryparams WHERE processid='$dumId' AND param IN ('".implode("','",$param_data)."')");

            while($row = $dataResult->fetch_assoc())
            {



                $dumUid = map_search($uid_map,$row['param']);
                $pFn = $stockObj->processObj->searchAdditionalProperties($dumUid)["ProcessFn"];


                //var_dump($pFn);
                array_push($data[$dumId],[$dumUid,$row['param'],$pFn($row['value'])]);
                
            }


        }

        
        $payload["data"] = $data;

       

        echo json_encode($payload);
    }


    function test_process_data($post_data)
    {

        $payload_received = json_decode($post_data["payload"],true);

        $payload = [];
        $payload["response"] = true;
        $payload['data'] = [];
        $payload['model_id'] = $payload_received["model_id"];
        $uid_map = $payload_received["uid_map"];


        $stockObj = new stockModel($payload_received["process"]);
         $additional_properties = $stockObj->processObj->getAdditionalProperties();


        $job = $payload_received["job"];


        $data = [];
        foreach ($job as  $value) {
           

           $dumId = $value[0];
           $params = $value[1];

           $data[$dumId] = [];

           $param_data = [];

           foreach ($params as $param) {
               
            array_push($param_data,$param[1]);
           }

           
          $dataResult = runQuery("SELECT param,AVG(value) as val FROM processtestparams WHERE processid = '$dumId' AND param IN ('".implode("','",$param_data)."') GROUP BY param");

            while($row = $dataResult->fetch_assoc())
            {

                $dumUid = map_search($uid_map,$row['param']);
               


                //var_dump($pFn);
                array_push($data[$dumId],[$dumUid,$row['param'],$row['val']]);
                
            }

            $all_found = [];

            foreach ($data[$dumId] as $param) {
                array_push($all_found,$param[0]);
            }



       }


       
       $payload["data"] = $data;

        echo json_encode($payload);

    }



    function get_process_properties($post_data)
    {
        $payload = [];
        $payload["response"] = true;
        $payload['data'] = [];

        if(!isset($post_data['process']) || !isset($post_data['grade']) )
        {
            $payload["response"] = false;
            $payload["message"] = "Data missing";
            unset($payload['data']);
            echo json_encode($payload);
            die();
        }

        $processname = $post_data['process'];


      
        

        $result = runQuery("SELECT * FROM gradeproperties WHERE processname='$processname'");

        $filteredlist = [];
        $property_param = [];

        while($row=$result->fetch_assoc())
        {
            if(in_array($row['gradename'],$post_data['grade']))
            {
                array_push($filteredlist,$row['properties']);
                $property_param[$row['properties']] = $row;
            }
            
        }

        $result = runQuery("SELECT * FROM processgradesproperties WHERE processname='$processname'");
        $gradedata = [];
        while($row=$result->fetch_assoc())
        {
            if(in_array($row['gradeparam'],$filteredlist))
            {
                $dumData =[];
                $dumData["type"] = $row["type"];
                $dumData["mpif"] = $row["mpif"];
                $dumData["class"] = $row["class"];
                $dumData["property"] = $row["gradeparam"];
                array_push($payload["data"],$dumData);
            }
            
        }


        $result = runQuery("SELECT * FROM sieve WHERE mesh order by cast(mesh as unsigned)");
        while($row=$result->fetch_assoc())
        {
            if(in_array($row['name'],$filteredlist))
            {
                $dumData =[];
                $dumData["type"] = "DECIMAL";
                $dumData["mpif"] = 5;
                $dumData["class"] = "Sieve";
                $dumData["property"] = $row["name"];
                array_push($payload["data"],$dumData);
            }
            
        }



        echo json_encode($payload);
    }



    function get_process_grade($post_data)
    {
        $payload = [];
        $payload["response"] = true;
        $payload['data'] = [];

        if(!isset($post_data['process']) )
        {
            $payload["response"] = false;
            $payload["message"] = "Data missing";
            unset($payload['data']);
            echo json_encode($payload);
            die();
        }

        $process = $post_data['process'];


        $result = runQuery("SELECT * FROM processgrades WHERE processname='$process'");

        while($row=$result->fetch_assoc())
        {  
            $dumData = [];
            $dumData["gradename"] = $row["gradename"];
            $dumData["cumulative"] = $row["cumulative"];
            array_push($payload['data'],$dumData);

        }

        $stockObj = new stockModel($process);
        $payload['basic_properties'] = $stockObj->processObj->getBasicProperties(); 
        $payload['additional_properties'] = $stockObj->processObj->getAdditionalProperties(); 
        $payload['additional_filters'] = $stockObj->processObj->getAdditionalFilter(); 

        echo json_encode($payload);
    }


    function map_search($map,$key)
    {
        foreach ($map as $value) {
            
            if($value[0]==$key)
            {
                return $value[1];
            }

            if($value[1]==$key)
            {
                return $value[0];
            }
        }

        return null;

    }


?>