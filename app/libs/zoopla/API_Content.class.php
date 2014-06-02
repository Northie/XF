<?php

namespace libs\zoopla;

class API_Content {
	public function __construct() {
		$z = \core\System_Settings::Load()->getSettings('zoopla');
		$this->uri = &$z['uri'];
		$this->api = &$z['api'];
	}
	
	public function Execute($loc) {
		
		$this->loc = htmlentities($loc);
		
		$zed_index = file_get_contents($this->uri."/zed_index.json?area=".urlencode($loc)."&output_type=outcode&api_key=".$this->api);
		$average_area_sold_price = file_get_contents($this->uri."/average_area_sold_price.json?area=".urlencode($loc)."&output_type=outcode&api_key=".$this->api);

		$zed_index = json_decode($zed_index,1);
		$average_area_sold_price = json_decode($average_area_sold_price,1);
		
		$this->zed_index = $z0 = $zed_index['zed_index'];
		$z['3 Months'] = $zed_index['zed_index_3month'];
		$z['6 Months'] = $zed_index['zed_index_6month'];
		$z['1 Year'] = $zed_index['zed_index_1year'];
		$z['2 Years'] = $zed_index['zed_index_2year'];
		$z['3 Years'] = $zed_index['zed_index_3year'];
		$z['4 Years'] = $zed_index['zed_index_4year'];
		$z['5 Years'] = $zed_index['zed_index_5year'];
		
		foreach($z as $key => $val) {

			$p = 1 - ($val / $z0);
			$p = (int) ($p * 100);
			$d = $z0 - $val;
			$h[$key] = array(
				'move'=>($d > 0 ? "+" : '-'),
				'move-class'=>($d > 0 ? "up" : 'down'),
				'value'=>abs($d),
				'percentage'=>abs($p),
				'orig'=>$val
			);
			
		}
		
		$this->zed_history = &$h;
		
		$hs = array(
			'3 Years'=>array(
				'properties'=>$average_area_sold_price['number_of_sales_3year'],
				'sold price'=>$average_area_sold_price['average_sold_price_3year']
			),
			'5 Years'=>array(
				'properties'=>$average_area_sold_price['number_of_sales_5year'],
				'sold price'=>$average_area_sold_price['average_sold_price_5year']
			),
			'7 Years'=>array(
				'properties'=>$average_area_sold_price['number_of_sales_7year'],
				'sold price'=>$average_area_sold_price['average_sold_price_7year']
			)
		);
		
		$this->avg_history = $hs;
	}
	
	public function getSilver() {
		$c = "
			<table>
				<tr>
					<th>".$this->loc." Zed-Index</th>
					<th>Value Change</th>
					<th>Average Price</th>
				</tr>
				<tr>
					<td>&pound;".number_format($this->zed_index)."</td>
					<td>
						".$this->zed_history['1 Year']['move-class']."
						".$this->zed_history['1 Year']['value']."
						(".$this->zed_history['1 Year']['move']." ".$this->zed_history['1 Year']['percentage']."%)<br />
						From 1 Year ago
					</td>
					<td>
						&pound;".number_format($this->avg_history['5 Years']['sold price'])." based on<br />
						".number_format($this->avg_history['5 Years']['properties'])." properties <br />
						over the last 5 years
					</td>
				</tr>
			</table>			
		";
		
		return $c;
	}
	
	public function getGold() {
		$c = "

		<table border='1' border-collapse='collapse'>
			<tr>
				<th>".$this->loc." Zed-Index</th>
				<th>Value Change</th>
				<th>Average Price</th>
			</tr>
		";

		for($i=1;$i<=3;$i++) {
			
			switch($i) {
				case 1:
					$zi = "&pound;".number_format($this->zed_index)."<br /><span>(".(date('Y')).")</span>";
					break;
				case 2:
					$zi = "&pound;".number_format($this->zed_history['2 Years']['orig'])."<br /><span>(".(date('Y') - 2).")</span>";
					break;
				case 3:
					$zi = "&pound;".number_format($this->zed_history['3 Years']['orig'])."<br /><span>".(date('Y') - 3).")</span>";
					break;
			}
			
			$c.="
				<tr>
					<td>".$zi."</td>
					<td>
						".$this->zed_history[$i.' Year'.($i > 1 ? 's' : '')]['move-class']."
						".$this->zed_history[$i.' Year'.($i > 1 ? 's' : '')]['value']."
						(".$this->zed_history[$i.' Year'.($i > 1 ? 's' : '')]['move']." ".$this->zed_history[$i.' Year'.($i > 1 ? 's' : '')]['percentage']."%)<br />
						From ".$i." Year".($i > 1 ? 's' : '')." ago
					</td>
					<td>
						&pound;".number_format($this->avg_history[(($i*2)+1).' Years']['sold price'])." based on 
						".number_format($this->avg_history[(($i*2)+1).' Years']['properties'])." properties <br />
						sold over the last ".(($i*2)+1)." years
					</td>
				</tr>
			";
		}
		$c.="</table>";
		
		return $c;
	}
	
