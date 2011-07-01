<?php
class OSMWriter
{
	private $str;
	private $nodeId = 10000;

	function __construct()
	{
		$this->str = '';
	}

	function xmlHeader()
	{
		$this->str .= "<?xml version='1.0' encoding='UTF-8'?>\n";
	}

	function openOsm( $version, $generator )
	{
		$this->str .= "<osm version='$version' generator='$generator'>\n";
	}

	function closeOsm()
	{
		$this->str .= "</osm>\n";
	}

	function openNode( $lat, $lon )
	{
		$this->nodeInfo = array( $lat, $lon );
		$this->tags = array();
	}

	function writeTag( $key, $value )
	{
		$this->tags[] = array( $key, $value );
	}

	function closeNode()
	{
		$this->str .= "\t<node id='{$this->nodeId}' lat='{$this->nodeInfo[0]}' lon='{$this->nodeInfo[1]}'";
		$this->nodeId++;
		if (count( $this->tags ) )
		{
			$this->str .= ">\n";
			foreach ( $this->tags as $tag )
			{
				$this->str .= "\t\t<tag k='{$tag[0]}' v='{$tag[1]}'/>\n";
			}
			$this->str .= "\t</node>\n";
		}
		else
		{
			$this->str .= "/>\n";
		}
	}

	function get()
	{
		return $this->str;
	}
}
