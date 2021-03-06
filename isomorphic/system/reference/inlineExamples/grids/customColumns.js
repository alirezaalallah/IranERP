
var ds = isc.DataSource.get("countryDS");

isc.VLayout.create({
	ID:"layout",
	width:500, height:250,
	members: [
		isc.HLayout.create({
			ID:"buttonLayout",
			width:"*", height:30,
			membersMargin: 10,
			members: [
				isc.IButton.create({
				    ID: "formulaButton",
				    autoFit: true,
				    title: "Show Formula Builder",
				    click: "countryList.addFormulaField();"
				}),
				isc.IButton.create({
				    ID: "summaryButton",
				    autoFit: true,
				    title: "Show Summary Builder",
				    click: "countryList.addSummaryField();"
				}),
				isc.IButton.create({
				    ID: "stateButton",
				    autoFit: true,
				    title: "Recreate from State",
				    click: function () {
				        var state = countryList.getFieldState(true);
						countryList.destroy();
						recreateListGrid();
				        countryList.setFieldState(state);
				    }
				})
			]
		})
	]
});

recreateListGrid();

function recreateListGrid() {
	layout.addMember(isc.ListGrid.create({
	    ID: "countryList",
	    width:"100%", height:"*",
	    alternateRecordStyles:true, cellHeight:22,
	    dataSource: ds,
	    autoFetchData: true,
	    canAddFormulaFields: true,
	    canAddSummaryFields: true,
	    fields:[
	        {name:"countryCode", title:"Flag", width:50, type:"image", imageURLPrefix:"flags/16/", 
	            imageURLSuffix:".png"
	        },
	        {name:"countryName", title:"Country"},
	        {name:"capital", title:"Capital"},
	        {name:"population", title:"Population", formatCellValue:"isc.Format.toUSString(value)"},
	        {name:"area", title:"Area (km&sup2;)", formatCellValue:"isc.Format.toUSString(value)"},
	        {name:"gdp", formatCellValue:"isc.Format.toUSString(value)"}
	    ]
	}));

}