	public function getGoldNew() {
		$c = "
			<div style='color:gray; font-size:16px; font-family:sans-serif;'>
				<table width='700px' border='0'>
					<tr>
						<td width='40px'>&nbsp;</td>
						<td>
							<table width='616px' border='0' bgcolor='#fff' style='border:2px solid #5D2162;'>
								<tr>
									<td width='20px'></td>
									<td colspan='3'>
										<h3 style='font-size:32px; font-weight:normal; margin:12px 0'>Property Trends</h3>
									</td>
									<td width='20px'></td>
								</tr>							
								<tr>
									<td width='20px'></td>
									<td width='280px' valign='top'>
										<p style='margin:0px;'>".$this->loc." Zed Index</p>
										<p style='color:silver; font-size:28px; margin-top:0px;'>&pound;".number_format($this->zed_index)."</p>
										<p style='margin-bottom:0px;'>Average asking price in ".$this->loc.":</p>
										<p style='color:silver; font-size:28px; margin-top:0px;'>&pound;".number_format($this->avg_history['5 Years']['sold price'])."</p>
										<p style='margin-bottom:0px;'>Number of properties sold in ".$this->loc.":</p>
										<p style='color:silver; font-size:28px; margin-top:0px;'>".number_format($this->avg_history['5 Years']['properties'])."</p>
										<!--img src='/img/poweredby.gif' /-->
										<a href='http://www.zoopla.co.uk/'><img src='http://www.zoopla.co.uk/static/images/mashery/powered-by-zoopla.png' width='111' height='54' title='Property information powered by Zoopla' alt='Property information powered by Zoopla' border='0'></a>
									</td>
									<td width='20px'></td>
									<td width='280px' valign='top'>
										<table border='0' width='300px'>
											<tr>
												<td colspan='4'>".$this->loc." Value Change</td>
											</tr>
											<tr>
												<td style='line-height:3em;'>3 Months</td>
												<td><img src='/img/".$this->zed_history['3 Months']['move-class'].".gif' /></td>
												<td style='font-size:24px; color:silver;'>&pound;".number_format($this->zed_history['3 Months']['value'])."</td>
												<td>(".$this->zed_history['3 Months']['move'].$this->zed_history['3 Months']['percentage']."%)</td>
											</tr>
											<tr>
												<td style='line-height:3em;'>6 Months</td>
												<td><img src='http://".$_SERVER['SERVER_NAME']."/img/".$this->zed_history['6 Months']['move-class'].".gif' /></td>
												<td style='font-size:24px; color:silver;'>&pound;".number_format($this->zed_history['6 Months']['value'])."</td>
												<td>(".$this->zed_history['6 Months']['move'].$this->zed_history['6 Months']['percentage']."%)</td>
											</tr>
											<tr>
												<td style='line-height:3em;'>1 Year</td>
												<td><img src='http://".$_SERVER['SERVER_NAME']."/img/".$this->zed_history['1 Year']['move-class'].".gif' /></td>
												<td style='font-size:24px; color:silver;'>&pound;".number_format($this->zed_history['1 Year']['value'])."</td>
												<td>(".$this->zed_history['1 Year']['move'].$this->zed_history['1 Year']['percentage']."%)</td>
											</tr>
											<tr>
												<td style='line-height:3em;'>3 Years</td>
												<td><img src='http://".$_SERVER['SERVER_NAME']."/img/".$this->zed_history['3 Years']['move-class'].".gif' /></td>
												<td style='font-size:24px; color:silver;'>&pound;".number_format($this->zed_history['3 Years']['value'])."</td>
												<td>(".$this->zed_history['3 Years']['move'].$this->zed_history['3 Years']['percentage']."%)</td>
											</tr>
										</table>
									</td>
									<td width='20px'></td>
								</tr>
							</table>
						</td>
						<td width='40px'>&nbsp;</td>
					</tr>
				</table>
			</div>
		";
												
		return $c;
	}
	
	public function getSilverNew() {
		$c = "
			<div style='color:gray; font-size:16px; font-family:sans-serif;'>
				<table width='700px' border='0'>
					<tr>
						<td width='40px'>&nbsp;</td>
						<td>
							<table width='616px' border='0' bgcolor='#fff' style='border:2px solid #5D2162;'>
								<tr>
									<td width='20px'></td>
									<td colspan='6'>
										<h3 style='font-size:32px; font-weight:normal; margin:12px 0'>Property Trends</h3>
									</td>
								</tr>
								<tr>
									<td width='20px'></td>
									<td width='178px'><p style='margin:0px;'>".$this->loc." Zed Index</p></td>
									<td width='20px'></td>
									<td width='178px'><p style='margin-bottom:0px;'>Average asking price in ".$this->loc.":</p></td>
									<td width='20px'></td>
									<td width='178px'><p style='margin-bottom:0px;'>Number of properties sold in ".$this->loc.":</p></td>
									<td width='20px'></td>
								</tr>
								<tr>
									<td></td>
									<td><p style='color:silver; font-size:28px; margin-top:0px;'>&pound;".number_format($this->zed_index)."</p></td>
									<td></td>
									<td><p style='color:silver; font-size:28px; margin-top:0px;'>&pound;".number_format($this->avg_history['5 Years']['sold price'])."</p></td>
									<td></td>
									<td><p style='color:silver; font-size:28px; margin-top:0px;'>".number_format($this->avg_history['5 Years']['properties'])."</p></td>
									<td></td>
								</tr>
								<tr>
									<td width='20px'></td>
									<td colspan='6'>
										<a href='http://www.zoopla.co.uk/'><img src='http://www.zoopla.co.uk/static/images/mashery/powered-by-zoopla.png' width='111' height='54' title='Property information powered by Zoopla' alt='Property information powered by Zoopla' border='0'></a>
									</td>								
								</tr>
							</table>
						</td>
						<td width='40px'>&nbsp;</td>
					</tr>
				</table>
			</div>
		";
												
		return $c;
	}
	
}