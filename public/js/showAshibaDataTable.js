function LoadData(){
        console.log("LoadData");
        $("#showTable").empty();
         var filterId = [];
         var selectedAllDbId = viewer.getSelection();
         for(var i =0; i<selectedAllDbId.length;i++){
              if(filterId.includes(selectedAllDbId[i])){
                continue;
              }else{
                filterId.push(selectedAllDbId[i]);
              }
            }
         console.log("Selected Ashiba" + JSON.stringify(filterId,null,4));
         
         var filterList = ["attributeName"];
         viewer.model.getBulkProperties2(filterId,filterList,
         
         function(propertiesList){
             let familyNameList = [];
             let typeNameList   = [];
             let sugikoCodeList = [];
             let sugikoNameList = [];
             let sugikoUnitWeightList = { };
             let quantity = {};
             
             $.each(propertiesList, function(i, properties){
                let familyName = properties["name"];
                var n = familyName.indexOf("[");
                let fname = familyName.slice(0,n);
                let property = properties["properties"];
                let typeName;
                let unitWeight;
                let sugikoCode;
                let sugikoName;
                for(var i in property){
                    var displayName = property[i].displayName;
                    if(displayName == "タイプ名"){
                        typeName = property[i].displayValue;
                        
                    }
                    if(displayName == "S_商品コード"){
                        sugikoCode = property[i].displayValue;
                        
                    }
                    if(displayName == "S_商品名"){
                        sugikoName = property[i].displayValue;
                        if(!sugikoNameList.includes(sugikoName) || sugikoNameList.length == 0){
                            familyNameList.push(fname);
                            typeNameList.push(typeName);
                            sugikoCodeList.push(sugikoCode);
                            sugikoNameList.push(sugikoName);
                            quantity[sugikoName] = 1;
                        }else{
                            
                            if(Object.keys(quantity).includes(sugikoName)){
                               quantity[sugikoName]++;
                           }
                        }
                   
                    }
                    if(displayName == "S_質量(kg)"){
                        unitWeight = property[i].displayValue;
                        console.log("Weight" + unitWeight );
                        if(!Object.keys(sugikoUnitWeightList).includes(sugikoName)){
                            sugikoUnitWeightList[sugikoName] = unitWeight;
                        }
                    }
                    
                    
                    
                }
                
             });
             console.log("FamilyNameList:" + JSON.stringify(familyNameList,null,3));
             console.log("TypeNameList:" + JSON.stringify(typeNameList,null,3));
             console.log("SugikoNameList:" + JSON.stringify(sugikoNameList,null,3));
             console.log("Quantity:" + JSON.stringify(quantity,null,3));
             console.log("Keys" + JSON.stringify(Object.keys(quantity)));
             console.log("Weight List" + JSON.stringify(sugikoUnitWeightList,null,3));
             
             //Display Data in table
             var totalWeight = 0;
             for(var i in familyNameList){
                var sugikoName = sugikoNameList[i];
                var row = "<tr><td>" +  familyNameList[i]   + "</td>"+
                            "<td>" +  typeNameList[i]   + "</td>"+
                            "<td>" +   sugikoCodeList[i]  + "</td>"+
                            "<td>" +   sugikoNameList[i]   + "</td>"+
                            "<td>" +   quantity[sugikoName]   + "</td>"+
                            "<td style='text-align: right'>" +   sugikoUnitWeightList[sugikoName]   + "</td>"+
                            "<td style='text-align: right'>" +   (quantity[sugikoName] * sugikoUnitWeightList[sugikoName]).toFixed(2)   + "</td>"+
                          "</tr>";
               console.log("Final Result"+row);
                          
                $("#showTable").append(row);
                totalWeight+= quantity[sugikoName] * sugikoUnitWeightList[sugikoName];
             }
             
             var row = "<tr><td colspan=6 style='text-align: right'> Total Weight of 足場 : </td>" + 
                        "<td  style='text-align: right'>"  +  totalWeight.toFixed(2)  + "</td><tr>";
             
             $(row).insertBefore($("#showTable tr:first"));
             
             
             
         });
         
        
    }
    function SegregateData(){
        //alert("SegregateData");
        var sel = viewer.getSelection();
        var filterList = ["attributeName"];
        viewer.model.getBulkProperties2(sel,filterList,
            function(e){
                $.each(e, function(i,propertyList){
                    let properties = propertyList["properties"];
                    for(var i in properties){
                    
                        var displayName = properties[i].displayName;
                    
                        if(displayName == "コメント"){
                            alert("hello");
                        }
                    
                    }
                });
                
                
            });
    }