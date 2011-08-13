isc.ListGrid.create({
    ID: "countryList",
    width:550, height:224, alternateRecordStyles:true, cellHeight:22,
    dataSource: countryDS,
    fields:[
        {name:"countryCode", title:"Flag", width:40, type:"image", imageURLPrefix:"flags/16/", imageURLSuffix:".png", canEdit:false},
        {name:"countryName",
            canEdit:false
        },
        {name:"continent"},
        {name:"member_g8",
            canEdit:false
        },
        {name:"population", formatCellValue:"isc.Format.toUSString(value);"},
        {name:"independence"}
    ],
    autoFetchData: true,
    canEdit: true,
    editEvent: "click"
})
