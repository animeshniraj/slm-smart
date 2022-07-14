<?php
	

	class stockModel
	{
		public $process;
		public $processObj;

		function __construct($process)
		{
			$this->$process = $process;

			switch (strtolower($process)) {
				case 'melting'			:$this->processObj= new MeltingStock() ;break;
				case 'raw bag'			:$this->processObj= new RawBagStock() ;break;
				case 'raw blend'		:$this->processObj= new RawBlendStock() ;break;
				case 'annealing'		:$this->processObj= new AnnealingStock() ;break;
				case 'semi finished'	:$this->processObj= new SemiFinishedStock() ;break;
				case 'final blend'		:$this->processObj= new FinalBlendStock() ;break;

				
				default: throw new Exception("Cannot find process"); break;
			}
		}
	}


	class MeltingStock
	{

		function getBasicProperties()
		{
			$properties = [];

			$properties["filter_date_from_entry"] = false;
			$properties["filter_date_from_property"] = "Heat On Time";


			return $properties;
		}

		function searchAdditionalProperties($uid)
		{
			$props = $this->getAdditionalProperties();

			foreach ($props as $prop) {
				if($prop["uid"] == $uid)
				{
					return $prop;
				}
			}
		}

		function getAdditionalProperties()
		{
			$properties = [];

			$dumParam = [
							'Name'=> 'Heat No.',
							'uid'=> 'heat_no',
							'Show'=> true,
							'Filter'=> true,
							'FilterType'=> 'agNumberColumnFilter',
							'ProcessFn'=> function($data)
											{return $data;}
					   ];
			array_push($properties,$dumParam);

			$dumParam = [
							'Name'=> 'Furnace Heat No.',
							'uid'=> 'furnace_heat_no',
							'Show'=> true,
							'Filter'=> true,
							'FilterType'=> 'agNumberColumnFilter',
							'ProcessFn'=> function($data)
											{return $data;}
			];
			array_push($properties,$dumParam);
			

			$dumParam = [
							'Name'=> 'Heat On Time',
							'uid'=> 'heat_on_time',
							'Show'=> true,
							'Filter'=> true,
							'FilterType'=> 'agDateColumnFilter',
							'ProcessFn'=> function ($data) {return Date('h:m A',strtotime($data));}
					   ];
			array_push($properties,$dumParam);

			$dumParam = [
							'Name'=> 'Heat Off Time',
							'uid'=> 'heat_off_time',
							'Show'=> true,
							'Filter'=> true,
							'FilterType'=> 'agDateColumnFilter',
							'ProcessFn'=> function($data) {return Date('h:m A',strtotime($data));}
					   ];
			array_push($properties,$dumParam);


			return $properties;
		}
		
		function getAdditionalFilter()
		{
			$properties = [];


			$result = runQuery("SELECT * FROM furnaces WHERE processname='Melting'");
			$dumoptions = [];
			while($row=$result->fetch_assoc())
			{
				array_push($dumoptions,$row['furnacename']);
			}


			$dumprop = [
							'Name'=> 'Furnace',
							'Type'=> 'option',
							'Options' => $dumoptions,
							'Selected' => [],	
					   ];
			$properties['Furnace']=$dumprop;
			


			return $properties;
		}

	}

	class RawBagStock
	{

		function getBasicProperties()
		{
			$properties = [];

			$properties["filter_date_from_entry"] = true;
			$properties["filter_date_from_property"] = "";


			return $properties;
		}

		function searchAdditionalProperties($uid)
		{
			$props = $this->getAdditionalProperties();

			foreach ($props as $prop) {
				if($prop["uid"] == $uid)
				{
					return $prop;
				}
			}
		}


		function getAdditionalProperties()
		{
			$properties = [];

			global $GRADE_TITLE;


			$dumParam = [
							'Name'=> 'Raw Bag No.',
							'uid'=> 'raw_bag_no',
							'Show'=> true,
							'Filter'=> true,
							'FilterType'=> 'agTextColumnFilter',
							'ProcessFn'=> function($data)
											{return $data;}
					   ];
			array_push($properties,$dumParam);


			$dumParam = [
							'Name'=> $GRADE_TITLE,
							'uid'=> 'grade',
							'Show'=> true,
							'Filter'=> true,
							'FilterType'=> 'agTextColumnFilter',
							'ProcessFn'=> function($data)
											{return $data;}
					   ];
			array_push($properties,$dumParam);

			return $properties;
		}

		function getAdditionalFilter()
		{
			$properties = [];

			return $properties;
		}
	}

	class RawBlendStock
	{
		function getBasicProperties()
		{
			$properties = [];

			$properties["filter_date_from_entry"] = true;
			$properties["filter_date_from_property"] = "";


			return $properties;
		}

		function searchAdditionalProperties($uid)
		{
			$props = $this->getAdditionalProperties();

			foreach ($props as $prop) {
				if($prop["uid"] == $uid)
				{
					return $prop;
				}
			}
		}


		function getAdditionalProperties()
		{
			$properties = [];

			global $GRADE_TITLE;


			$dumParam = [
							'Name'=> 'Blend Number',
							'uid'=> 'blend_number',
							'Show'=> true,
							'Filter'=> true,
							'FilterType'=> 'agTextColumnFilter',
							'ProcessFn'=> function($data)
											{return $data;}
					   ];
			array_push($properties,$dumParam);


			$dumParam = [
							'Name'=> $GRADE_TITLE,
							'uid'=> 'grade',
							'Show'=> true,
							'Filter'=> true,
							'FilterType'=> 'agTextColumnFilter',
							'ProcessFn'=> function($data)
											{return $data;}
					   ];
			array_push($properties,$dumParam);

			return $properties;

		}
		
		function getAdditionalFilter()
		{
			$properties = [];

			return $properties;
		}
	}


	class AnnealingStock
	{
		function getBasicProperties()
		{
			$properties = [];

			$properties["filter_date_from_entry"] = true;
			$properties["filter_date_from_property"] = "";


			return $properties;
		}


		function searchAdditionalProperties($uid)
		{
			$props = $this->getAdditionalProperties();

			foreach ($props as $prop) {
				if($prop["uid"] == $uid)
				{
					return $prop;
				}
			}
		}


		function getAdditionalProperties()
		{
			$properties = [];


			global $GRADE_TITLE;


			$dumParam = [
							'Name'=> 'Raw Blend Id',
							'uid'=> 'blend_number',
							'Show'=> true,
							'Filter'=> true,
							'FilterType'=> 'agTextColumnFilter',
							'ProcessFn'=> function($data)
											{return $data;}
					   ];
			//array_push($properties,$dumParam);


			$dumParam = [
							'Name'=> $GRADE_TITLE,
							'uid'=> 'grade',
							'Show'=> true,
							'Filter'=> true,
							'FilterType'=> 'agTextColumnFilter',
							'ProcessFn'=> function($data)
											{return $data;}
					   ];
			array_push($properties,$dumParam);

			return $properties;

		}
		
		
		function getAdditionalFilter()
		{
			$properties = [];

			$result = runQuery("SELECT * FROM furnaces WHERE processname='Annealing'");
			$dumoptions = [];
			while($row=$result->fetch_assoc())
			{
				array_push($dumoptions,$row['furnacename']);
			}


			$dumprop = [
							'Name'=> 'Furnace',
							'Type'=> 'option',
							'Options' => $dumoptions,
							'Selected' => [],	
					   ];
			$properties['Furnace']=$dumprop;


			$result = runQuery("SELECT * FROM processgrades WHERE processname='Final Blend'");
			$dumoptions = [];
			while($row=$result->fetch_assoc())
			{
				array_push($dumoptions,$row['gradename']);
			}


			$dumprop = [
							'Name'=> 'Final Blend Grade',
							'Type'=> 'option',
							'Options' => $dumoptions,
							'Selected' => [],	
					   ];
			$properties['Final Blend Grade']=$dumprop;

			return $properties;
		}
	}

	class SemiFinishedStock
	{
		function getBasicProperties()
		{
			$properties = [];

			$properties["filter_date_from_entry"] = true;
			$properties["filter_date_from_property"] = "";


			return $properties;
		}


		function searchAdditionalProperties($uid)
		{
			$props = $this->getAdditionalProperties();

			foreach ($props as $prop) {
				if($prop["uid"] == $uid)
				{
					return $prop;
				}
			}
		}


		function getAdditionalProperties()
		{
			$properties = [];

			global $GRADE_TITLE;


			$dumParam = [
							'Name'=> 'Bin Number',
							'uid'=> 'bin_number',
							'Show'=> true,
							'Filter'=> true,
							'FilterType'=> 'agTextColumnFilter',
							'ProcessFn'=> function($data)
											{return $data;}
					   ];
			array_push($properties,$dumParam);


			$dumParam = [
							'Name'=> $GRADE_TITLE,
							'uid'=> 'grade',
							'Show'=> true,
							'Filter'=> true,
							'FilterType'=> 'agTextColumnFilter',
							'ProcessFn'=> function($data)
											{return $data;}
					   ];
			array_push($properties,$dumParam);

			return $properties;

		}
		
		
		function getAdditionalFilter()
		{
			$properties = [];

			return $properties;
		}
		
	}

	class FinalBlendStock
	{
		function getBasicProperties()
		{
			$properties = [];

			$properties["filter_date_from_entry"] = true;
			$properties["filter_date_from_property"] = "";


			return $properties;
		}

		function searchAdditionalProperties($uid)
		{
			$props = $this->getAdditionalProperties();

			foreach ($props as $prop) {
				if($prop["uid"] == $uid)
				{
					return $prop;
				}
			}
		}


		function getAdditionalProperties()
		{
			$properties = [];


			global $GRADE_TITLE;



			$dumParam = [
							'Name'=> $GRADE_TITLE,
							'uid'=> 'grade',
							'Show'=> true,
							'Filter'=> true,
							'FilterType'=> 'agTextColumnFilter',
							'ProcessFn'=> function($data)
											{return $data;}
					   ];
			array_push($properties,$dumParam);

			return $properties;

		}
		
		function getAdditionalFilter()
		{
			$properties = [];

			return $properties;
		}

	}

?>