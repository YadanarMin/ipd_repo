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
         console.log("Selected Kasetsu" + JSON.stringify(filterId,null,4));
         
         var filterList = ["attributeName"];
         viewer.model.getBulkProperties2(filterId,filterList,
         
         function(propertiesList){
             let familyNameList = [];
             let typeNameList   = [];
             let lenghtList = [];
             let UnitWeightList = { };
             let quantity = {};
            
             $.each(propertiesList, function(i, properties){
                let familyName = properties["name"];
                var n = familyName.indexOf("[");
                let fname = familyName.slice(0,n);
                let property = properties["properties"];
                let typeName;
                let length;
                let unitWeight;
                let identifier;
               
                for(var i in property){
                    var displayName = property[i].displayName;
                    
                    if(displayName.includes("長さ")){
                        length = Math.round(property[i].displayValue);
                    }
                    
                    if(displayName == "タイプ名"){
                        typeName = property[i].displayValue;
                    }
                    
                    if(displayName == "質量"){
                        unitWeight = property[i].displayValue;
                        
                            if(length){
                                identifier= fname+typeName+length;
                                if(!Object.keys(quantity).includes(identifier)){
                                    familyNameList.push(fname);
                                    typeNameList.push(typeName);
                                    lenghtList.push(length);
                                    quantity[identifier] = 1;
                                    UnitWeightList[identifier] = unitWeight;
                                }else{
                                    quantity[identifier]++;
                                }
                            }else{
                                identifier = fname+typeName+"Undefined";
                                if(!Object.keys(quantity).includes(identifier)){
                                    familyNameList.push(fname);
                                    typeNameList.push(typeName);
                                    lenghtList.push("Undefined");
                                    quantity[identifier] = 1;
                                    UnitWeightList[identifier] = unitWeight;
                                }else{
                                    quantity[identifier]++;
                                }
                                
                            }
                    }
                    
                }
                
             });
             console.log("FamilyNameList:" + JSON.stringify(familyNameList,null,3));
             console.log("TypeNameList:" + JSON.stringify(typeNameList,null,3));
             console.log("LenghtList:" + JSON.stringify(lenghtList,null,3));
             console.log("Quantity:" + JSON.stringify(quantity,null,3));
             console.log("WeightList:" + JSON.stringify(UnitWeightList,null,3));
             
             
             
             //Display Data in table
             var totalWeight = 0;
             for(var i in familyNameList){
                var identifier = familyNameList[i]+typeNameList[i]+lenghtList[i];
                var row = "<tr><td>" +  familyNameList[i]   + "</td>"+
                            "<td>" +  typeNameList[i]   + "</td>"+
                            "<td>" +  lenghtList[i]   + "</td>"+
                            "<td>" +  quantity[identifier]   + "</td>"+
                            "<td>" +   UnitWeightList[identifier].toFixed(2)  + "</td>"+
                            "<td style='text-align: right'>" +  (quantity[identifier] * UnitWeightList[identifier]).toFixed(2)   + "</td>"+
                          "</tr>";
              console.log("Final Result"+row);
                          
                $("#showTable").append(row);
                totalWeight+= quantity[identifier] * UnitWeightList[identifier];
             }
             
             var row = "<tr><td colspan=5 style='text-align: right'> Total Weight of 重仮設 : </td>" + 
                         "<td  style='text-align: right'>"  +  totalWeight.toFixed(2)  + "</td><tr>";
             
             $(row).insertBefore($("#showTable tr:first"));
         });
         
        
    }
    