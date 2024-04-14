<?php 
require 'init.php';
include "headerast.php";
/*
echo $_POST['lin'] * $_POST['b'];
echo "a =".$a=$_GET['lin'.$row->asset_group_id];
echo "b =".$b=$_GET['len'];
echo "c =".$c=$_POST['lin'] * $_POST['len'];
*/
//echo "assetgrup =".$_POST['asetgrup[]'];
?>

<table id="fast" cellpadding="5" cellspacing="1" border="0" width="">
    <tr>  
	    <td colspan="1" align="left"><b>Rincian Kerusakan</b></td>
    </tr>
	<tr>
        <td>
			<table border="0" cellpadding="" cellspacing="">
				<tr bgcolor="#FF0000">
					<th>Category</th> <th>Item</th> <th>Merk</th> <th>Type</th> 
				</tr>
					<?php
						$res = $db->get_results("SELECT distinct(asset_group_id),(group_name) FROM `category` ORDER BY asset_group_id ASC");
						if($res): 
						foreach($res as $row): 
						if($row->asset_group_id<0)
						{
							$temp['id'] = $row->asset_group_id;
							$temp['name'] = $row->group_name;
							continue; 
						}
					?>
				<tr class="even" nowrap="nowrap">
					<td colspan="" >
						<input type="checkbox" name="asetgrup[]" value="<?=$row->asset_group_id?>" id="add_total" /><label for="c<?=$row->asset_group_id?>"><?=$row->group_name?></label>
					</td>            
					<td>
						<select name="item<?=$row->asset_group_id?>" id="item<?=$row->asset_group_id?>" >
							<option value="">-Asset Catagory-</option>
								<?php 
									$resitem = $db->get_results("SELECT distinct(item1),(id_item) FROM `category` WHERE asset_group_id='".$row->asset_group_id."'");
									foreach($resitem as $item): 
								?>       
									<option value="<?=$item->id_item?>" <?=($item->item1==$_POST['item']?'selected="selected"':'')?>> <?=$item->item1?> </option>  
																		
								<?php 
									endforeach;
								?>	
							<option value=0>Other</option>			
						</select>
							<p style="display:none;" class="othcat<?=$row->asset_group_id?>">Other: <input name="other_cat<?=$row->asset_group_id?>" type="text" value="" /></p>
					</td>            
					<td>
						<select name="merk<?=$row->asset_group_id?>" id="merk">
							<option value="">-Merk-</option>
								<?php 
									$resmerk = $db->get_results("SELECT distinct(merk) FROM `category` WHERE asset_group_id='".$row->asset_group_id."'");
									foreach($resmerk as $merk): ?>      
									<option value="<?=$merk->merk?>" <?=($merk->merk==$_POST['merk']?'selected="selected"':'')?>> <?=$merk->merk?> </option>                            
									<?php 
										endforeach;
									?>
							<option value=0>Other</option>		
						</select>
							<p style="display:none;" class="othmerk<?=$row->asset_group_id?>">Other: <input name="other_merk<?=$row->asset_group_id?>" type="text" value="" /></p>
					</td>
					<td>
						<select name="type<?=$row->asset_group_id?>" id="type">
							<option value="">-type-</option>
								<?php 
									$restype = $db->get_results("SELECT distinct(type) FROM `category` WHERE asset_group_id='".$row->asset_group_id."'");
									foreach($restype as $type): 
								?> 
									<option value="<?=$type->type?>" <?=($type->type==$_POST['merk']?'selected="selected"':'')?>> <?=$type->type?> </option>  
								  
								<?php 
									endforeach;
								?>
								<option value=0>Other</option>
						</select>
							<p style="display:none;" class="othtype<?=$row->asset_group_id?>">Other: <input type="text" name="other_type<?=$row->asset_group_id?>"></p>
					</td>
				</tr>
				<tr class="odd"> 
					<td>&nbsp;</td> 
					<td colspan="3"></td> 
				</tr>
				<tr> 
					<td>Satuan</td> 
					<td>
					<!--	<input name="sat<?=$row->asset_group_id?>" type="text" class="narr2" size="50" maxlength="50" />-->
                        <select name="sat<?=$row->asset_group_id?>">
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
                    <td style="display:none;" class="othsat<?=$row->asset_group_id?>"> Other: <input class="narr2" type="text" name="other_sat<?=$row->asset_group_id?>"></td>
					
                    <td style="display:none" colspan="2" class="hit1<?=$row->asset_group_id?>">Tarikan:
					<!--<td>-->
					<!--	<form method="post" action="" > -->
							<table border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"> 
								<tr style="display:none" class="hit1<?=$row->asset_group_id?>">
									<td width="20">Line:</td> 
									<td> </td>	
									<td width="20">lenght :</td>									
								</tr>
								<tr style="display:none" class="hit1<?=$row->asset_group_id?>">	
									<td><input type="text" size="20" name="lin<?=$row->asset_group_id?>"  class="narr"/> </td>  
									<td> x</td>	
									<td><input type="text" size="20" name="len<?=$row->asset_group_id?>" class="narr"/> </td>
									
								</tr>
							</table>
					<!--	</form> -->
					</td>
				</tr>
				<tr> 
                    <td>Quantity</td> 
					<td>
						<input type="text" name="quan<?=$row->asset_group_id?>" class="narr2" value="<?php $c?>"/>
					</td>
					<td colspan="2">&nbsp;</td> 
				</tr>
				<tr>
					<td>Note :</td> <td colspan="3"> <textarea name="note<?=$row->asset_group_id?>" cols="40" rows=""></textarea></td>
				</tr>
				<tr>
					<td colspan="4">&nbsp;</td>
				</tr>
		  
					<input type="hidden" value="0" id="theValue" />
					<script type="text/javascript">
						function loadtotal(pass)
						{
							
							var ni = document.getElementById('total_add'+pass);
							var numi = document.getElementById('theValue');
							var num = (document.getElementById('theValue').value -1)+ 2;

							numi.value = num;
							var newdiv = document.createElement('div');
							var divIdName = 'my'+num+'Div';
							newdiv.setAttribute('id',divIdName);
							// newdiv.innerHTML = 'Element Number '+num+' has been added! <a href=\'#\' onclick=\'removeElement('+divIdName+')\'>Remove the div "'+divIdName+'"</a>';
								
							ni.appendChild(newdiv);
							
							$("#"+divIdName).load('./total_add.php?add_total='+pass+'&a='+num);
							/*$("#"+divIdName).load('./total_add.php?add_total=' + $('#add_total').val()+"&a="+num);*/
							
							
						};
					</script>
				  
					
				<tr>  <!--<input type="checkbox" name="group" value="
					<?=$row->asset_group_id?>" id="add_total" />-->
					<td colspan="4">
						  <!--<input type="hidden" value="0" name="c_add" /> 
						  <input name="" type="button" id="add_total" onclick="loadtotal() c_add.value=1" value="1" /> -->
				  
						<button name="add" type="button" onclick="loadtotal(<?=$row->asset_group_id?>)"  id="add_total" value="<?=$row->asset_group_id?>">Add</button>
					
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<div id="total_add<?=$row->asset_group_id?>"></div>
					</td>
				</tr>
					<?php endforeach; ?> 
					<?php  endif;?>
			</table>
			   
		</td>
	</tr>

        	
