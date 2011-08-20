<?php /* Smarty version Smarty-3.0.8, created on 2011-08-20 19:28:23
         compiled from "./templates/Menu/Menu.html" */ ?>
<?php /*%%SmartyHeaderCode:12088920804e500ad73ca8d8-53217843%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0f755b46f33e1859cfd4b54ee2c46facded07ccc' => 
    array (
      0 => './templates/Menu/Menu.html',
      1 => 1313868499,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12088920804e500ad73ca8d8-53217843',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<html>
<head>
</head>
<body dir="rtl">

<?php echo $_smarty_tpl->getVariable('SmartClientJs')->value;?>


<script type="text/javascript" charset="utf-8">

var dsMasterName = "<?php echo $_smarty_tpl->getVariable('dsMaster')->value;?>
"; 
var frmMasterName = "frm<?php echo $_smarty_tpl->getVariable('dsMaster')->value;?>
";
var MasterGridName = "<?php echo $_smarty_tpl->getVariable('dsMaster')->value;?>
Grid";


isc.RestDataSource.create({
	ID:dsMasterName,
	fields:[
	        {name:"id",type:"integer",primatyKey:true,hidden:true},
	        {name:"MenuTitle",type:"string",title:"عنوان منو"},
	        {name:"IconPath",type:"string",title:"مسیر آیکون"},
	        {name:"MenuItemCommand",type:"string",title:"دستور منو"},
	        {name:"ParentTitle",type:"string",title:"منوی پدر"},
	        {name:"MenuItemParentID", 
	        	editorType: "comboBox", 
	            optionDataSource: dsMasterName, 
	            displayField:"MenuTitle", valueField:"id",title:"منوی پدر"	
	        }
	        ],
	dataFormat:"json",
	        	
	fetchDataURL:"index.php/?phpmodule=<?php echo $_smarty_tpl->getVariable('currentFile')->value;?>
",
	addDataURL :"index.php?phpmodule=<?php echo $_smarty_tpl->getVariable('currentFile')->value;?>
",
	updateDataURL:"index.php?phpmodule=<?php echo $_smarty_tpl->getVariable('currentFile')->value;?>
",
	removeDataURL:"index.php?phpmodule=<?php echo $_smarty_tpl->getVariable('currentFile')->value;?>
"
			});

isc.ListGrid.create({
	showFilterEditor: true,
	allowFilterExpressions: true,
    ID: MasterGridName,
    width:"100%", height:"100%", alternateRecordStyles:true,
    autoFetchData: true,
    dataSource: dsMasterName,
    recordClick:"this.FillForm()",
    FillForm:function()
        {
			 var record = this.getSelectedRecord();
		     if (record == null) return ;
		   	 eval(frmMasterName).editRecord(record);
		     
        }

   });

isc.DynamicForm.create({
	ID:frmMasterName,
	dataSource:dsMasterName,
	numCols:2,
	useAllDataSourceFields:true,
	defaultLayoutAlign: "center"
	
});

var dsMaster = eval(dsMasterName);
var frmMaster = eval(frmMasterName);
var MasterGrid = eval(MasterGridName);
DisableForm(frmMaster);
isc.ToolStripButton.create({ID:"btnNew_Master",title:"جدید",  icon: "Images.php?Color=Orange&IconType=Icons&ActionType=Health",click:function(){EnableForm(frmMaster);btnSave_Master.enable();btnNew_Master.disable();btnEdit_Master.disable();btnDelete_Master.disable();frmMaster.editNewRecord();}});
isc.ToolStripButton.create({ID:"btnSave_Master",title:"ذخیره",  icon: "Images.php?Color=Orange&IconType=Icons&ActionType=Save",disabled:true,click:function(){DisableForm(frmMaster);SaveMaster();btnSave_Master.disable();btnNew_Master.enable();btnEdit_Master.enable();btnDelete_Master.enable();}});
isc.ToolStripButton.create({ID:"btnEdit_Master",title:"ویرایش",  icon: "Images.php?Color=Orange&IconType=Icons&ActionType=Pen",click:function(){EnableForm(frmMaster);btnSave_Master.enable();btnNew_Master.disable();btnEdit_Master.disable();btnDelete_Master.disable();}});
isc.ToolStripButton.create({ID:"btnDelete_Master",title:"حذف",  icon: "Images.php?Color=Orange&IconType=Icons&ActionType=Trash",click:function(){
	ShowDialog(
			'اخطار حذف',
			'آیا از حذف مورد انتخاب شده اطمینان دارید؟',
			'بله',
			'خیر',
			'DeleteMaster'
			);
}});
isc.ToolStripButton.create({ID:"btnCancel_Master",title:"انصراف",  icon: "Images.php?Color=Orange&IconType=Icons&ActionType=Cancel",click:function(){DisableForm(frmMaster);btnNew_Master.enable();btnEdit_Master.enable();btnSave_Master.disable();btnDelete_Master.enable();}});

isc.ToolStrip.create({
    width: "100%", 
    height:24, 
    ID:"ToolstripMaster",
    members: [btnNew_Master, "separator",
              btnEdit_Master, "separator",
              btnSave_Master,"separator", 
              btnDelete_Master,"separator", 
              btnCancel_Master]
});


isc.HLayout.create({
	ID:"con",
    width: "100%",
    height: "100%",
 	defaultLayoutAlign: "right",
    members: [
              isc.VLayout.create({
 				  defaultLayoutAlign: "right",
            	  showResizeBar:true,
                  Height:"100%",
                  width:"*",
                  members:[ToolstripMaster,	
                           frmMaster
						   ]
              }),
		     isc.VLayout.create({
			            width: "70%",
			            members: [MasterGrid ]
			       		 })
              ]
  });




function SaveMaster()
{
	if(frmMaster.isNewRecord ())
	frmMaster.saveData();
	else
	{
		
		dsMaster.updateData(frmMaster.getValues());
		//MasterGrid.selection.selectSingle(0);
	}
}
function DeleteMaster(ans)
{
	if(ans=='YES'){
	 var record = MasterGrid.getSelectedRecord();
     if (record == null) return ;
     MasterGrid.removeData(record);
     frmMaster.clearValues();
	}
}

function EnableForm(frm)
{
for(var i=0;i<frm.getFields().length;i++) frm.getFields()[i].enable();
}
function DisableForm(frm)
{
for(var i=0;i<frm.getFields().length;i++) frm.getFields()[i].disable();
}

function ShowDialog(Title,Message,Yes,No,afterclose)
{
	isc.Window.create({
		ID:"dlgQuest",
		height:100,
		width:300,
		canDragResize: true,
		autoCenter:true,
		isModal:true,
		autoSize:true,
		align:"right",
		headerControls : [ "closeButton",
							"minimizeButton", isc.Label.create({
											height: "100%",
											width: "100%",
											contents: Title,
											align:"center"
								        })
							
					 ],
		items:[
					isc.VLayout.create({
					    defaultLayoutAlign: "center",
					    width: "100%",
					    height: "100%",
					    layoutMargin: 6,
					    membersMargin: 6,
					    border: "1px",
					    align: "center",  // As promised!
					    members: [
					        isc.Label.create({
					            height: "100%",
					            width: "100%",
					            contents: Message,
					            align:"center"
					        }),
					       isc.HLayout.create({
							   	layoutMargin: 6,
							    membersMargin: 6,
							    border: "1px",
                                defaultLayoutAlign:"center",
							    members:[
											isc.Label.create({width:"*"}),
										    isc.Button.create({title:Yes,click:function(){dlgQuest.hide();eval(afterclose+'("YES")');}}),
										    isc.Button.create({title:No,click:function(){dlgQuest.hide();eval(afterclose+'("NO")');}}),
											isc.Label.create({width:"*"})
										    ]

					      })
					    ]
					})
		 		]
		
	});
}

</script>



</body>
</html>
