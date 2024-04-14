<?php 
require 'init.php';
include "headerast.php";

/*
echo "add =".$_POST['add']."</br>"; 
echo $_POST['lin'] * $_POST['b'];
echo "a =".$a=$_POST['a'];
echo "b =".$b=$_POST['b'];
echo "c =".$c=$_POST['lin'] * $_POST['len']."</br>";
*/

/*
//echo "COUNT =".$_POST['sat']."</br>";
echo "tot1 =".$_GET['add_total']."</br>";
echo "tot2 =".$_GET['add_total2']."</br>";
//echo "test =".$_POST['test']."</br>";

//echo $x= $_GET['add_total'] ."</br>";
//echo $x=$x+$_GET['add_total']."</br>";
*/

$numadd = $_GET['a'];
//$numadd = '1';

$assetgroupid = $_GET['add_total'];
echo "AA-->".$assetgroupid ."<br>";
echo "get A=".$numadd;
?>
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr bgcolor="#FF0000">
			<th>Category</th> <th>Item</th> <th>Merk</th> <th>Type</th> 
		</tr>
             <?php
				/*
					$res = $db->get_results("SELECT distinct(asset_group_id),(group_name) FROM `category` ORDER BY asset_group_id ASC");
					if($res): 
					foreach($res as $row): 
					if($row->asset_group_id<0)
				   {
					$temp['id'] = $row->asset_group_id;
					$temp['name'] = $row->group_name;
					continue; }?> 
				*/
					//$res = $db->get_results("SELECT * FROM `category` WHERE `asset_group_id`='{$_GET['add_total']}'"); 
			?>
		<tr class="even" nowrap="nowrap">
			<td colspan="" >
              <!--  <input type="checkbox" name="asetgrup[]" value="<?=$row->asset_group_id?>" id="c<?=$row->asset_group_id?>" onchange="toogleAssetCtgor()" /><label for="c<?=$row->asset_group_id?>"><?=$row->group_name?></label>-->
				<?php 
				     //$res = $db->get_results("SELECT distinct(group_name) FROM `category` WHERE `asset_group_id`='1' ");//test
					 $res = $db->get_results("SELECT distinct(group_name) FROM `category` WHERE `asset_group_id`='{$_GET['add_total']}'");
					 foreach($res as $c): 
					 echo $c->group_name; 
					 
					 endforeach;
				?>
               <input name="asetgrup2[]" type="hidden" value="<?=$_GET['a']?>" /> 
			</td>            
            <td>
				<select name="item_a<?=$numadd?>" id="item">
					<option value="">-Asset Catagory-</option>
                        <?php 
						     //$resitem = $db->get_results("SELECT distinct(item1) FROM `category` WHERE `asset_group_id`='1'"); 
						    $resitem = $db->get_results("SELECT distinct(item1) FROM `category` WHERE `asset_group_id`='{$_GET['add_total']}'"); 
							foreach($resitem as $item): 
						?>   
						<option value="<?=$item->item1?>" <?=($item->item1==$_POST['item']?'selected="selected"':'')?>> <?=$item->item1?> </option>  
						
						<?php 
							endforeach;
						?>				
					<option value=0>Other</option>
				</select>
					<p style="display:none;" class="othcat_a<?=$numadd?>">Other: <input type="text" name="other_cat_a<?=$numadd?>"></p>
            </td>
            
            <td>
				<select name="merk_a<?=$numadd?>" id="merk2">
					<option value="">-Merk-</option>
						<?php  //$resmerk = $db->get_results("SELECT distinct(merk) FROM `category` WHERE `asset_group_id`='1'");  
							   $resmerk = $db->get_results("SELECT distinct(merk) FROM `category` WHERE `asset_group_id`='{$_GET['add_total']}'");
							   foreach($resmerk as $merk): 
						?>      
					<option value="<?=$merk->merk?>" <?=($merk->merk==$_POST['merk']?'selected="selected"':'')?>> <?=$merk->merk?> </option>  
						<?php endforeach;?>		
                        <option value=0>Other</option>				
					</select>
					<p style="display:none;" class="othmerk_a<?=$numadd?>">Other: <input type="text" name="other_merk_a<?=$numadd?>"></p>
            </td>
			<td>
				<select name="type_a<?=$numadd?>" id="type">
					<option value="">-type-</option>
						<?php //$restype = $db->get_results("SELECT distinct(type) FROM `category` WHERE `asset_group_id`='1'");
							  $restype = $db->get_results("SELECT distinct(type) FROM `category` WHERE `asset_group_id`='{$_GET['add_total']}'");
							   foreach($restype as $type): 
						?>      
					<option value="<?=$type->type?>" <?=($type->type==$_POST['merk']?'selected="selected"':'')?>> <?=$type->type?> </option>  
					<?php endforeach;?>
                    <option value=0>Other</option>				
				</select>
					<p style="display:none;" class="othtype_a<?=$numadd?>">Other: <input type="text" name="other_type_a<?=$numadd?>"></p>
            </td>
		</tr >
		<tr class="odd"> 
			<td>&nbsp;</td> <td colspan="3"></td> 
		</tr>
		<tr> 
			<td>Satuan</td> 
	     	<td>
                        <select name="sat_a<?=$numadd?>">
                        	<option value="">-Satuan-</option>
							<option value="Unit">Unit</option>
                            <option value="Blok">Blok</option>
                            <option value="Buah">Buah</option>
                            <option value="Set">Set</option>
                            <option value="Cm">Cm</option>
                            <option value="Meter">Meter</option>
                            <option value=0>Other</option>
                          </select>
			</td>
            <td style="display:none;" class="othsat_a<?=$numadd?>"> Other: <input class="narr2" type="text" name="other_sat_a<?=$numadd?>"></td>
            
            <td  style="display:none" class="hit2<?=$numadd?>">Tarikan</td> 
			<td>
				<table border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"> 
					<tr style="display:none" class="hit2<?=$numadd?>">
							<td width="20">Line:</td> 
							<td> </td>	
							<td width="20">lenght :</td>									
					</tr>
					<tr style="display:none" class="hit2<?=$numadd?>">	
							<td><input type="text" size="20" name="lin_a<?=$numadd?>"  class="narr"/> </td>  
							<td> x</td>	
							<td><input type="text" size="20" name="len_a<?=$numadd?>" class="narr"/> </td>		
					</tr>
				</table>

			</td>
		</tr>
		<tr> 
            <td>Quantity</td> 
			<td> <input name="quan_a<?=$numadd?>" type="text" value="<?php $c ?>" class="narr2"/> </td>
			<td colspan="2">&nbsp;</td> 
		</tr>
        
		<tr>
			<td>Note :</td> 
			<td colspan="3"> 
				<textarea name="note_a<?=$numadd?>" cols="40" rows=""></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="4">&nbsp;</td>
		</tr>
			  
			  
		<tr>
			<td colspan="4">
				<div></div>
			</td>
		</tr>
           <?php //endforeach; ?> <?php
							//endif;?>
    </table>
	
	<script language="javascript">
	$(function(){				
		$("select[name*=item_a]").change(function(){	
			var name = $(this).attr("name");
			var index;
			
			index = name.substr(6);
			
			
			var val = $(this, "option:selected").val();
			if(val == 0){
				$(".othcat_a"+index).fadeIn('slow');				
			} else {				
				$(".othcat_a"+index).hide();
			}
			return false; 
		});
		
		$("select[name*=item_a]").change(function(){			
			var val = $(this, "option:selected").val();
			var name = $(this).attr("name");
			var index;
			
			index = name.substr(6);
			
			//alert(val);
			if(val == "Kabel" || val == "Kabel Grounding" || val == "Kabel PCM" || val == "Kabel Power" || val == " Kabel power AC"
			   || val == "Kabel power DC" || val == "Kabel Power Hariff" || val == "Kabel Transmission" || val == "Kabel Jumper"){
				$(".hit2"+index).fadeIn('slow');				
			} else {				
				$(".hit2"+index).hide();
			}
			return false;
		});
		
		$("select[name*=merk_a]").change(function(){
			var name = $(this).attr("name");
			var index = name.substr(6);
			
			var val = $(this, "option:selected").val();
			if(val == 0){
				$(".othmerk_a"+index).fadeIn('slow');				
			} else {				
				$(".othmerk_a"+index).hide();
			}
			return false;
		});
		$("select[name*=type_a]").change(function(){			
			var name = $(this).attr("name");
			var index;
			
			index = name.substr(6);
				
			var val = $(this, "option:selected").val();
			if(val == 0){
				$(".othtype_a"+index).fadeIn('slow');				
			} else {				
				$(".othtype_a"+index).hide();
			}
			return false;
		});
		
		$("select[name*=sat_a]").change(function(){			
			var name = $(this).attr("name");
			var index = name.substr(5);
			
			var val = $(this, "option:selected").val();
			if(val == 0){
				$(".othsat_a"+index).fadeIn('slow');				
			} else {				
				$(".othsat_a"+index).hide();
			}
			return false;
		});
		
		$("input[name*=len_a]").blur(function(){
			
			/*
				var index = name.substr(-1);
				var nilai = parseFloat($(this).val());
				nilai = isNaN(nilai) ? 0 : nilai;
			*/
			var name = $(this).attr("name");
			var index;
			
			index = name.substr(5);
			
			var qty = parseFloat($(this).val());
			qty = $("input[name=lin_a"+index+"]").val() * $("input[name=len_a"+index+"]").val();
			
			$("input[name=quan_a"+index+"]").val(qty);
			
			
			
			
		});
		
		
	});
</script> 	

 

