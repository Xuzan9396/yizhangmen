
<table class="table table-hover" style="table-layout:fixed; word-break: break-all; word-wrap: break-word;">
					<col align="left" />
  					<col align="left" />
  					<col align="right" />
					<tr id="son_class_tabfirst" style="text-align:center;">
						<th class="col-md-1">分类ID</th>
						<th class="col-md-4">分类名称</th>
						<th class="col-md-1">显示名称</th>
						<th class="col-md-1" style="text-align:center;">是否推荐</th>
						<th class="col-md-1" style="text-align:center;">是否显示</th>
						<th class="col-md-1">分组</th>
						<th class="col-md-1">排序</th>
						<th class="col-md-2">操作</th>
					</tr>
					<foreach name="list" item="val" key="k">
						<tr>
							<td>{$val.sere_id}</td>
							<td  onclick="tr_click(this)"id="{$val.sere_id}" url="{:U('Admin/ServiceHall/index')}" style="cursor:pointer;"><span class="glyphicon glyphicon-plus" style="color:lightblue;"></span> &nbsp;{$val.sere_name}
							</td>
							<td >{$val.sere_name}</td>
							<td style="text-align:center;cursor:pointer;"><img src="" width="20" height="20"></td>
							<td style="text-align:center;cursor:pointer;">
									    <input type="checkbox" id="blankCheckbox" value="option1">
							</td>
							<td ><input type="text" size="2" placeholder="0"></td>
							<td><input type="text" size="2" placeholder="50"></td>
							<td>
								<a href="__URL__/editDiplay?sav_id={$val.sere_id}">
									<button class="btn-info" >修改</button>
								</a>
								<button onclick="javascript:void( tr_del({$val.sere_id},this,'{:U('Admin/ServiceHall/del')}','{:U('Admin/ServiceHall/getData')}'))"  class="btn-danger">删除 </button>
							</td>
						</tr>
						<!--  -->
						<tr style="display:none;">
							<td>hello</td>
							<td>hello</td>
							<td>hello</td>
							<td>hello</td>
							<td>hello</td>
							<td>hello</td>
							<td>hello</td>
							<td>hello</td>
						</tr>
					</foreach>
				</table>