</table>

<script language="javascript">
/*	
	function itemchange(objname)
	{
		var val = document.getElementById(objname).value;//$(this, "option:selected").val();
		var name = objname;//$(this).attr("name");
		var index;	
			
		index = name.substr(4);
		
		if(val == 0){
			$(".othcat"+index).fadeIn('slow');				
		} else {				
			$(".othcat"+index).hide();
		}
		
		if(val == "71" || val == "72" || val == "73" || val == "74" || val == "75"|| val == "76" || val == "77" || val == "78" || val == "118" || val == "133" || val == "25" || val == "56" || val == "21" || val == "26" || val == "40"){
			$(".hit1"+index).fadeIn('slow');				
		} else {				
			$(".hit1"+index).hide();
		}
		return false;
	}	
*/	
	$(function()
	   {
		$("input[name*=len]").blur(function(){			
			/*
				var index = name.substr(-1);
				var nilai = parseFloat($(this).val());
				nilai = isNaN(nilai) ? 0 : nilai;
			*/
			var name = $(this).attr("name");
			var index;
			/*
			if(name.length > 5) 
				index = name.substr(-2);
			else
				index = name.substr(-1);
			*/
			index = name.substr(3);
			
			
			var qty = parseFloat($(this).val());
			qty = $("input[name=lin"+index+"]").val() * $("input[name=len"+index+"]").val();
			
			$("input[name=quan"+index+"]").val(qty);
		});		 
		
		 
		$("select[name*=item]").change(function(){	
			var name = $(this).attr("name");
			var index;
			/*
			if(name.length > 5) 
				index = name.substr(-2);
				//alert(index);
			else
				index = name.substr(-1);
				//alert(index);
			*/
			index = name.substr(4);			
			
			var val = $(this, "option:selected").val();
			if(val == 0){
				$(".othcat"+index).fadeIn('slow');				
			} else {				
				$(".othcat"+index).hide();
			}
			return false;
		});
	
		$("select[name*=item]").change(function(){			
			var val = $(this, "option:selected").val();
			var name = $(this).attr("name");
			var index;
			/*
			if(name.length > 5) 
				index = name.substr(-2);
			else
				index = name.substr(-1);
			*/
				
			index = name.substr(4);
			
			if(val == "71" || val == "72" || val == "73" || val == "74" || val == "75"|| val == "76" || val == "77" || val == "78" || val == "118" || val == "133" || val == "25" || val == "56" || val == "21" || val == "26" || val == "40"){
				$(".hit1"+index).fadeIn('slow');				
			} else {				
				$(".hit1"+index).hide();
			}
			return false;
		});
		
		$("select[name*=merk]").change(function(){
			var name = $(this).attr("name");
			var index = name.substr(4);
			
			var val = $(this, "option:selected").val();
			if(val == 0)
			{ 
				//alert(index)
				$(".othmerk"+index).fadeIn('slow');				
			} else {				
				$(".othmerk"+index).hide();
			}
			return false;
		});
		$("select[name*=type]").change(function(){			
			var name = $(this).attr("name");
			var index;		
			
			index = name.substr(4);
			
			var val = $(this, "option:selected").val();
			if(val == 0){
				$(".othtype"+index).fadeIn('slow');				
			} else {				
				$(".othtype"+index).hide();
			}
			return false;
		});
		
		$("select[name*=sat]").change(function(){			
			var name = $(this).attr("name");
			var index = name.substr(3);
			
			var val = $(this, "option:selected").val();
			if(val == 0){
				$(".othsat"+index).fadeIn('slow');				
			} else {				
				$(".othsat"+index).hide();
			}
			return false;
		});
		
	});
</script>       