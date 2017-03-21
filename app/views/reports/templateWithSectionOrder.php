<?php include 'tablenav.php';?>
<?php 
$increment = 0; 
$condition = 'ht' ;
$elementCount = count($data['thead']);
$tdCount = $elementCount - 4;
if(!empty($data['report']) && $data['report'] != null && $data['report'] != false && count($data['report']) != 0)
{
    echo "<thead class='thead_position'><tr>";
    for($j = 0; $j < $elementCount; $j++)
    {
        echo "<th>" . $data['thead'][$j] . "</th>";
    }
    echo "</tr></thead><tbody>";
	for ($i = 0; $i < count($data['report']); $i++) 
    {
        $onhandClass = "positive"; 
    	if($increment == 0 || $condition != $data['report'][$i]['SctNo'])
    	{
			echo '<tr class = "section_name"><td></td><td></td><td></td>';
            echo '<td class="SectionName">SECTION '.$data['report'][$i]['SctNo'].' - '.$data['report'][$i]['SctName'].'</td>';
            for($k = 0; $k < $tdCount; $k++)
            {
                echo '<td></td>';
            }
            echo '</tr>';
		}
    	if(floor($data['report'][$i]["onhand"] < 0))
    	{
    		$onhandClass = "negative";
    	}
        $data['report'][$i]["onhand"] = floor($data['report'][$i]["onhand"]);
        $data['report'][$i]["lastReceiving"] = abs($data['report'][$i]["lastReceiving"]);
        $data['report'][$i]["sales"] = abs(floor($data['report'][$i]["sales"]));
        if(!empty($data['report'][$i]["unitPrice"]))
        {
            $data['report'][$i]["unitPrice"] = round($data['report'][$i]["unitPrice"]);
        }
        $data['report'][$i]["CaseCost"] = round($data['report'][$i]["CaseCost"]);
        echo "<tr>";
        for($l=0; $l < count($data['qt']); $l++)
        {
            if($data["qt"][$l] == "Retail")
            {
                echo "<td class='" . $data["qt"][$l] . "'>$" . $data['report'][$i][$data["qt"][$l]] . "</td>";
            }
            else
            {
                if($data["qt"][$l] == "onhand")
                {
                    echo "<td class='" . $data["qt"][$l] . " " . $onhandClass . "'>" . $data['report'][$i][$data["qt"][$l]] . "</td>";
                }
                else
                {
                    if(($data["qt"][$l] == "lastReceiving" && empty($data['report'][$i]["lastReceivingDate"])) 
                        || ($data["qt"][$l] == "tpr" && $data['report'][$i]["tpr"] == ".00")
                        || ($data["qt"][$l] == "tprStart" && $data['report'][$i]["tpr"] == ".00")
                        || ($data["qt"][$l] == "tprEnd" && $data['report'][$i]["tpr"] == ".00"))
                    {
                        echo "<td class='" . $data["qt"][$l] . "'></td>";
                    }
                    else
                    {
                        if($data["qt"][$l] == "UPC")
                        {
                            echo "<td class='" . $data["qt"][$l] . " " . $onhandClass . "'>
                            <a href = '/expiration/public/home/UPCPriceCompare_url/" . $data['report'][$i][$data["qt"][$l]] . "'>" . $data['report'][$i][$data["qt"][$l]] . "
                            </a></td>";
                        }
                        else
                        {
                            if($data["qt"][$l] == "CertCode")
                            {
                                echo "<td class='" . $data["qt"][$l] . "'>
                                <a href = '/expiration/public/home/vendorItemCode_url/" . str_replace(' ', '', $data['report'][$i][$data["qt"][$l]]) . "'>" . str_replace(' ', '', $data['report'][$i][$data["qt"][$l]]) . "</a></td>";
                            }
                            else
                            {
                                echo "<td class='" . $data["qt"][$l] . "'>" . $data['report'][$i][$data["qt"][$l]] . "</td>";
                            }
                        }
                    }
                }
            }

        }
		
    	$increment = $increment + 1 ;
    	$condition = $data['report'][$i]['SctNo'];
    }
}
else
{
    echo "<a href='/expiration/public/home/'><p class='text-warning errortext'>THE REPORT DID NOT GENERATE ANY RESULTS. THIS HAPPENS WHEN 0 ITEMS ARE RELATED TO YOUR SEARCH. IF IT IS NOT THE CASE, PLEASE CHECK THE UPC NUMBER. DID YOU ENTER THE RIGHT SALES DATES ?</p></a>";
}
?>
</tbody>
</table>
</div>
</div>
</div>